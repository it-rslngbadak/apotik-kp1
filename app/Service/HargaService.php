<?php

namespace App\Service;

use App\Models\Simrs\FarmalkesSimrs;


class HargaService
{
    public static function getHargaData($request)
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
            $query->where('farmalkes_desc', 'like', '%' . $searchValue . '%');
        };

        // ✅ Base query
        $baseQuery = FarmalkesSimrs::select(['farmalkes_id', 'farmalkes_desc', 'isi', 'satuan', 'harga_netto_beli']);

        $totalRecords = FarmalkesSimrs::count();

        $totalRecordsWithFilter = (clone $baseQuery)
            ->where($searchFilter)
            ->count();

        $query = (clone $baseQuery)
            ->orderBy('farmalkes_desc', 'ASC')
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
            $data_arr[] = [
                "farmalkes_id" => $record->farmalkes_id,
                "farmalkes_desc" => $record->farmalkes_desc,
                "satuan" => $record->satuan,
                "harga_jual"  => number_format($record->harga_jual, 0, ',', '.'),
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
