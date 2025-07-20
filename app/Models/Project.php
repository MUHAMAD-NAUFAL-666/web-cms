<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'klien',
        'deskripsi',
        'gambar',
        'link',
        'stack',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_featured',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_featured' => 'boolean',
    ];

    // Scope untuk featured projects
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope untuk project berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk project yang sudah selesai
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Accessor untuk mendapatkan array teknologi
    public function getStackArrayAttribute()
    {
        return $this->stack ? explode(',', $this->stack) : [];
    }

    // Accessor untuk status badge color
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'planning' => 'warning',
            'development' => 'info',
            'testing' => 'primary',
            'completed' => 'success',
            'maintenance' => 'gray',
            default => 'gray',
        };
    }

    // Accessor untuk status icon
    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'planning' => 'heroicon-o-clock',
            'development' => 'heroicon-o-code-bracket',
            'testing' => 'heroicon-o-bug-ant',
            'completed' => 'heroicon-o-check-circle',
            'maintenance' => 'heroicon-o-wrench-screwdriver',
            default => 'heroicon-o-question-mark-circle',
        };
    }
}
