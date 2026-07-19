<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramUnit extends Model
{
    use HasFactory;

    protected $table = 'program_units';

    protected $fillable = [
        'rkap_id',
        'nama_program',
        'ket_program',
        'slug',
        'bulan',
        'kategori',
    ];

    protected $appends = [
        'total_pendapatan',
        'total_biaya'
    ];

    protected static function booted()
    {
        static::creating(function (ProgramUnit $data) {
            if ($data->nama_program) {
                $data->slug = $data->slug ?: static::generateUniqueToken();
            }
        });

        static::updating(function (ProgramUnit $data) {
            if ((($data->isDirty('nama_program')) && $data->nama_program) || empty($data->slug)) {
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
        $total = $this->coa
            ->where('jenis_coa', 'Pendapatan')
            ->sum('total_perkiraan');

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
        $total = $this->coa->where('jenis_coa', 'Biaya')->sum('total_perkiraan');
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

    // public function unit()
    // {
    //     return $this->belongsTo(Unit::class, 'unit_id', 'id');
    // }

    public function rkap()
    {
        return $this->belongsTo(Rkap::class, 'rkap_id', 'id');
    }

    public function coa()
    {
        return $this->hasMany(Coa::class, 'program_unit_id', 'id');
    }
}
