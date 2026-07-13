<?php

namespace App\Service;

use App\Models\ProgramUnit;

class ProgramUnitService
{
    public static function getProgramUnitData($request, $rkapSlug)
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
            $query->whereRaw('UPPER(nama_program) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(ket_program) like ?', ['%' . $searchValue . '%']);
        };

        // ✅ Base query
        $baseQuery = ProgramUnit::whereHas('rkap', function ($query) use ($rkapSlug) {
            $query->where('slug', $rkapSlug);
        });
        // Jika ingin filter tgl_keluar juga, ganti/tambah:
        // ->where('tgl_keluar', '<=', $sampai_tgl)

        $totalRecords = ProgramUnit::whereHas('rkap', function ($query) use ($rkapSlug) {
            $query->where('slug', $rkapSlug);
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
                <a href="' . route('coa/list', $record->slug) . '" class="btn btn-sm bg-success-light">
                    <i class="far fa-eye"></i>
                </a>
                <button type="button" class="btn btn-sm bg-warning-light btn-edit-program"
                    data-id="' . $record->id . '"
                    data-nama="' . e($record->nama_program) . '"
                    data-ket="' . e($record->ket_program) . '">
                    <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm bg-danger-light btn-delete-program"
                    data-bs-toggle="modal" data-bs-target="#deleteProgramUnit"
                    data-id="' . $record->id . '"
                    data-nama="' . e($record->nama_program) . '">
                    <i class="fa fa-trash"></i>
                </button>
            ';

            $data_arr[] = [
                "nama_program" => $record->nama_program,
                "ket_program"  => $record->ket_program ?? '-',
                "modify"       => $modify,
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
