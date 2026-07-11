<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProgramUnit;
use App\Models\Unit;
use App\Service\ProgramUnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramUnitController extends Controller
{
    public function index($slug)
    {
        $unit = Unit::where('slug', $slug)->first();
        return view('rkap.program-unit.index', compact('unit'));
    }

    public function getProgramUnitData(Request $request, $unitSlug)
    {
        $unitData = ProgramUnitService::getProgramUnitData($request, $unitSlug);
        return response()->json($unitData);
    }

    public function store(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'ket_program'  => 'nullable|string',
        ], [
            'nama_program.required' => 'Nama program wajib diisi',
            'nama_program.max'      => 'Nama program maksimal 255 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $unit = Unit::where('slug', $slug)->firstOrFail();

            ProgramUnit::create([
                'unit_id'      => $unit->id,
                'nama_program' => $request->nama_program,
                'ket_program'  => $request->ket_program,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Program unit berhasil ditambahkan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function update(Request $request, $slug, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'ket_program'  => 'nullable|string',
        ], [
            'nama_program.required' => 'Nama program wajib diisi',
            'nama_program.max'      => 'Nama program maksimal 255 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $program = ProgramUnit::findOrFail($id);
            $program->update([
                'nama_program' => $request->nama_program,
                'ket_program'  => $request->ket_program,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Program unit berhasil diupdate',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
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
                'message' => 'Gagal menghapus data',
            ], 500);
        }
    }
}
