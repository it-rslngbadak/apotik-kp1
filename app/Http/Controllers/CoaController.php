<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProgramUnit;
use App\Service\CoaService;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    public function index($slug)
    {
        $program = ProgramUnit::where('slug', $slug)->first();
        return view('rkap.coa.index', compact('program'));
    }

    public function getCoaData(Request $request, $programUnitSlug)
    {
        $coaData = CoaService::getCoaData($request, $programUnitSlug);
        return response()->json($coaData);
    }
}
