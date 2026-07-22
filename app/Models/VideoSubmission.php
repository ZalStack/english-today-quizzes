<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoSubmission extends Model
{
    protected $fillable = [
        'challenge_id', 'user_id', 'link', 'notes',
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
