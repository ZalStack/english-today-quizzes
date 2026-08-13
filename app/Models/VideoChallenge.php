<?php
// app/Models/VideoChallenge.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'created_by',
        'material_link',
        'material_title',
        'kisi_kisi_link',
        'kisi_kisi_title',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // FIX: Tambahkan foreign key yang benar
    public function submissions()
    {
        return $this->hasMany(VideoSubmission::class, 'challenge_id');
    }
}
