<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materi';

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_original_name',
        'file_type',
        'file_size',
        'is_active',
        'uploaded_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getFileIconAttribute(): string
    {
        return match (strtolower($this->file_type)) {
            'pdf' => 'document-text',
            'ppt', 'pptx' => 'presentation-chart-bar',
            default => 'document',
        };
    }

    public function getFileColorAttribute(): string
    {
        return match (strtolower($this->file_type)) {
            'pdf' => 'red',
            'ppt', 'pptx' => 'orange',
            default => 'blue',
        };
    }
}
