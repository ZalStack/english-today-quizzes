<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'growth_gained',
        'source_type',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
