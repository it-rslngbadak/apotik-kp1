<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rkap;
use App\Models\Unit;
use App\Service\RkapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RkapController extends Controller
{
    public function index($slug)
    {
        $unit = Unit::where('slug', $slug)->first();
        return view('rkap.index', compact('unit'));
    }

    public function getRkapData(Request $request, $unitSlug)
    {
        $coaData = RkapService::getRkapData($request, $unitSlug);
        return response()->json($coaData);
    }

    public function store(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'periode' => 'required|string|max:255',
        ], [
            'periode.required' => 'Periode wajib diisi',
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
            Rkap::create([
                'unit_id' => $unit->id,
                'periode' => $request->periode,
                'status' => $request->status ?? '-',
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Rkap unit berhasil ditambahkan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $slug, $id)
    {
        $validator = Validator::make($request->all(), [
            'periode' => 'required|string|max:255',
        ], [
            'periode.required' => 'Periode wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $rkap = Rkap::findOrFail($id);
            $rkap->update([
                'periode' => $request->periode,
                'status' => $request->status ?? '-',
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Periode berhasil diupdate',
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
            $program = Rkap::findOrFail($id);
            $program->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'RKAP berhasil dihapus',
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
