<?php

namespace App\Http\Controllers;

use App\Models\Setoran;
use App\Service\SetoranService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class SetoranController extends Controller
{
    public function index()
    {
        return view('apotik.setoran.index');
    }

    public function getDataSetoran(Request $request)
    {
        $response = SetoranService::getSetoranData($request);

        return response()->json($response)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function shiftStatus()
    {
        return response()->json(SetoranService::getShiftStatus());
    }

    public function store(Request $request)
    {
        $request->validate([
            'setoran' => 'required|numeric|min:0',
            'shift'   => 'required|in:PAGI,SORE',
        ]);

        try {
            $setoran = SetoranService::store($request->only('setoran', 'shift'), Auth::id());

            return response()->json([
                'status'  => 'success',
                'message' => 'Setoran berhasil disimpan.',
                'data'    => [
                    'total_tunai_customer' => (int) $setoran->total_tunai_customer,
                    'selisih'              => (int) $setoran->selisih,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'setoran' => 'required|numeric|min:0',
            'shift'   => 'required|in:PAGI,SORE',
        ]);

        $setoran = Setoran::findOrFail($id);

        try {
            $setoran = SetoranService::update($setoran, $request->only('setoran', 'shift'));

            return response()->json([
                'status'  => 'success',
                'message' => 'Setoran berhasil diperbarui.',
                'data'    => [
                    'total_tunai_customer' => (int) $setoran->total_tunai_customer,
                    'selisih'              => (int) $setoran->selisih,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }


    // Disiapkan dulu, belum diaktifkan sesuai permintaan
    // public function destroy($id)
    // {
    //     Setoran::findOrFail($id)->delete();
    //     return response()->json(['status' => 'success', 'message' => 'Setoran berhasil dihapus.']);
    // }

    public function cetakStruk($id)
    {
        $setoran = Setoran::with('user')->findOrFail($id);

        if (! SetoranService::canPrint($setoran, Auth::user())) {
            abort(403, 'Anda tidak memiliki akses untuk mencetak laporan setoran ini.');
        }

        $customers = \App\Models\Customer::with('transaksiObat')
            ->where('metode_bayar', 'TUNAI')
            ->where('updated_by', $setoran->user_id)
            ->whereDate('updated_at', $setoran->tanggal->toDateString())
            ->orderBy('no_registrasi')
            ->get();

        $logoPath = public_path('assets/img/single_Logo_Klinik_Badak.png');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('apotik.setoran.laporan', compact('setoran', 'customers', 'logoBase64'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-setoran-' . $setoran->id . '.pdf');
    }
}
