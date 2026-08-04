<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Service\HargaService;
use Illuminate\Http\Request;

class HargaController extends Controller
{
    public function index()
    {
        return view('apotik.harga.index');
    }

    public function getDataHarga(Request $request)
    {
        $response = HargaService::getHargaData($request);
        return response()->json($response)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');;
    }
}
