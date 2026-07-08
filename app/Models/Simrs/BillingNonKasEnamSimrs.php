<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingNonKasEnamSimrs extends Model
{
    use HasFactory;
    protected $connection = 'odbc';

    protected $table = 'billing_non_kas_enam';
}
