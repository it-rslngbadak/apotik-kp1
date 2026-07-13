<?php

namespace App\Service;

use App\Models\RefBiayaUnit;
use App\Models\RefPendapatanUnit;

class ReferensiService
{
    public static function getRefPendapatanData($request)
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
            $query->whereRaw('UPPER(nama_transaksi) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(cara_bayar) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(coa_pendapatan) like ?', ['%' . $searchValue . '%']);
        };

        // ✅ Base query
        $baseQuery = RefPendapatanUnit::where('nama_unit', $request->get('nama_unit'));

        $totalRecords = RefPendapatanUnit::where('nama_unit', $request->get('nama_unit'))
            ->count();

        $totalRecordsWithFilter = (clone $baseQuery)
            ->where($searchFilter)
            ->count();

        $query = (clone $baseQuery)
            ->where($searchFilter);

        if ($columnName) {
            $query->orderBy($columnName, $columnSortOrder);
        }

        $records = $query
            ->orderBy('coa_pendapatan', 'DESC')
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {
            $data_arr[] = [
                // "kode_transaksi" => $record->kode_transaksi,
                "nama_transaksi" => $record->nama_transaksi,
                "cara_bayar" => $record->cara_bayar,
                "jumlah" => $record->jumlah,
                "total" => 'Rp ' . number_format($record->total, 0, ',', '.'),
                "coa_pendapatan" => $record->coa_pendapatan,
                // "coa_biaya" => $record->coa_biaya,
            ];
        }

        return [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "data"                 => $data_arr,
        ];
    }

    public static function getRefBiayaData($request)
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
            $query->whereRaw('UPPER(nama_transaksi) like ?', ['%' . $searchValue . '%']);
        };

        // ✅ Base query
        $baseQuery = RefBiayaUnit::where('nama_unit', $request->get('nama_unit'));

        $totalRecords = RefBiayaUnit::where('nama_unit', $request->get('nama_unit'))
            ->count();

        $totalRecordsWithFilter = (clone $baseQuery)
            ->where($searchFilter)
            ->count();

        $query = (clone $baseQuery)
            ->where($searchFilter);

        if ($columnName) {
            $query->orderBy($columnName, $columnSortOrder);
        }

        $records = $query
            ->orderBy('coa_pendapatan', 'DESC')
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {
            $data_arr[] = [
                // "kode_transaksi" => $record->kode_transaksi,
                "nama_transaksi" => $record->nama_transaksi,
                // "cara_bayar" => $record->cara_bayar,
                "jumlah" => $record->jumlah,
                // "total" => 'Rp ' . number_format($record->total, 0, ',', '.'),
                // "coa_pendapatan" => $record->coa_pendapatan,
                // "coa_biaya" => $record->coa_biaya,
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
