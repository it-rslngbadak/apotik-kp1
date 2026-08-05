<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    protected $connection = 'pgsql';

    protected $table = 'customers';

    protected $fillable = [
        'no_registrasi',
        'tanggal_registrasi',
        'no_hp',
        'nama_customer',
        'status',
        'metode_bayar',
        'uang_tunai',
        'kembalian',
    ];

    protected $appends = [
        'total_biaya',
        'total_biaya_real',
    ];

    protected static function booted()
    {
        static::creating(function (Customer $data) {
            if ($data->no_registrasi) {
                $data->slug = $data->slug ?: static::generateUniqueToken();
            }
        });

        static::updating(function (Customer $data) {
            if ((($data->isDirty('no_registrasi')) && $data->no_registrasi) || empty($data->slug)) {
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

    public function getTotalBiayaAttribute()
    {
        if ($this->metode_bayar == 'TUNAI') {
            $total =  ceil($this->transaksiObat->sum(fn($b) => $b->sub_total / 100)) * 100;
        } else {
            $total =  $this->transaksiObat->sum(fn($b) => $b->sub_total);
        }
        return $total;
    }
    public function getTotalBiayaRealAttribute()
    {
        $total =  $this->transaksiObat->sum(fn($b) => $b->sub_total);
        return $total;
    }

    public function transaksiObat()
    {
        return $this->hasMany(TransaksiObat::class, 'customer_id', 'id');
    }
}
