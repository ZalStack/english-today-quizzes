<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyQuestClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quest_key',
        'claimed_on',
    ];

    protected $casts = [
        'claimed_on' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
