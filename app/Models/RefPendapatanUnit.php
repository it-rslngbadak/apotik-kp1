<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefPendapatanUnit extends Model
{
    use HasFactory;

    protected $table = 'ref_pendapatan_units';

    protected $fillable = [
        'tahun',
        'unit_id',
        'nama_unit',
        'kode_transaksi',
        'nama_transaksi',
        'cara_bayar',
        'jumlah',
        'total',
        'coa_pendapatan',
        'coa_biaya',
    ];
}
