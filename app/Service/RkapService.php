<?php

namespace App\Service;

use App\Models\ProgramUnit;
use App\Models\Rkap;
use App\Models\Unit;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RkapService
{
    public static function getRkapData($request, $unitSlug)
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
            $query->whereRaw('UPPER(periode) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(status) like ?', ['%' . $searchValue . '%']);
        };

        // ✅ Base query
        $baseQuery = Rkap::whereHas('unit', function ($query) use ($unitSlug) {
            $query->where('slug', $unitSlug);
        });
        // Jika ingin filter tgl_keluar juga, ganti/tambah:
        // ->where('tgl_keluar', '<=', $sampai_tgl)

        $totalRecords = Rkap::whereHas('unit', function ($query) use ($unitSlug) {
            $query->where('slug', $unitSlug);
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
                <a href="' . route('program-unit/list', $record->slug) . '" class="btn btn-sm bg-success-light">
                    <i class="far fa-eye"></i>
                </a>
                <button type="button" class="btn btn-sm bg-warning-light btn-edit-data"
                    data-id="' . $record->id . '"
                    data-periode="' . e($record->periode) . '">
                    <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm bg-danger-light btn-delete-data"
                    data-bs-toggle="modal" data-bs-target="#deleteData"
                    data-id="' . $record->id . '"
                    data-periode="' . e($record->periode) . '">
                    <i class="fa fa-trash"></i>
                </button>
            ';

            $data_arr[] = [
                "periode" => $record->periode,
                "pendapatan" => $record->total_pendapatan ? number_format($record->total_pendapatan, 0, ',', '.') : '-',
                "biaya" => $record->total_biaya ? number_format($record->total_biaya, 0, ',', '.') : '-',
                "status" => $record->status ?? '-',
                "modify" => $modify,
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
