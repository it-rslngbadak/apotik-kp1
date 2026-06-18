<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferensiDiscountSimrs extends Model
{
    use HasFactory;

    protected $connection = 'odbc';

    protected $table = 'referensi_discount';

    protected $appends = [
        'deskripsi'
    ];
}
