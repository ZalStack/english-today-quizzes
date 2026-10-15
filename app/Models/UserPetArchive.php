<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPetArchive extends Model
{
    use HasFactory;

    protected $table = 'user_pet_archive';

    protected $fillable = [
        'user_id',
        'pet_id',
        'nickname',
        'species',
        'rarity',
        'element',
        'model_preset',
        'color_primary',
        'color_secondary',
        'generation',
        'final_level',
        'hatched_at',
    ];

    protected $casts = [
        'generation' => 'integer',
        'final_level' => 'integer',
        'hatched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
