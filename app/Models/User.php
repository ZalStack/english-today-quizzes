<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'full_name',
        'email',
        'password',
        'division_id',
        'role',
        'status',
        'phone',
        'address',
        'avatar',
        'points',
        'welcome_pack_claimed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'points' => 'integer',
            'welcome_pack_claimed_at' => 'datetime',
        ];
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function createdQuizzes()
    {
        return $this->hasMany(Quiz::class, 'created_by');
    }

    public function quizAttempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }

    public function isHR()
    {
        return $this->role === 'hr';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    public function userPet()
    {
        return $this->hasOne(UserPet::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function userItems()
    {
        return $this->hasMany(UserItem::class);
    }

    public function petArchive()
    {
        return $this->hasMany(UserPetArchive::class);
    }
}
