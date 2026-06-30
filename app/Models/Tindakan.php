<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tindakan extends Model
{
    use HasFactory;

    protected $connection = 'pgsql';

    protected $table = 'tindakans';

    protected $fillable = [
        'billing_id',
        'nama_tindakan',
        'jumlah',
        'discount',
        'jenis_transaksi',
        'biaya',
        'payment',
    ];

    protected $appends = [
        'total_biaya'
    ];

    public function getTotalBiayaAttribute(): int
    {
        $total = $this->jumlah * $this->biaya;
        $discount = (int) round($total * ((int) $this->discount / 100));
        $total_biaya = (int) round($total - $discount);
        return $total_biaya;
    }

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class, 'billing_id', 'id');
    }
}
