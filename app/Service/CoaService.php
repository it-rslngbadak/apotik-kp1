<?php

namespace App\Service;

use App\Models\Coa;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoaService
{
    public static function getCoaData($request, $slug)
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
            $query->whereRaw('UPPER(desc_transaksi) like ?', ['%' . $searchValue . '%'])
                ->orWhereHas('kodeTransaksi', function ($query) use ($searchValue) {
                    $query->whereRaw('UPPER(kode) like ?', ['%' . $searchValue . '%']);
                });
        };

        // ✅ Base query
        $baseQuery = Coa::whereHas('programUnit', function ($query) use ($slug) {
            $query->where('slug', $slug);
        });
        // Jika ingin filter tgl_keluar juga, ganti/tambah:
        // ->where('tgl_keluar', '<=', $sampai_tgl)

        $totalRecords = Coa::whereHas('programUnit', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->count();

        $totalRecordsWithFilter = (clone $baseQuery)
            ->where($searchFilter)
            ->count();

        $query = (clone $baseQuery)
            ->where($searchFilter);

        if ($columnName) {
            $query->orderBy($columnName, $columnSortOrder);
        }

        $records = $query
            ->orderBy('created_at', 'DESC')
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {
            $modify = '
                <button type="button" class="btn btn-sm bg-warning-light btn-edit-coa"
                    data-id="' . $record->id . '"
                    data-jenis_coa="' . e($record->jenis_coa) . '"
                    data-eselon="' . e($record->eselon) . '"
                    data-jumlah="' . e($record->jumlah) . '"
                    data-satuan="' . e($record->satuan) . '"
                    data-harga_satuan="' . e($record->harga_satuan) . '"
                    data-desc_transaksi="' . e($record->desc_transaksi) . '"
                    data-coa_text="' . e($record->kode_coa) . '"
                    data-kode_transaksi_id="' . $record->kode_transaksi_id . '"
                    data-kategori="' . $record->kategori . '"
                    data-jenis_tarif="' . $record->jenis_tarif . '">
                    <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm bg-danger-light btn-delete-coa"
                    data-bs-toggle="modal" data-bs-target="#deleteProgramUnit"
                    data-id="' . $record->id . '"
                    data-nama="' . e($record->kodeTransaksi->kode) . '">
                    <i class="fa fa-trash"></i>
                </button>
            ';

            $data_arr[] = [
                "coa" => $record->kode_coa,
                "jenis_coa" => $record->jenis_coa,
                "eselon" => '-',
                "ket_coa" => $record->desc_coa,
                "desc_transaksi"  => $record->desc_transaksi,
                "harga_satuan"  => $record->harga_satuan,
                "jumlah"  => $record->jumlah,
                "satuan"  => $record->satuan,
                "perkiraan"  => $record->total_perkiraan,
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

    public static function store(array $data)
    {
        return Coa::create([
            'program_unit_id'   => $data['program_unit_id'],
            'kode_transaksi_id' => $data['kode_transaksi_id'],
            'kategori'            => $data['kategori'],
            'desc_transaksi'    => $data['desc_transaksi'],
            'jumlah'            => $data['jumlah'],
            'satuan'            => $data['satuan'],
            'harga_satuan'      => $data['harga_satuan'],
            'jenis_coa'         => $data['jenis_coa'],
            'eselon'            => $data['eselon'] ?? null,
            'jenis_tarif'            => $data['jenis_tarif'] ?? null,
            'status'            => 'Pending', // default, sesuaikan kalau ada value lain di DB
        ]);
    }

    public static function update(Coa $coa, array $data)
    {
        $coa->update([
            'kode_transaksi_id' => $data['kode_transaksi_id'],
            'desc_transaksi'    => $data['desc_transaksi'],
            'kategori'            => $data['kategori'],
            'jumlah'            => $data['jumlah'],
            'satuan'            => $data['satuan'],
            'harga_satuan'      => $data['harga_satuan'],
            'jenis_coa'         => $data['jenis_coa'],
            'eselon'            => $data['eselon'] ?? null,
            'jenis_tarif'            => $data['jenis_tarif'] ?? null,
        ]);

        return $coa->fresh();
    }

    public static function destroy(Coa $coa)
    {
        return $coa->delete();
    }
}
