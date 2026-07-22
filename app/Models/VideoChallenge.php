<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoChallenge extends Model
{
    protected $fillable = [
        'title', 'description', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(VideoSubmission::class, 'challenge_id');
    }
}
