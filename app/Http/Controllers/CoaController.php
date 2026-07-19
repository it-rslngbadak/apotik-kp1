<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoaRequest;
use App\Models\Coa;
use App\Models\KodeTransaksi;
use App\Models\MasterFarmalkes;
use App\Models\MasterTindakan;
use App\Models\MasterUmum;
use App\Models\ProgramUnit;
use App\Service\CoaService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

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

    public function store(CoaRequest $request, $slug)
    {
        try {
            $validated = $request->all();
            $program = ProgramUnit::where('slug', $slug)->firstOrFail();
            if ($request->kategori == 'Tindakan') {
                $referensi = MasterTindakan::where('nama_tindakan', $request->desc_transaksi)->first();
                $desc_transaksi =  $referensi ? $referensi->nama_tindakan : $request->desc_transaksi;
            } else if ($request->kategori == 'Farmalkes') {
                $referensi = MasterFarmalkes::where('nama_item', $request->desc_transaksi)->first();
                $desc_transaksi =  $referensi ? $referensi->nama_item : $request->desc_transaksi;
            } else {
                $referensi = MasterUmum::where('nama_item', $request->desc_transaksi)->first();
                $desc_transaksi =  $referensi ? $referensi->nama_item : $request->desc_transaksi;
            }

            // pastikan program_unit_id yang disimpan konsisten dengan slug di url,
            // bukan cuma percaya hidden input di form
            $validated['program_unit_id'] = $program->id;
            $validated['desc_transaksi'] = $desc_transaksi;

            $coa = CoaService::store($validated);

            return response()->json([
                'message' => 'COA berhasil disimpan',
                'data'    => $coa,
            ]);
        } catch (ValidationException $e) {
            throw $e; // otomatis jadi response 422 json untuk request ajax
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Program Unit tidak ditemukan',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('CoaController@store error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan pada server ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update COA
     */
    public function update(CoaRequest $request, $slug, $id)
    {
        try {
            $validated = $request->all();

            $program = ProgramUnit::where('slug', $slug)->firstOrFail();
            if ($request->kategori == 'Tindakan') {
                $referensi = MasterTindakan::where('nama_tindakan', $request->desc_transaksi)->first();
                $desc_transaksi =  $referensi ? $referensi->nama_tindakan : $request->desc_transaksi;
            } else if ($request->kategori == 'Farmalkes') {
                $referensi = MasterFarmalkes::where('nama_item', $request->desc_transaksi)->first();
                $desc_transaksi =  $referensi ? $referensi->nama_item : $request->desc_transaksi;
            } else {
                $referensi = MasterUmum::where('nama_item', $request->desc_transaksi)->first();
                $desc_transaksi =  $referensi ? $referensi->nama_item : $request->desc_transaksi;
            }

            $coa = Coa::where('program_unit_id', $program->id)
                ->where('id', $id)
                ->firstOrFail();

            $validated['program_unit_id'] = $program->id;
            $validated['desc_transaksi'] = $desc_transaksi;
            $coa = CoaService::update($coa, $validated);

            return response()->json([
                'message' => 'COA berhasil diperbarui',
                'data'    => $coa,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data COA tidak ditemukan',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('CoaController@update error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan pada server' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus COA
     */
    public function destroy($slug, $id)
    {
        try {
            $program = ProgramUnit::where('slug', $slug)->firstOrFail();

            $coa = Coa::where('program_unit_id', $program->id)
                ->where('id', $id)
                ->firstOrFail();

            CoaService::destroy($coa);

            return response()->json([
                'message' => 'COA berhasil dihapus',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data COA tidak ditemukan',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('CoaController@destroy error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Gagal menghapus data',
            ], 500);
        }
    }
}
