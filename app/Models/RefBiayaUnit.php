<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefBiayaUnit extends Model
{
    use HasFactory;
    protected $table = 'ref_biaya_units';

    protected $fillable = [
        'tahun',
        'unit_id',
        'nama_unit',
        'kode_transaksi',
        'nama_transaksi',
        'jumlah',
    ];
}
