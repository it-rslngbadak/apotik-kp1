<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rkap extends Model
{
    use HasFactory;

    protected $table = 'rkaps';

    protected $fillable = [
        'program_unit_id',
        'kode_transaksi_id',
        'desc_transaksi',
        'jumlah',
        'satuan',
        'harga_satuan',
        'jenis_coa',
        'status',
    ];

    protected $appends = [
        'total_harga'
    ];

    public function getTotalhargaAttribute()
    {
        return $this->jumlah * $this->harga_satuan;
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function kodeTransaksi()
    {
        return $this->belongsTo(KodeTransaksi::class, 'kode_transaksi_id', 'id');
    }
}
