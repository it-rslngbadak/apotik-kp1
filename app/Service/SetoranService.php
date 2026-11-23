<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Setoran;
use Carbon\Carbon;

class SetoranService
{
    // === Jam buka input setoran per jenis hari ===
    // Urutan shift = urutan wajib setor (SORE baru bisa setor setelah PAGI setor).
    private const JADWAL_SETORAN = [
        'WEEKDAY' => [ // Senin–Jumat: 2 shift
            'PAGI' => '14:30',
            'SORE' => '21:30',
        ],
        'WEEKEND' => [ // Sabtu–Minggu: 1 shift saja (apotek buka 08:00–16:00)
            'FULL' => '15:30',
        ],
    ];

    private const LABEL_HARI = [
        'WEEKDAY' => 'Senin–Jumat',
        'WEEKEND' => 'Sabtu–Minggu',
    ];

    // Range jam lama. HANYA dipakai untuk data setoran lama di mana 1 user setor lebih dari
    // sekali di hari yang sama (sebelum aturan 1 user = 1 setoran per hari berlaku),
    // supaya cetak ulang laporan lama tidak berubah.
    private const LEGACY_SHIFT_RANGE = [
        'PAGI' => ['07:00:00', '14:29:59'],
        'SORE' => ['14:30:00', '22:10:00'],
    ];

    private const PRINT_ALLOWED_ROLES = ['Admin', 'Super Admin'];

    public static function getSetoranData($request)
    {
        $draw            = $request->get('draw');
        $start           = $request->get('start');
        $rowPerPage      = $request->get('length');
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        $columnIndex     = $columnIndex_arr[0]['column'] ?? null;
        $columnName      = $columnIndex !== null ? $columnName_arr[$columnIndex]['data'] : null;
        $columnSortOrder = $order_arr[0]['dir'] ?? 'asc';
        $searchValue     = strtoupper($search_arr['value'] ?? '');

        $searchFilter = function ($query) use ($searchValue) {
            $query->whereRaw('UPPER(shift) like ?', ['%' . $searchValue . '%']);
        };

        $dari_tgl = $request->get('dari_tgl')
            ? Carbon::createFromFormat('d-m-Y', $request->get('dari_tgl'))->startOfDay()
            : Carbon::now()->subDays(90)->startOfDay();

        $sampai_tgl = $request->get('sampai_tgl')
            ? Carbon::createFromFormat('d-m-Y', $request->get('sampai_tgl'))->endOfDay()
            : Carbon::now();

        $baseQuery = Setoran::with('user')
            ->where('tanggal', '>=', $dari_tgl)
            ->where('tanggal', '<=', $sampai_tgl);

        $totalRecords = Setoran::count();

        $totalRecordsWithFilter = (clone $baseQuery)->where($searchFilter)->count();

        $query = (clone $baseQuery)
            ->orderBy('tanggal', 'DESC')
            ->where($searchFilter);

        if ($columnName) {
            $query->orderBy($columnName, $columnSortOrder);
        }

        $records = $query->skip($start)->take($rowPerPage)->get();

        $data_arr = [];
        $currentUser = auth()->user();

        foreach ($records as $record) {
            $isToday = $record->tanggal->isToday();

            $modify = '';

            // if ($isToday) {
            //     $modify .= '
            //         <button type="button" class="btn btn-sm bg-warning-light btn-edit-setoran"
            //             data-id="' . $record->id . '"
            //             data-setoran="' . e($record->setoran) . '"
            //             data-shift="' . e($record->shift) . '">
            //             <i class="fa fa-edit"></i>
            //         </button>
            //     ';
            // }

            if ($record->status === 'SELESAI' && self::canPrint($record, $currentUser)) {
                $modify .= '
                    <button type="button" onclick="cetakStruk(' . $record->id . ')" class="btn btn-sm btn-primary">
                        <i class="fa fa-print"></i>
                    </button>
                ';
            }

            // tombol delete - hanya untuk admin
            if (self::isAdmin($currentUser)) {
                $modify .= '
                    <button type="button" class="btn btn-sm btn-danger btn-delete-setoran" data-id="' . $record->id . '">
                        <i class="fa fa-trash"></i>
                    </button>
                ';
            }

            $selisihValue = (int) ($record->selisih ?? 0);

            $selisihBadge = match (true) {
                $selisihValue === 0 => '<span class="badge badge-success">Rp 0</span>',
                $selisihValue > 0   => '<span class="badge badge-info">+Rp ' . number_format($selisihValue, 0, ',', '.') . '</span>',
                default             => '<span class="badge badge-danger">-Rp ' . number_format(abs($selisihValue), 0, ',', '.') . '</span>',
            };

            $data_arr[] = [
                'tanggal' => $record->tanggal->translatedFormat('d M Y H:i'),
                'setoran' => 'Rp ' . number_format($record->setoran, 0, ',', '.'),
                'shift'   => $record->shift,
                'kasir'   => $record->user->name ?? '-',
                'selisih'   => $selisihBadge,
                'modify'  => $modify,
            ];
        }

        return [
            'draw'            => intval($draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalRecordsWithFilter,
            'data'            => $data_arr,
        ];
    }

    /**
     * Semua shift yang dikenal => label hari berlakunya. Dipakai untuk validasi & opsi form.
     */
    public static function daftarShift(): array
    {
        $daftar = [];

        foreach (self::JADWAL_SETORAN as $jenisHari => $jadwal) {
            foreach (array_keys($jadwal) as $shift) {
                $daftar[$shift] = self::LABEL_HARI[$jenisHari];
            }
        }

        return $daftar;
    }

    /**
     * Shift yang berlaku pada tanggal tersebut => jam buka input setoran.
     */
    public static function jadwalShift(Carbon $date): array
    {
        return self::JADWAL_SETORAN[self::jenisHari($date)];
    }

    /**
     * Setoran milik user pada tanggal tertentu. Satu user hanya boleh 1 setoran per hari,
     * dan user yang sudah setor hari ini tidak boleh bertransaksi lagi di kasir.
     */
    public static function setoranUser(int $userId, Carbon $date): ?Setoran
    {
        return Setoran::where('user_id', $userId)
            ->whereDate('tanggal', $date->toDateString())
            ->first();
    }

    /**
     * Status ketersediaan input untuk tiap shift hari ini + status setoran user yang login.
     * Dipakai untuk notifikasi & untuk enable/disable opsi shift di form.
     */
    public static function getShiftStatus(int $userId): array
    {
        $now            = Carbon::now();
        $setoranHariIni = Setoran::with('user')->today()->get()->keyBy('shift');
        $shifts         = [];

        foreach (self::jadwalShift($now) as $shift => $openTime) {
            $openAt   = $now->copy()->setTimeFromTimeString($openTime);
            $setoran  = $setoranHariIni->get($shift);
            $wajib    = self::shiftSebelumnya($shift, $now);
            $menunggu = ($wajib && ! $setoranHariIni->has($wajib)) ? $wajib : null;
            $isOpen   = $now->gte($openAt);

            $shifts[$shift] = [
                'open_at'       => $openAt->format('H:i'),
                'is_open'       => $isOpen,
                'already_input' => (bool) $setoran,
                'kasir'         => $setoran?->user?->name,
                'menunggu'      => $menunggu,
                'can_input'     => $isOpen && ! $setoran && ! $menunggu,
            ];
        }

        $milikUser = self::setoranUser($userId, $now);

        return [
            'hari'         => self::LABEL_HARI[self::jenisHari($now)],
            'shifts'       => $shifts,
            'setoran_user' => $milikUser
                ? ['shift' => $milikUser->shift, 'jam' => $milikUser->tanggal->format('H:i')]
                : null,
        ];
    }

    public static function store(array $data, int $userId): Setoran
    {
        $shift = strtoupper($data['shift'] ?? '');
        $now   = Carbon::now();

        self::pastikanShiftBerlaku($shift, $now);

        $openAt = $now->copy()->setTimeFromTimeString(self::jadwalShift($now)[$shift]);

        if ($now->lt($openAt)) {
            throw new \RuntimeException(
                "Input setoran shift {$shift} baru bisa dilakukan mulai pukul {$openAt->format('H:i')}."
            );
        }

        if ($milikUser = self::setoranUser($userId, $now)) {
            throw new \RuntimeException(
                "Anda sudah setor shift {$milikUser->shift} hari ini. Setoran shift berikutnya harus menggunakan user kasir lain."
            );
        }

        if (Setoran::today()->where('shift', $shift)->exists()) {
            throw new \RuntimeException("Setoran untuk shift {$shift} hari ini sudah diinput.");
        }

        $wajib = self::shiftSebelumnya($shift, $now);

        if ($wajib && ! Setoran::today()->where('shift', $wajib)->exists()) {
            throw new \RuntimeException(
                "Setoran shift {$wajib} hari ini belum diinput. Setoran shift {$shift} baru bisa dilakukan setelah setoran shift {$wajib}."
            );
        }

        $totalTunai = self::getTotalTunaiCustomer($userId, $now, $shift);
        $selisih    = (int) $data['setoran'] - $totalTunai;

        return Setoran::create([
            'user_id'               => $userId,
            'setoran'                => $data['setoran'],
            'shift'                  => $shift,
            'tanggal'                => $now,
            'status'                 => 'SELESAI',
            'total_tunai_customer'   => $totalTunai,
            'selisih'                => $selisih,
        ]);
    }

    public static function update(Setoran $setoran, array $data): Setoran
    {
        if (! $setoran->tanggal->isToday()) {
            throw new \RuntimeException('Setoran hanya bisa diubah pada hari yang sama saat dibuat.');
        }

        $shift = strtoupper($data['shift'] ?? '');

        self::pastikanShiftBerlaku($shift, $setoran->tanggal);

        if ($shift !== $setoran->shift) {
            $conflict = Setoran::today()
                ->where('shift', $shift)
                ->where('id', '!=', $setoran->id)
                ->exists();

            if ($conflict) {
                throw new \RuntimeException("Setoran untuk shift {$shift} hari ini sudah diinput.");
            }
        }

        $totalTunai = self::getTotalTunaiCustomer($setoran->user_id, $setoran->tanggal, $shift);
        $selisih    = (int) $data['setoran'] - $totalTunai;

        $setoran->update([
            'setoran'                => $data['setoran'],
            'shift'                  => $shift,
            'total_tunai_customer'   => $totalTunai,
            'selisih'                => $selisih,
        ]);

        return $setoran;
    }

    /**
     * Input setoran custom (khusus admin): tanggal & shift bebas dipilih, tanpa batasan
     * jam buka maupun urutan shift. Tetap wajib: shift sesuai jenis hari, dan 1 user
     * hanya 1 setoran per hari (kalau tidak, transaksi hari itu terhitung dua kali).
     */
    public static function storeCustom(array $data, int $userId): Setoran
    {
        $shift   = strtoupper($data['shift'] ?? '');
        $tanggal = Carbon::createFromFormat('d-m-Y', $data['tanggal']);

        self::pastikanShiftBerlaku($shift, $tanggal);

        if ($milikUser = self::setoranUser($userId, $tanggal)) {
            throw new \RuntimeException(
                "Kasir ini sudah punya setoran shift {$milikUser->shift} pada tanggal {$tanggal->format('d-m-Y')}. Satu user hanya boleh 1 setoran per hari."
            );
        }

        $tanggal->setTimeFromTimeString(self::jadwalShift($tanggal)[$shift]);

        $totalTunai = self::getTotalTunaiCustomer($userId, $tanggal, $shift);
        $selisih    = (int) $data['setoran'] - $totalTunai;

        return Setoran::create([
            'user_id'               => $userId,
            'setoran'                => $data['setoran'],
            'shift'                  => $shift,
            'tanggal'                => $tanggal,
            'status'                 => 'SELESAI',
            'total_tunai_customer'   => $totalTunai,
            'selisih'                => $selisih,
        ]);
    }

    public static function delete(Setoran $setoran): void
    {
        $setoran->delete();
    }

    public static function isAdmin($user): bool
    {
        return $user && in_array($user->role_name, self::PRINT_ALLOWED_ROLES);
    }

    /**
     * Periode [mulai, selesai] transaksi kasir yang dihitung untuk setoran:
     * 1 hari penuh milik user tsb (user sudah tidak bisa bertransaksi setelah setor).
     */
    public static function getPeriode(int $userId, Carbon $date, string $shift): array
    {
        $shift = strtoupper($shift);

        $jumlahSetoranUser = Setoran::where('user_id', $userId)
            ->whereDate('tanggal', $date->toDateString())
            ->count();

        if ($jumlahSetoranUser > 1 && isset(self::LEGACY_SHIFT_RANGE[$shift])) {
            [$start, $end] = self::LEGACY_SHIFT_RANGE[$shift];

            return [
                $date->copy()->setTimeFromTimeString($start),
                $date->copy()->setTimeFromTimeString($end),
            ];
        }

        return [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
    }

    private static function getTotalTunaiCustomer(int $userId, Carbon $date, string $shift): int
    {
        $customers = Customer::with('transaksiObat')
            ->where('status', 'SELESAI')
            ->where('metode_bayar', 'TUNAI')
            ->where('updated_by', $userId)
            ->whereBetween('tanggal_registrasi', self::getPeriode($userId, $date, $shift))
            ->get();

        return (int) $customers->sum(fn(Customer $c) => $c->total_biaya);
    }

    private static function jenisHari(Carbon $date): string
    {
        return $date->isWeekend() ? 'WEEKEND' : 'WEEKDAY';
    }

    /**
     * Shift yang wajib sudah setor sebelum shift ini (null kalau shift pertama).
     */
    private static function shiftSebelumnya(string $shift, Carbon $date): ?string
    {
        $urutan = array_keys(self::jadwalShift($date));
        $index  = array_search($shift, $urutan, true);

        return ($index !== false && $index > 0) ? $urutan[$index - 1] : null;
    }

    private static function pastikanShiftBerlaku(string $shift, Carbon $date): void
    {
        $jadwal = self::jadwalShift($date);

        if (! array_key_exists($shift, $jadwal)) {
            throw new \InvalidArgumentException(sprintf(
                'Shift %s tidak berlaku pada tanggal %s (%s). Shift yang tersedia: %s.',
                $shift ?: '-',
                $date->format('d-m-Y'),
                self::LABEL_HARI[self::jenisHari($date)],
                implode(', ', array_keys($jadwal))
            ));
        }
    }

    public static function canPrint(Setoran $setoran, $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($setoran->user_id === $user->id || $user->role_name === 'Super Admin') {
            return true;
        }

        return in_array(strtolower($user->role ?? ''), self::PRINT_ALLOWED_ROLES);
    }
}
