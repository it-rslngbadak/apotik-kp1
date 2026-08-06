<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Setoran extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';

    protected $table = 'setoran';

    protected $fillable = [
        'user_id',
        'setoran',
        'shift',
        'tanggal',
        'status',
        'total_tunai_customer',
        'selisih',
        'slug',
    ];

    // protected $appends = [
    //     'selisih',
    // ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (Setoran $data) {
            if ($data->tanggal) {
                $data->slug = $data->slug ?: static::generateUniqueToken();
            }
        });

        static::updating(function (Setoran $data) {
            if ((($data->isDirty('tanggal')) && $data->tanggal) || empty($data->slug)) {
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

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', now()->toDateString());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
