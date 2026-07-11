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
        'unit_id',
        'nama_program',
        'ket_program',
        'slug',
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

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function rkap()
    {
        return $this->hasMany(Unit::class, 'program_unit_id', 'id');
    }
}
