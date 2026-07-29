<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TransaksiObat extends Model
{
    use HasFactory;

    protected $table = 'transaksi_obat';

    protected $fillable = [
        'customer_id',
        'slug',
        'farmalkes_id',
        'nama_obat',
        'jumlah',
        'hna',
        'harga_jual',
        'ppn',
    ];

    protected $appends = [
        'sub_total',
        'metode_bayar',
    ];

    protected static function booted()
    {
        static::creating(function (TransaksiObat $data) {
            if ($data->nama_obat) {
                $data->slug = $data->slug ?: static::generateUniqueToken();
            }
        });

        static::updating(function (TransaksiObat $data) {
            if ((($data->isDirty('nama_obat')) && $data->nama_obat) || empty($data->slug)) {
                $data->slug = static::generateUniqueToken($data->id);
            }
        });
    }

    protected static function generateUniqueToken(int $ignoreId = null): string
    {
        do {
            $token = Str::random(16);
        } while (
            static::where('slug', $token)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()
        );

        return $token;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getSubTotalAttribute()
    {
        $total =  $this->jumlah * $this->harga_jual;
        return $total;
    }

    public function getMetodeBayarAttribute()
    {
        return $this->customer->metode_bayar;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
