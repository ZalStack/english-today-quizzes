<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_id',
        'nickname',
        'status',
        'life_stage',
        'egg_tier',
        'generation',
        'growth_points',
        'crack_points',
        'max_growth_points',
        'level',
        'experience',
        'max_experience',
        'happiness',
        'energy',
        'total_hatched_count',
        'hatched_at',
        'last_fed_at',
        'last_petted_at',
    ];

    protected $casts = [
        'hatched_at' => 'datetime',
        'last_fed_at' => 'datetime',
        'last_petted_at' => 'datetime',
        'growth_points' => 'integer',
        'crack_points' => 'integer',
        'generation' => 'integer',
        'level' => 'integer',
        'experience' => 'integer',
        'max_experience' => 'integer',
        'happiness' => 'integer',
        'energy' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function isEgg(): bool
    {
        return $this->status === 'egg';
    }

    public function isReadyToHatch(): bool
    {
        return $this->status === 'ready_to_hatch' || ($this->status === 'egg' && $this->crack_points >= 100);
    }

    public function isHatched(): bool
    {
        return $this->status === 'hatched' && !is_null($this->pet_id);
    }

    public function isAdult(): bool
    {
        return $this->life_stage === 'adult';
    }

    public function getGrowthPercentageAttribute(): int
    {
        if ($this->max_growth_points <= 0) return 0;
        return min(100, (int) round(($this->growth_points / $this->max_growth_points) * 100));
    }

    /**
     * Meteran keretakan telur (0–100) — dipakai mekanik alat pecah telur.
     */
    public function getCrackPercentageAttribute(): int
    {
        return min(100, (int) $this->crack_points);
    }

    public function getExpPercentageAttribute(): int
    {
        if ($this->max_experience <= 0) return 0;
        return min(100, (int) round(($this->experience / $this->max_experience) * 100));
    }

    /**
     * Tahap visual retakan telur 0–4, kini berbasis crack_points.
     */
    public function getCrackStageAttribute(): int
    {
        if ($this->crack_points >= 100) return 4;
        if ($this->crack_points >= 75) return 3;
        if ($this->crack_points >= 50) return 2;
        if ($this->crack_points >= 25) return 1;
        return 0;
    }

    public function getLifeStageLabelAttribute(): string
    {
        return match ($this->life_stage) {
            'baby' => 'Bayi',
            'juvenile' => 'Remaja',
            'adult' => 'Dewasa',
            default => 'Telur',
        };
    }

    public function getLifeStageIconAttribute(): string
    {
        return match ($this->life_stage) {
            'baby' => '👶',
            'juvenile' => '🧒',
            'adult' => '🧑',
            default => '🥚',
        };
    }
}
