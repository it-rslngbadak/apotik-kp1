<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Service\UnitService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        return view('rkap.unit.index');
    }

    public function getUnitData(Request $request)
    {
        $unitData = UnitService::getUnitData($request);
        return response()->json($unitData);
    }
}
