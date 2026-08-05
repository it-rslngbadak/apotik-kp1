<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmalkesSimrs extends Model
{
    use HasFactory;

    protected $connection = 'odbc';

    protected $table = 'farmalkes';

    protected $appends = [
        'harga_jual',
    ];

    public function getHargaJualAttribute()
    {
        $hna = $this->harga_netto_beli / $this->isi;
        $harga = $hna + ($hna * (25 / 100));
        $ppn = $harga * (11 / 100);
        $hargaJual = $harga + $ppn;
        return $hargaJual;
    }
}
