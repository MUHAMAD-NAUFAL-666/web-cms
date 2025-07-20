<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'deskripsi',
        'icon',
        'warna_tema',
        'urutan',
        'is_active',
        'is_featured',
        'harga_mulai',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'harga_mulai' => 'decimal:2',
        'urutan' => 'integer',
    ];

    // Scope untuk layanan aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk layanan unggulan
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope untuk urutan
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan', 'asc');
    }

    // Accessor untuk format harga
    public function getFormattedHargaAttribute()
    {
        if ($this->harga_mulai) {
            return 'Mulai dari Rp ' . number_format($this->harga_mulai, 0, ',', '.');
        }
        return 'Harga disesuaikan';
    }
}
