<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Rkap extends Model
{
    use HasFactory;

    protected $table = 'rkaps';

    protected $fillable = [
        'unit_id',
        'periode',
        'status',
        'slug',
    ];

    protected $appends = [
        'total_pendapatan',
        'total_biaya'
    ];

    protected static function booted()
    {
        static::creating(function (Rkap $data) {
            if ($data->periode) {
                $data->slug = $data->slug ?: static::generateUniqueToken();
            }
        });

        static::updating(function (Rkap $data) {
            if ((($data->isDirty('periode')) && $data->periode) || empty($data->slug)) {
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

    public function getTotalPendapatanAttribute()
    {
        $total = $this->programUnit
            ->sum('total_pendapatan');

        if ($total < 100000) {
            $factor = 1000;
        } elseif ($total < 1000000) {
            $factor = 10000;
        } elseif ($total < 10000000) {
            $factor = 100000;
        } elseif ($total < 100000000) {
            $factor = 1000000;
        } elseif ($total < 1000000000) {
            $factor = 10000000;
        } elseif ($total < 10000000000) {
            $factor = 100000000;
        } elseif ($total < 100000000000) {
            $factor = 1000000000;
        } else {
            $factor = 10000000000;
        }

        return round($total / $factor) * $factor;
    }

    public function getTotalBiayaAttribute()
    {
        $total = $this->programUnit
            ->sum('total_biaya');

        if ($total < 100000) {
            $factor = 1000;
        } elseif ($total < 1000000) {
            $factor = 10000;
        } elseif ($total < 10000000) {
            $factor = 100000;
        } elseif ($total < 100000000) {
            $factor = 1000000;
        } elseif ($total < 1000000000) {
            $factor = 10000000;
        } elseif ($total < 10000000000) {
            $factor = 100000000;
        } elseif ($total < 100000000000) {
            $factor = 1000000000;
        } else {
            $factor = 10000000000;
        }

        return round($total / $factor) * $factor;
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function programUnit()
    {
        return $this->hasMany(ProgramUnit::class, 'rkap_id', 'id');
    }
}
