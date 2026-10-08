<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'species',
        'rarity',
        'element',
        'description',
        'color_primary',
        'color_secondary',
        'model_preset',
        'base_power',
    ];

    protected $appends = [
        'element_icon',
        'rarity_badge_color',
    ];

    public function userPets()
    {
        return $this->hasMany(UserPet::class);
    }

    public function getRarityBadgeColorAttribute(): string
    {
        return match ($this->rarity) {
            'common' => 'bg-gray-100 text-gray-700 border-gray-300',
            'rare' => 'bg-blue-100 text-blue-700 border-blue-300',
            'epic' => 'bg-purple-100 text-purple-700 border-purple-300',
            'legendary' => 'bg-amber-100 text-amber-800 border-amber-400',
            default => 'bg-gray-100 text-gray-700 border-gray-300',
        };
    }

    public function getElementIconAttribute(): string
    {
        return match ($this->element) {
            'fire' => '🔥',
            'water' => '💧',
            'nature' => '🌿',
            'electric' => '⚡',
            'cosmic' => '✨',
            'wind' => '🌪️',
            default => '⭐',
        };
    }
}
