<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'posisi',
        'foto',
        'bio',
        'email',
        'phone',
        'sosial_media',
        'spesialisasi',
        'pengalaman_tahun',
        'is_active',
        'is_featured',
        'tanggal_bergabung',
        'status',
    ];

    protected $casts = [
        'sosial_media' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'tanggal_bergabung' => 'date',
        'pengalaman_tahun' => 'integer',
    ];

    // Tambahkan accessor untuk debugging
    public function getSosialMediaAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        
        return $value ?: [];
    }
}
