<?php

namespace App\Service;

use App\Models\Coa;
use App\Models\ProgramUnit;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramUnitService
{
    public static function getProgramUnitData($request, $rkapSlug)
    {
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        $columnIndex     = $columnIndex_arr[0]['column'] ?? null;
        $columnName      = $columnIndex !== null ? $columnName_arr[$columnIndex]['data'] : null;
        $columnSortOrder = $order_arr[0]['dir'] ?? 'asc';
        $searchValue     = strtoupper($search_arr['value']);
        // ✅ Closure filter reusable
        $searchFilter = function ($query) use ($searchValue) {
            $query->whereRaw('UPPER(nama_program) like ?', ['%' . $searchValue . '%'])
                ->orWhereRaw('UPPER(ket_program) like ?', ['%' . $searchValue . '%']);
        };

        // ✅ Base query
        $baseQuery = ProgramUnit::where('bulan', $request->get('filterBulan'))
            ->whereHas('rkap', function ($query) use ($rkapSlug) {
                $query->where('slug', $rkapSlug);
            });

        $totalRecords = ProgramUnit::whereHas('rkap', function ($query) use ($rkapSlug) {
            $query->where('slug', $rkapSlug);
        })->count();

        $totalRecordsWithFilter = (clone $baseQuery)
            ->where($searchFilter)
            ->count();

        $query = (clone $baseQuery)
            ->where($searchFilter);

        if ($columnName) {
            $query->orderBy($columnName, $columnSortOrder);
        }

        $records = $query
            ->orderBy('created_at', 'DESC')
            ->skip($start)
            ->take($rowPerPage)
            ->get();
        $data_arr = [];
        foreach ($records as $record) {
            $modify = '
                <a href="' . route('coa/list', $record->slug) . '" class="btn btn-sm bg-success-light">
                    <i class="far fa-eye"></i>
                </a>
                <button type="button" class="btn btn-sm bg-warning-light btn-edit-program"
                    data-id="' . $record->id . '"
                    data-nama="' . e($record->nama_program) . '"
                    data-ket="' . e($record->ket_program) . '"
                    data-kategori="' . e($record->kategori) . '"
                    data-bulan="' . e($record->bulan) . '">
                    <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm bg-danger-light btn-delete-program"
                    data-bs-toggle="modal" data-bs-target="#deleteProgramUnit"
                    data-id="' . $record->id . '"
                    data-nama="' . e($record->nama_program) . '">
                    <i class="fa fa-trash"></i>
                </button>
            ';

            $data_arr[] = [
                "nama_program" => $record->nama_program,
                "ket_program"  => $record->ket_program ?? '-',
                "kategori"  => $record->kategori ?? '-',
                "pendapatan"  => $record->total_pendapatan ? number_format($record->total_pendapatan, 0, ',', '.') : '-',
                "biaya"  => $record->total_biaya ? number_format($record->total_biaya, 0, ',', '.') : '-',
                "modify"       => $modify,
            ];
        }

        return [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "data"                 => $data_arr,
        ];
    }

    public static function storeProgramUnit($data, $rkap)
    {
        DB::beginTransaction();
        try {
            $program = ProgramUnit::create([
                'rkap_id'      => $rkap->id,
                'nama_program' => $data['nama_program'],
                'ket_program'  => $data['ket_program'] ?? null,
                'bulan'  => $data['bulan'] ?? null,
                'kategori'  => $data['kategori'],
            ]);
            DB::commit();
            return [
                'status' => 'success',
                'data' => $program,
            ];
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return [
                'status' => 'failed',
                'message' => $th->getMessage(),
            ];
        }
    }

    public static function updateProgramUnit($data, $id)
    {
        DB::beginTransaction();
        try {
            $program = ProgramUnit::findOrFail($id);
            $program->update([
                'nama_program' => $data['nama_program'],
                'ket_program'  => $data['ket_program'],
                'bulan'  => $data['bulan'],
                'kategori'  => $data['kategori'],
            ]);
            DB::commit();
            return [
                'status'  => 'success',
                'data' => $program,
            ];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return [
                'status'  => 'error',
                'message' => 'Data tidak ditemukan',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return [
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ];
        }
    }

    public static function storeProgramUnitRegular($data, $rkap)
    {
        DB::beginTransaction();
        try {
            $program = ProgramUnit::create([
                'rkap_id'      => $rkap->id,
                'nama_program' => $data['nama_program'],
                'ket_program'  => $data['ket_program'],
                'kategori'  => $data['kategori'],
            ]);
            DB::commit();
            return [
                'status' => 'success',
                'data' => $program,
            ];
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return [
                'status' => 'failed',
                'message' => $th->getMessage(),
            ];
        }
    }

    public static function saveProgramReguler($program)
    {
        DB::beginTransaction();
        try {
            $program->update([
                'bulan' => '1'
            ]);
            for ($i = 2; $i <= 12; $i++) {
                $newProgram = ProgramUnit::create([
                    'rkap_id'      => $program->rkap_id,
                    'nama_program' => $program->nama_program,
                    'ket_program'  => $program->ket_program,
                    'bulan'  => $i,
                    'kategori'  => $program->kategori,
                ]);
                foreach ($program->coa as $value) {
                    Coa::create([
                        'program_unit_id'   => $newProgram->id,
                        'kode_transaksi_id' => $value->kode_transaksi_id,
                        'kategori'            => $value->kategori,
                        'desc_transaksi'    => $value->desc_transaksi,
                        'jumlah'            => $value->jumlah,
                        'satuan'            => $value->satuan,
                        'harga_satuan'      => $value->harga_satuan,
                        'jenis_coa'         => $value->jenis_coa,
                        'eselon'            => $value->eselon,
                        'jenis_tarif'            => $value->jenis_tarif,
                        'status'            => 'Pending', // default, sesuaikan kalau ada value lain di DB
                    ]);
                }
            }
            DB::commit();
            return [
                'status' => 'success'
            ];
        } catch (\Exception $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return [
                'status' => 'failed',
                'message' => $th->getMessage(),
            ];
        }
    }
}
