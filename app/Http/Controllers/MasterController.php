<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KodeTransaksi;
use App\Models\MasterFarmalkes;
use App\Models\MasterTindakan;
use App\Models\MasterUmum;
use App\Models\ProgramUnit;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function searchTindakan(Request $request)
    {
        $search = strtoupper($request->q);
        $data = MasterTindakan::when(
            $search,
            fn($q) =>
            $q->where('eselon', $request->get('eselon'))
                ->whereRaw('UPPER(nama_tindakan) like ?', ['%' . $search . '%']) // sesuaikan nama kolom
        )
            ->limit(20)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'text' => $item->nama_tindakan,
                'jumlah' => 1,
                'satuan' => $item->satuan ?? '-',
                'harga_satuan' => $item->{$request->jenis_tarif} ?? null,
            ]);

        return response()->json($data);
    }

    private function kelasToKolomHarga(?string $kelas): ?string
    {
        // TODO: sesuaikan dengan nama kolom asli di tabel master_tindakans
        return match ($kelas) {
            'Kamar' => 'tarif_kamar',
            'Rawat Jalan' => 'tarif_rj',
            'UGD' => 'tarif_rj',
            'III' => 'tarif_kelas_3',
            'II'  => 'tarif_kelas_2',
            'I'   => 'tarif_kelas_1',
            'VIP' => 'tarif_vip',
            'ICU' => 'tarif_icu',
            'ISOLASI' => 'tarif_isolasi',
            default => null,
        };
    }

    public function searchFarmalkes(Request $request)
    {
        $search = strtoupper($request->q);
        $data = MasterFarmalkes::when(
            $search,
            fn($q) =>
            $q->whereRaw('UPPER(nama_item) like ?', ['%' . $search . '%'])
        )
            ->limit(20)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'text' => $item->nama_item,
                'jumlah' => 1,
                'satuan' => $item->satuan ?? '-',
                'harga_satuan' => $item->harga_satuan, // sesuaikan nama kolom
            ]);

        return response()->json($data);
    }

    public function searchUmum(Request $request)
    {
        $search = strtoupper($request->q);
        $data = MasterUmum::when(
            $request->q,
            fn($q) =>
            $q->whereRaw('UPPER(nama_item) like ?', ['%' . $search . '%'])
        )
            ->limit(20)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'text' => $item->nama_item,
                'jumlah' => 1,
                'satuan' => $item->satuan ?? '-',
                'harga_satuan' => $item->harga,
            ]);

        return response()->json($data);
    }

    public function searchKodeTransaksi(Request $request)
    {
        $programUnit = ProgramUnit::findOrFail($request->program_unit_id);
        $search = strtoupper($request->q);
        $data = KodeTransaksi::where('jenis_kode', $request->jenis_kode)
            // TODO: sesuaikan relasi kategori unit -> kode_transaksi yang sebenarnya
            ->when(
                $programUnit->kategori_id ?? null,
                fn($q) =>
                $q->where('kategori_id', $programUnit->kategori_id)
            )
            ->when(
                $request->q,
                fn($q) =>
                $q->whereRaw('UPPER(nama_transaksi) like ?', ['%' . $search . '%'])
            )
            ->limit(20)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'text' => $item->kode . ' - ' . $item->nama_transaksi,
            ]);

        return response()->json($data);
    }
}
