<?php

namespace App\Service;

use App\Models\Coa;
use App\Models\Customer;
use App\Models\KodeTransaksi;
use App\Models\Simrs\FarmalkesSimrs;
use App\Models\Simrs\RegSimrs;
use App\Models\Simrs\TransaksiResepSimrs;
use App\Models\TransaksiObat;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    public static function getCustomerData($request)
    {
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        $columnIndex     = $columnIndex_arr[0]['column'] ?? null;
        $columnName      = $columnIndex !== null ? $columnName_arr[$columnIndex]['data'] : null;
        $columnSortOrder = $order_arr[0]['dir'] ?? 'asc';
        $searchValue     = strtoupper($search_arr['value']);

        // ✅ Closure filter reusable
        $searchFilter = function ($query) use ($searchValue) {
            $query->whereRaw('UPPER(no_registrasi) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(no_hp) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(nama_customer) like ?', ['%' . $searchValue . '%']);
        };

        // ✅ Fix: parse format 'd-M-Y' sesuai output datetimepicker (contoh: 29-May-2025)
        $dari_tgl = $request->get('dari_tgl')
            ? \Carbon\Carbon::createFromFormat('d-m-Y', $request->get('dari_tgl'))->startOfDay()
            : \Carbon\Carbon::now()->subDays(90)->startOfDay();

        $sampai_tgl = $request->get('sampai_tgl')
            ? \Carbon\Carbon::createFromFormat('d-m-Y', $request->get('sampai_tgl'))->endOfDay()
            : \Carbon\Carbon::now();

        // ✅ Base query
        $baseQuery = Customer::with('transaksiObat')
            ->where('tanggal_registrasi', '>=', $dari_tgl)
            ->where('tanggal_registrasi', '<=', $sampai_tgl);

        $totalRecords = Customer::count();

        $totalRecordsWithFilter = (clone $baseQuery)
            ->where($searchFilter)
            ->count();

        $query = (clone $baseQuery)
            ->orderBy('tanggal_registrasi', 'DESC')
            ->where($searchFilter);

        if ($columnName) {
            $query->orderBy($columnName, $columnSortOrder);
        }

        $records = $query
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {
            $itemsJson = $record->transaksiObat->map(function ($item) {
                return [
                    'nama_obat'   => $item->nama_obat,
                    'jumlah'      => $item->jumlah,
                    'harga_jual'  => $item->harga_jual,
                    'sub_total'    => $item->sub_total,
                ];
            });
            $modify = '
                <button type="button" class="btn btn-sm bg-warning-light btn-edit-customer"
                    data-id="' . $record->id . '"
                    data-no_registrasi="' . e($record->no_registrasi) . '"
                    data-tanggal_registrasi="' . e($record->tanggal_registrasi) . '"
                    data-nama_customer="' . e($record->nama_customer) . '"
                    data-no_hp="' . e($record->no_hp) . '"
                    data-metode_bayar="' . e($record->metode_bayar) . '"
                    data-total="' . e($record->total_biaya) . '"
                    data-uang_tunai="' . e($record->uang_tunai) . '"
                    data-kembalian="' . e($record->kembalian) . '"
                    data-items="' . e($itemsJson->toJson()) . '">
                    <i class="fa fa-edit"></i>
                </button>
            ';

            // tombol cetak struk HANYA muncul kalau status "Selesai"
            if ($record->status === 'SELESAI') {
                $modify .= '
                    <button type="button" onclick="cetakStruk(' . e($record->id) . ')" class="btn btn-primary">
                        🖨️ Cetak Struk
                    </button>
                ';
            }
            $statusBadge = match (strtoupper($record->status)) {
                'DILAYANI' => '<span class="badge badge-warning">DILAYANI</span>',
                'SELESAI'  => '<span class="badge badge-success">SELESAI</span>',
                default    => '<span class="badge badge-secondary">' . e($record->status) . '</span>',
            };

            $data_arr[] = [
                "no_registrasi" => $record->no_registrasi,
                "tanggal_registrasi" => \Carbon\Carbon::parse($record->tanggal_registrasi)
                    ->translatedFormat('d M Y H:i'),
                "no_hp" => $record->no_hp,
                "nama_customer" => $record->nama_customer,
                "metode_bayar" => $record->metode_bayar,
                "status"  => $statusBadge,
                // "harga_satuan"  => number_format($record->harga_satuan, 0, ',', '.'),
                "modify"       => $modify,
            ];
        }

        return [
            "draw"            => intval($draw),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecordsWithFilter,
            "data"            => $data_arr,
        ];
    }

    public static function reload()
    {
        DB::beginTransaction();
        try {
            $latestCustomer = Customer::orderBy('tanggal_registrasi', 'desc')->pluck('no_registrasi')->toArray();

            $query = RegSimrs::select(['reg_no', 'tanggal_registrasi', 'no_mr'])
                ->where('no_mr', 'B0001')
                ->whereRaw("DATE(tanggal_registrasi) = ?", [
                    now()->format('Y-m-d')
                ]);

            if ($latestCustomer) {
                $query->whereNotIn('reg_no', $latestCustomer);
            }

            $latestRegs = $query->get(); // <-- wajib get()
            if ($latestRegs->isEmpty()) {
                return [
                    'status' => 'failed',
                    'message' => 'Tidak ada data customers terbaru',
                ];
            }
            foreach ($latestRegs as $reg) {
                $customer = Customer::firstOrCreate(
                    [
                        'no_registrasi'      => $reg->reg_no,
                    ],
                    [
                        'tanggal_registrasi' => $reg->tanggal_registrasi,
                        'status'             => 'DILAYANI',
                    ]
                );

                if ($customer->wasRecentlyCreated) {
                    $resepByReg = TransaksiResepSimrs::select(['farmalkes_id', 'jumlah_dijual', 'HNA', 'harga_jual'])->with('farmalkes')
                        ->where('regnum', $reg->reg_no)
                        ->get();
                    foreach ($resepByReg as $resep) {
                        $farmalkes = FarmalkesSimrs::select(['harga_netto_beli', 'farmalkes_id', 'isi'])
                            ->where('farmalkes_id', $resep->farmalkes_id)
                            ->firstOrFail();
                        $hna = (int) $farmalkes->harga_netto_beli / (int) $farmalkes->isi;
                        $totalBiaya = (int) $resep->harga_jual * (int) $resep->jumlah_dijual;
                        $TransaksiObat = TransaksiObat::create([
                            'customer_id'   => $customer->id,
                            'farmalkes_id'  => $resep->farmalkes_id,
                            'nama_obat'     => $resep->farmalkes->farmalkes_desc,
                            'jumlah'        => (int) $resep->jumlah_dijual,
                            'hna'           => (int) $hna,
                            'harga_jual'    => (int) $resep->harga_jual,
                            'ppn'           => (int) $totalBiaya - (int)($totalBiaya / (111 / 100)),
                        ]);
                    }
                }
            }
            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Berhasil mereload data customers',
                'count' => $latestRegs->count(),
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return [
                'status' => 'failed',
                'message' => 'Gagal mereload data customers',
            ];
        }
    }

    public static function updateCustomer($request)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($request->id);
            $customer->metode_bayar = $request->metode_bayar;
            $customer->nama_customer = $request->nama_customer;
            $customer->no_hp = $request->no_hp;

            if ($request->metode_bayar === 'TUNAI') {
                $uangTunai = (float) $request->uang_tunai;
                $customer->uang_tunai = $uangTunai;
                $customer->kembalian = $uangTunai - $customer->total_biaya; // dihitung ulang di server, bukan dari kiriman client
            } else {
                // metode selain tunai tidak butuh kembalian
                $customer->uang_tunai = null;
                $customer->kembalian = null;
            }
            $customer->status = 'SELESAI';
            $customer->updated_by = auth()->user()->id;
            $customer->save();

            DB::commit();
            return [
                'status'  => 'success',
                'message' => 'Berhasil menyimpan data customer',
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return [
                'status'  => 'failed',
                'message' => 'Gagal menyimpan data customer',
            ];
        }
    }
}
