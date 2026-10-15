<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'icon',
        'price',
        'power',
        'uses',
        'effect_exp',
        'effect_happiness',
        'effect_energy',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer',
        'power' => 'integer',
        'uses' => 'integer',
        'effect_exp' => 'integer',
        'effect_happiness' => 'integer',
        'effect_energy' => 'integer',
    ];

    public function userItems()
    {
        return $this->hasMany(UserItem::class);
    }

    public function isTool(): bool
    {
        return $this->category === 'tool';
    }

    public function isFood(): bool
    {
        return $this->category === 'food';
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->isTool() ? 'Alat Pecah Telur' : 'Makanan Perawatan';
    }

    /**
     * Ringkasan efek untuk kartu toko (Bahasa Indonesia).
     */
    public function getEffectSummaryAttribute(): string
    {
        if ($this->isTool()) {
            return "+{$this->power} retak · {$this->uses} kali pakai";
        }

        $parts = [];
        if ($this->effect_exp > 0) {
            $parts[] = "+{$this->effect_exp} EXP";
        }
        if ($this->effect_happiness > 0) {
            $parts[] = "+{$this->effect_happiness} happiness";
        }
        if ($this->effect_energy > 0) {
            $parts[] = "+{$this->effect_energy} energy";
        }

        return implode(' · ', $parts) ?: 'Perawatan special';
    }
}
