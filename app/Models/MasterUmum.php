<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterUmum extends Model
{
    use HasFactory;

    protected $table = 'master_umum';

    protected $fillable = [
        'nama_item',
        'harga_satuan',
        'satuan',
        'kategori',
    ];
}
