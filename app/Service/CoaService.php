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
                <button type="button" class="btn btn-sm bg-warning-light btn-edit-program"
                    data-id="' . $record->id . '"
                    data-kode-transaksi="' . e($record->kodeTransaksi->kode) . '"
                    data-ket="' . e($record->desc_transaksi) . '">
                    <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm bg-danger-light btn-delete-program"
                    data-bs-toggle="modal" data-bs-target="#deleteProgramUnit"
                    data-id="' . $record->id . '"
                    data-nama="' . e($record->kodeTransaksi->kode) . '">
                    <i class="fa fa-trash"></i>
                </button>
            ';

            $data_arr[] = [
                "coa" => $record->kode_coa,
                "eselon" => '-',
                "ket_coa" => $record->desc_coa,
                "desc_transaksi"  => $record->desc_transaksi,
                "harga_satuan"  => $record->harga_satuan,
                "jumlah"  => $record->jumlah,
                "satuan"  => $record->satuan,
                "perkiraan"  => $record->total_harga,
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
}
