<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeTransaksi extends Model
{
    use HasFactory;

    protected $table = 'kode_transaksi';

    protected $fillable = [
        'kode',
        'nama_transaksi',
        'desc',
        'jenis_kode',
        'kategori',
    ];
}
