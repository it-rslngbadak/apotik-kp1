<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'nama',
        'kode_pendapatan',
        'desc_pendapatan',
        'kode_biaya',
        'desc_biaya',
        'kategori',
        'slug',
    ];

    protected static function booted()
    {
        static::creating(function (Unit $data) {
            if ($data->nama) {
                $data->slug = $data->slug ?: static::generateUniqueToken();
            }
        });

        static::updating(function (Unit $data) {
            if ((($data->isDirty('nama')) && $data->nama) || empty($data->slug)) {
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

    public function rkap()
    {
        return $this->hasManyThrough(Rkap::class, ProgramUnit::class, 'unit_id', 'program_unit_id', 'id', 'id');
    }

    public function programUnit()
    {
        return $this->hasMany(ProgramUnit::class, 'unit_id', 'id');
    }
}
