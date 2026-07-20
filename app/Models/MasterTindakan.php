<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTindakan extends Model
{
    use HasFactory;

    protected $table = 'master_tindakan';

    protected $fillable = [
        'nama_tindakan',
        'eselon',
        'tarif_kamar',
        'tarif_rj',
        'tarif_ugd',
        'tarif_kls_3',
        'tarif_kls_2',
        'tarif_kls_1',
        'tarif_kls_vip',
        'tarif_kls_icu',
        'tarif_kls_isolasi',
    ];
}
