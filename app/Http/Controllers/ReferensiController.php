<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RefBiayaUnit;
use App\Models\RefPendapatanUnit;
use App\Service\ReferensiService;
use Illuminate\Http\Request;

class ReferensiController extends Controller
{
    public function indexPendapatan()
    {
        $units = RefPendapatanUnit::select('nama_unit')->distinct()->get();
        return view('rkap.referensi.pendapatan', compact('units'));
    }

    public function getPendapatanData(Request $request)
    {
        $data = ReferensiService::getRefPendapatanData($request);
        return response()->json($data);
    }

    public function indexBiaya()
    {
        $units = RefBiayaUnit::select('nama_unit')->distinct()->get();
        return view('rkap.referensi.biaya', compact('units'));
    }

    public function getBiayaData(Request $request)
    {
        $data = ReferensiService::getRefBiayaData($request);
        return response()->json($data);
    }
}
