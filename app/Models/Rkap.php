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

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function programUnit()
    {
        return $this->hasMany(ProgramUnit::class, 'rkap_id', 'id');
    }
}
