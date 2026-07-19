<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramUnitRequest;
use App\Models\ProgramUnit;
use App\Models\Rkap;
use App\Models\Unit;
use App\Service\ProgramUnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProgramUnitController extends Controller
{
    public function index($slug)
    {
        $rkap = Rkap::where('slug', $slug)->first();
        return view('rkap.program-unit.index', compact('rkap'));
    }

    public function getProgramUnitData(Request $request, $rkapSlug)
    {
        $unitData = ProgramUnitService::getProgramUnitData($request, $rkapSlug);
        return response()->json($unitData);
    }

    public function createRegularProgram($slug)
    {
        $rkap = Rkap::where('slug', $slug)->first();
        return view('rkap.program-unit.create', compact('rkap'));
    }

    public function inputCoaProgramReguler($slug)
    {
        $program = ProgramUnit::where('slug', $slug)->first();
        return view('rkap.program-unit.input-coa-regular', compact('program'));
    }

    public function store(ProgramUnitRequest $request, $slug)
    {
        $validated = $request->validated();
        $rkap = Rkap::where('slug', $slug)->firstOrFail();
        $program = ProgramUnitService::storeProgramUnit($validated, $rkap);

        if ($program['status'] == 'success') {
            return response()->json([
                'status'  => 'success',
                'message' => 'Program unit berhasil ditambahkan',
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function storeRegularProgram(ProgramUnitRequest $request, $slug)
    {
        $validated = $request->validated();
        $rkap = Rkap::where('slug', $slug)->firstOrFail();
        $result = ProgramUnitService::storeProgramUnit($validated, $rkap);

        if ($result['status'] == 'success') {
            $program = $result['data'];
            return redirect()->route('program-unit-regular.template', $program->slug);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function saveProgramReguler($slug)
    {
        $program = ProgramUnit::where('slug', $slug)->first();
        $result = ProgramUnitService::saveProgramReguler($program);
        if ($result['status'] == 'success') {
            return redirect()->route('program-unit/list', $program->rkap->slug);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function update(ProgramUnitRequest $request, $slug, $id)
    {
        $validated = $request->validated();
        $program = ProgramUnitService::updateProgramUnit($validated, $id);
        if ($program['status'] == 'success') {
            return response()->json([
                'status'  => 'success',
                'message' => 'Program unit berhasil update',
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function destroy($slug, $id)
    {
        try {
            $program = ProgramUnit::findOrFail($id);
            $program->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Program unit berhasil dihapus',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus data ' . $e->getMessage(),
            ], 500);
        }
    }
}
