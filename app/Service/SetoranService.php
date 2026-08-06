<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Setoran;
use Carbon\Carbon;

class SetoranService
{
    // === Jam buka input per shift (silakan sesuaikan) ===
    private const SHIFT_OPEN_TIME = [
        'PAGI' => '14:30',
        'SORE' => '17:00',
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

            if ($isToday) {
                $modify .= '
                    <button type="button" class="btn btn-sm bg-warning-light btn-edit-setoran"
                        data-id="' . $record->id . '"
                        data-setoran="' . e($record->setoran) . '"
                        data-shift="' . e($record->shift) . '">
                        <i class="fa fa-edit"></i>
                    </button>
                ';
            }

            if ($record->status === 'SELESAI' && self::canPrint($record, $currentUser)) {
                $modify .= '
                    <button type="button" onclick="cetakStruk(' . $record->id . ')" class="btn btn-sm btn-primary">
                        <i class="fa fa-print"></i>
                    </button>
                ';
            }

            // tombol delete - disiapkan tapi disembunyikan dulu
            // $modify .= '<button type="button" class="btn btn-sm btn-danger btn-delete-setoran" data-id="' . $record->id . '"><i class="fa fa-trash"></i></button>';

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
     * Status ketersediaan input untuk tiap shift hari ini.
     * Dipakai untuk notifikasi & untuk enable/disable opsi shift di form.
     */
    public static function getShiftStatus(): array
    {
        $now = Carbon::now();
        $result = [];

        foreach (self::SHIFT_OPEN_TIME as $shift => $openTime) {
            $openAt = $now->copy()->setTimeFromTimeString($openTime);

            $alreadyInput = Setoran::today()->where('shift', $shift)->exists();
            $isOpen       = $now->gte($openAt);

            $result[$shift] = [
                'open_at'       => $openAt->format('H:i'),
                'is_open'       => $isOpen,
                'already_input' => $alreadyInput,
                'can_input'     => $isOpen && ! $alreadyInput,
            ];
        }

        return $result;
    }

    public static function store(array $data, int $userId): Setoran
    {
        $shift = strtoupper($data['shift'] ?? '');

        if (! array_key_exists($shift, self::SHIFT_OPEN_TIME)) {
            throw new \InvalidArgumentException('Shift tidak valid.');
        }

        $now    = Carbon::now();
        $openAt = $now->copy()->setTimeFromTimeString(self::SHIFT_OPEN_TIME[$shift]);

        if ($now->lt($openAt)) {
            throw new \RuntimeException(
                "Input setoran shift {$shift} baru bisa dilakukan mulai pukul {$openAt->format('H:i')}."
            );
        }

        if (Setoran::today()->where('shift', $shift)->exists()) {
            throw new \RuntimeException("Setoran untuk shift {$shift} hari ini sudah diinput.");
        }

        $totalTunai = self::getTotalTunaiCustomer($userId, $now);
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

        if (! array_key_exists($shift, self::SHIFT_OPEN_TIME)) {
            throw new \InvalidArgumentException('Shift tidak valid.');
        }

        if ($shift !== $setoran->shift) {
            $conflict = Setoran::today()
                ->where('shift', $shift)
                ->where('id', '!=', $setoran->id)
                ->exists();

            if ($conflict) {
                throw new \RuntimeException("Setoran untuk shift {$shift} hari ini sudah diinput.");
            }
        }

        $totalTunai = self::getTotalTunaiCustomer($setoran->user_id, $setoran->tanggal);
        $selisih    = (int) $data['setoran'] - $totalTunai;

        $setoran->update([
            'setoran'                => $data['setoran'],
            'shift'                  => $shift,
            'total_tunai_customer'   => $totalTunai,
            'selisih'                => $selisih,
        ]);

        return $setoran;
    }

    private static function getTotalTunaiCustomer(int $userId, Carbon $date): int
    {
        $customers = Customer::with('transaksiObat')
            ->where('metode_bayar', 'TUNAI')
            ->where('updated_by', $userId)
            ->whereDate('updated_at', $date->toDateString())
            ->get();

        return (int) $customers->sum(fn(Customer $c) => $c->total_biaya);
    }

    public static function canPrint(Setoran $setoran, $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($setoran->user_id === $user->id) {
            return true;
        }

        return in_array(strtolower($user->role ?? ''), self::PRINT_ALLOWED_ROLES);
    }
}
