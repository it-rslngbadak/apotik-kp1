<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiResepSimrs extends Model
{
    use HasFactory;
    protected $connection = 'odbc';

    protected $table = 'transaksi_resep';

    public function farmalkes()
    {
        return $this->belongsTo(FarmalkesSimrs::class, 'farmalkes_id', 'farmalkes_id');
    }
}
