<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'balance_after',
        'description',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label & ikon tipe transaksi (Bahasa Indonesia).
     */
    public function getTypeMetaAttribute(): array
    {
        return match ($this->type) {
            'welcome_pack' => ['label' => 'Paket Selamat Datang', 'icon' => '🎁'],
            'quiz_first_attempt' => ['label' => 'Kuis (percobaan pertama)', 'icon' => '🎯'],
            'video_submitted' => ['label' => 'Video Challenge', 'icon' => '🎬'],
            'daily_quest' => ['label' => 'Misi Harian', 'icon' => '⭐'],
            'shop_purchase' => ['label' => 'Belanja di Toko', 'icon' => '🛒'],
            default => ['label' => $this->type, 'icon' => '🪙'],
        };
    }
}
