<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

class Coa extends Model
{
    use HasFactory;
    use BelongsToThrough;

    protected $table = 'coas';

    protected $fillable = [
        'program_unit_id',
        'kode_transaksi_id',
        'desc_transaksi',
        'jumlah',
        'satuan',
        'harga_satuan',
        'jenis_coa',
        'status',
        'eselon',
        'kategori',
        'jenis_tarif',
    ];

    protected $appends = [
        'total_perkiraan',
        'kode_coa',
        'desc_coa',
    ];

    public function getTotalPerkiraanAttribute()
    {
        return $this->jumlah * $this->harga_satuan;
    }
    public function getKodeCoaAttribute()
    {
        if ($this->jenis_coa === 'Pendapatan') {
            $coa = $this->unit->kode_pendapatan . '.' . $this->kodeTransaksi->kode;
        } else {
            $coa = $this->unit->kode_biaya . '.' . $this->kodeTransaksi->kode;
        }
        return $coa;
    }

    public function getDescCoaAttribute()
    {
        return $this->kodeTransaksi->nama_transaksi;
    }

    public function unit()
    {
        return $this->belongsToThrough(Unit::class, [Rkap::class, ProgramUnit::class]);
    }

    public function programUnit()
    {
        return $this->belongsTo(ProgramUnit::class, 'program_unit_id', 'id');
    }

    public function kodeTransaksi()
    {
        return $this->belongsTo(KodeTransaksi::class, 'kode_transaksi_id', 'id');
    }
}
