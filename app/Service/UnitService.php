<?php

namespace App\Service;

use App\Models\Billing;
use App\Models\Sp3;
use App\Models\Unit;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnitService
{
    public static function getUnitData($request)
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
            $query->whereRaw('UPPER(nama) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(kode_pendapatan) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(desc_pendapatan) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(kode_biaya) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(desc_biaya) like ?', ['%' . $searchValue . '%']);
        };

        // ✅ Base query dengan filter tanggal (tgl_masuk & tgl_keluar)
        $baseQuery = new Unit();
        // Jika ingin filter tgl_keluar juga, ganti/tambah:
        // ->where('tgl_keluar', '<=', $sampai_tgl)

        $totalRecords = Unit::count();

        $totalRecordsWithFilter = (clone $baseQuery)
            ->where($searchFilter)
            ->count();

        $query = (clone $baseQuery)
            ->where($searchFilter);

        if ($columnName) {
            $query->orderBy($columnName, $columnSortOrder);
        }

        $records = $query
            ->orderBy('kode_pendapatan', 'ASC')
            ->orderBy('kode_biaya', 'ASC')
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {
            $modify = '
            <td class="text-end">
                <a href="' . route('rkap/list', $record->slug) . '" class="btn btn-sm bg-success-light">
                    <i class="far fa-eye me-2"></i>
            </td>
            ';

            $data_arr[] = [
                "nama"          => $record->nama,
                "modify"          => $modify,
            ];
        }

        return [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "data"                 => $data_arr,
        ];
    }
}
