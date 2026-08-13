<?php
// app/Models/VideoSubmission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id',
        'user_id',
        'link',
        'notes',
    ];

    public function challenge()
    {
        return $this->belongsTo(VideoChallenge::class, 'challenge_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
