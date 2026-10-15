<?php

namespace App\Services;

use App\Models\Pet;
use App\Models\PetLog;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserItem;
use App\Models\UserPet;
use App\Models\UserPetArchive;
use Illuminate\Support\Facades\DB;

class PetService
{
    /** Batas maksimal meteran keretakan telur. */
    public const CRACK_MAX = 100;

    /** Level minimal untuk naik tahap ke tahap berikutnya. */
    public const STAGE_LEVELS = [
        'baby' => 3,      // Bayi    → Remaja di level 3
        'juvenile' => 6,  // Remaja  → Dewasa di level 6
    ];

    /** Info tampilan tiap tahap hidup. */
    public const STAGE_META = [
        'egg' => ['label' => 'Telur', 'icon' => '🥚'],
        'baby' => ['label' => 'Bayi', 'icon' => '👶'],
        'juvenile' => ['label' => 'Remaja', 'icon' => '🧒'],
        'adult' => ['label' => 'Dewasa', 'icon' => '🧑'],
    ];

    /** Isi paket selamat datang untuk employee baru. */
    public const WELCOME_POINTS = 50;

    /**
     * Terisi saat terjadi kenaikan tahap pada request berjalan
     * (mis. dari baby → juvenile saat memberi makan).
     *
     * @var array{from: string, to: string, from_label: string, to_label: string, to_icon: string}|null
     */
    public ?array $lastStageUp = null;

    /**
     * Get or initialize the user's active egg/pet.
     */
    public function getOrCreateUserPet(User $user): UserPet
    {
        $userPet = UserPet::where('user_id', $user->id)->with('pet')->first();

        if (!$userPet) {
            $userPet = UserPet::create([
                'user_id' => $user->id,
                'pet_id' => null,
                'nickname' => null,
                'status' => 'egg',
                'life_stage' => 'egg',
                'egg_tier' => 'bronze',
                'generation' => 1,
                'growth_points' => 0,
                'crack_points' => 0,
                'max_growth_points' => 100,
                'level' => 1,
                'experience' => 0,
                'max_experience' => 100,
                'happiness' => 100,
                'energy' => 100,
                'total_hatched_count' => 0,
            ]);
            $userPet->load('pet');
        }

        $this->claimWelcomePackIfNeeded($user);

        return $userPet;
    }

    /**
     * Klaim paket selamat datang sekali seumur hidup:
     * 50 poin modal + Palu Kayu + Susu Botol.
     */
    public function claimWelcomePackIfNeeded(User $user): void
    {
        if ($user->welcome_pack_claimed_at !== null) {
            return;
        }

        // Hindari klaim ganda pada request yang sama
        if (User::whereKey($user->id)->value('welcome_pack_claimed_at') !== null) {
            $user->welcome_pack_claimed_at = now();
            return;
        }

        DB::transaction(function () use ($user) {
            app(PointService::class)->earn(
                $user,
                self::WELCOME_POINTS,
                'welcome_pack',
                'Paket Selamat Datang: modal poin pertamamu! 🎁'
            );

            $starterItems = [
                ['slug' => 'palu-kayu', 'qty' => 8],
                ['slug' => 'susu-botol', 'qty' => 2],
            ];

            foreach ($starterItems as $starter) {
                $item = ShopItem::where('slug', $starter['slug'])->first();
                if (!$item) {
                    continue;
                }
                $userItem = UserItem::firstOrCreate(
                    ['user_id' => $user->id, 'shop_item_id' => $item->id],
                    ['quantity' => 0]
                );
                $userItem->increment('quantity', $starter['qty']);
            }

            PetLog::create([
                'user_id' => $user->id,
                'growth_gained' => 0,
                'source_type' => 'welcome_pack',
                'description' => 'Menerima Paket Selamat Datang: +50 poin, Palu Kayu ×8, Susu Botol ×2 🎁',
            ]);

            $user->welcome_pack_claimed_at = now();
            $user->save();
        });
    }

    /**
     * Award pet EXP ketika peliharaan sudah menetas.
     * (Telur tidak lagi tumbuh dari tugas — retakan telur memakai alat dari toko.)
     */
    public function awardGrowth(User $user, int $amount, string $source, string $description = ''): UserPet
    {
        $userPet = $this->getOrCreateUserPet($user);

        // Fase telur (egg / ready_to_hatch): tidak ada EXP —
        // pertumbuhan telur memakai alat dari toko.
        if (!$userPet->isHatched()) {
            return $userPet;
        }

        DB::transaction(function () use ($user, $userPet, $amount, $source, $description) {
            PetLog::create([
                'user_id' => $user->id,
                'growth_gained' => $amount,
                'source_type' => $source,
                'description' => $description ?: "Mendapatkan +{$amount} EXP dari {$source}",
            ]);

            $newExp = $userPet->experience + $amount;
            $level = $userPet->level;
            $maxExp = $userPet->max_experience;

            while ($newExp >= $maxExp) {
                $newExp -= $maxExp;
                $level++;
                $maxExp = (int) round($maxExp * 1.25); // Scaling EXP
            }

            $userPet->level = $level;
            $userPet->experience = $newExp;
            $userPet->max_experience = $maxExp;
            $userPet->happiness = min(100, $userPet->happiness + 10);
            $userPet->save();

            $this->detectStageUp($user, $userPet);
        });

        return $userPet->fresh(['pet']);
    }

    /**
     * Deteksi & eksekusi kenaikan tahap (baby → juvenile → adult).
     */
    protected function detectStageUp(User $user, UserPet $userPet): void
    {
        if (!$userPet->isHatched()) {
            return;
        }

        $stage = $userPet->life_stage ?: 'baby';
        if ($stage === 'adult') {
            return;
        }

        $threshold = self::STAGE_LEVELS[$stage] ?? null;
        if ($threshold === null || $userPet->level < $threshold) {
            return;
        }

        $newStage = $stage === 'baby' ? 'juvenile' : 'adult';
        $fromLabel = self::STAGE_META[$stage]['label'];
        $toLabel = self::STAGE_META[$newStage]['label'];
        $toIcon = self::STAGE_META[$newStage]['icon'];

        $userPet->life_stage = $newStage;
        $userPet->save();

        PetLog::create([
            'user_id' => $user->id,
            'growth_gained' => 0,
            'source_type' => 'stage_up',
            'description' => "{$userPet->nickname} naik tahap: {$fromLabel} → {$toLabel} {$toIcon}!",
        ]);

        $this->lastStageUp = [
            'from' => $stage,
            'to' => $newStage,
            'from_label' => $fromLabel,
            'to_label' => $toLabel,
            'to_icon' => $toIcon,
        ];
    }

    /**
     * Retakkan telur dengan daya alat. Hanya untuk telur yang belum siap.
     */
    public function applyCrack(User $user, int $power, string $source = 'alat'): UserPet
    {
        $userPet = $this->getOrCreateUserPet($user);

        if (!$userPet->isEgg() || $userPet->isReadyToHatch()) {
            return $userPet;
        }

        $newCrack = min(self::CRACK_MAX, $userPet->crack_points + max(0, $power));
        $userPet->crack_points = $newCrack;

        if ($newCrack >= self::CRACK_MAX) {
            $userPet->status = 'ready_to_hatch';
        }

        $userPet->save();

        PetLog::create([
            'user_id' => $user->id,
            'growth_gained' => 0,
            'source_type' => 'tool_used',
            'description' => "Memecahkan telur memakai {$source} (+{$power} retak → {$newCrack}%)",
        ]);

        return $userPet->fresh(['pet']);
    }

    /**
     * Ketukan tangan kosong — retak kecil gratis.
     */
    public function tapCrack(User $user): UserPet
    {
        return $this->applyCrack($user, 1, 'tangan kosong');
    }

    /**
     * Hatch the egg and obtain a random pet based on rarity weights.
     */
    public function hatch(User $user, ?string $nickname = null): array
    {
        $userPet = $this->getOrCreateUserPet($user);

        if ($userPet->isHatched()) {
            return ['success' => false, 'message' => 'Pet kamu sudah menetas!'];
        }

        if (!$userPet->isEgg() && $userPet->status !== 'ready_to_hatch') {
            return ['success' => false, 'message' => 'Tidak ada telur untuk ditetaskan.'];
        }

        if (!$userPet->isReadyToHatch()) {
            $remaining = self::CRACK_MAX - $userPet->crack_points;
            return [
                'success' => false,
                'message' => "Telur belum cukup retan! Kurang {$remaining}% — pakai alat dari Toko 🔨",
            ];
        }

        $rolledPet = $this->rollRandomPet();

        if (!$rolledPet) {
            // Fallback pet if table was empty
            $rolledPet = Pet::firstOrCreate(
                ['species' => 'Wise Owl'],
                [
                    'name' => 'Barnaby',
                    'rarity' => 'common',
                    'element' => 'nature',
                    'description' => 'A calm and wise companion.',
                    'color_primary' => '#8D6E63',
                    'color_secondary' => '#D7CCC8',
                    'model_preset' => 'owl',
                    'base_power' => 12,
                ]
            );
        }

        $userPet->update([
            'pet_id' => $rolledPet->id,
            'nickname' => $nickname ?: $rolledPet->name,
            'status' => 'hatched',
            'life_stage' => 'baby',
            'hatched_at' => now(),
            'total_hatched_count' => $userPet->total_hatched_count + 1,
            'level' => 1,
            'experience' => 0,
            'max_experience' => 100,
            'happiness' => 100,
            'energy' => 100,
        ]);

        PetLog::create([
            'user_id' => $user->id,
            'growth_gained' => 50,
            'source_type' => 'hatch_event',
            'description' => "Telur generasi ke-{$userPet->generation} menetas menjadi {$rolledPet->species} ({$rolledPet->rarity})! 🐣",
        ]);

        $userPet->load('pet');

        return [
            'success' => true,
            'user_pet' => $userPet,
            'pet' => $rolledPet,
            'rarity' => $rolledPet->rarity,
            'life_stage' => 'baby',
            'message' => "Selamat! Telurmu menetas menjadi {$rolledPet->species} ({$rolledPet->rarity}) — tahap Bayi 👶!",
        ];
    }

    /**
     * Feed the pet pakai makanan termurah yang tersedia di inventaris.
     */
    public function feed(User $user): array
    {
        $userPet = $this->getOrCreateUserPet($user);

        if (!$userPet->isHatched()) {
            return ['success' => false, 'message' => 'Telur belum menetas! Pecahkan telur dulu dengan alat dari Toko 🔨'];
        }

        // Pilih makanan termurah yang masih tersisa
        $userItem = UserItem::where('user_id', $user->id)
            ->where('quantity', '>', 0)
            ->whereHas('shopItem', fn ($q) => $q->where('category', 'food')->where('is_active', true))
            ->with('shopItem')
            ->get()
            ->sortBy('shopItem.price')
            ->first();

        if (!$userItem) {
            return [
                'success' => false,
                'message' => 'Stok makanan habis 🍽️ — beli makanan di Toko agar peliharaanmu tumbuh!',
            ];
        }

        $result = $this->feedWithEffects(
            $user,
            (int) $userItem->shopItem->effect_exp,
            (int) $userItem->shopItem->effect_happiness,
            (int) $userItem->shopItem->effect_energy,
            "{$userItem->shopItem->name} {$userItem->shopItem->icon}"
        );

        if (!$result['success']) {
            return $result;
        }

        $userItem->decrement('quantity');
        if ($userItem->quantity <= 0) {
            $userItem->delete();
        }

        $result['quantity_left'] = max(0, (int) $userItem->quantity);

        return $result;
    }

    /**
     * Terapkan efek makanan pada peliharaan (dipakai ShopService & feed()).
     *
     * @return array{success: bool, message: string, exp_gained: int, stage_up: ?array, level: int, experience: int, max_experience: int, happiness: int, energy: int, life_stage: string}
     */
    public function feedWithEffects(User $user, int $exp, int $happiness, int $energy, string $label): array
    {
        $userPet = $this->getOrCreateUserPet($user);

        if (!$userPet->isHatched()) {
            return ['success' => false, 'message' => 'Belum ada peliharaan yang bisa diberi makan — pecahkan telurmu dulu!'];
        }

        $this->lastStageUp = null;

        DB::transaction(function () use ($user, $userPet, $exp, $happiness, $energy, $label) {
            $newExp = $userPet->experience + max(0, $exp);
            $level = $userPet->level;
            $maxExp = $userPet->max_experience;

            while ($newExp >= $maxExp) {
                $newExp -= $maxExp;
                $level++;
                $maxExp = (int) round($maxExp * 1.25);
            }

            $userPet->level = $level;
            $userPet->experience = $newExp;
            $userPet->max_experience = $maxExp;
            $userPet->happiness = min(100, $userPet->happiness + max(0, $happiness));
            $userPet->energy = min(100, $userPet->energy + max(0, $energy));
            $userPet->last_fed_at = now();
            $userPet->save();

            PetLog::create([
                'user_id' => $user->id,
                'growth_gained' => (int) $exp,
                'source_type' => 'pet_fed',
                'description' => "Memberi makan {$label} → +{$exp} EXP",
            ]);

            $this->detectStageUp($user, $userPet);
        });

        $userPet->refresh();

        return [
            'success' => true,
            'message' => "{$userPet->nickname} makan dengan lahap! (+{$exp} EXP)",
            'exp_gained' => (int) $exp,
            'stage_up' => $this->lastStageUp,
            'level' => (int) $userPet->level,
            'experience' => (int) $userPet->experience,
            'max_experience' => (int) $userPet->max_experience,
            'happiness' => (int) $userPet->happiness,
            'energy' => (int) $userPet->energy,
            'life_stage' => $userPet->life_stage,
        ];
    }

    /**
     * Pet / Stroke the creature.
     */
    public function pet(User $user): array
    {
        $userPet = $this->getOrCreateUserPet($user);

        if ($userPet->isEgg()) {
            // Tapping the egg gives a tiny encouragement
            $userPet->happiness = min(100, $userPet->happiness + 5);
            $userPet->save();
            return [
                'success' => true,
                'user_pet' => $userPet,
                'message' => 'Telur bergoyang hangat saat kamu mengelusnya!',
            ];
        }

        $userPet->happiness = min(100, $userPet->happiness + 15);
        $userPet->last_petted_at = now();
        $userPet->save();

        return [
            'success' => true,
            'user_pet' => $userPet->fresh(['pet']),
            'message' => "{$userPet->nickname} melompat gembira dan menyukaimu! (+15 Happiness)",
        ];
    }

    /**
     * Berkembang biak: pet dewasa menghasilkan telur generasi berikutnya,
     * pet lama diarsipkan ke Koleksi (Hall of Fame).
     *
     * @return array{success: bool, message: string, generation: int, archived: ?array, user_pet: ?UserPet}
     */
    public function breed(User $user): array
    {
        $userPet = $this->getOrCreateUserPet($user)->load('pet');

        if (!$userPet->isHatched() || !$userPet->isAdult()) {
            return [
                'success' => false,
                'message' => 'Hanya peliharaan Dewasa yang bisa bertelur! Rawat dulu hingga dewasa 🧑',
            ];
        }

        $archived = null;

        DB::transaction(function () use ($user, $userPet, &$archived) {
            $pet = $userPet->pet;

            $archived = UserPetArchive::create([
                'user_id' => $user->id,
                'pet_id' => $userPet->pet_id,
                'nickname' => $userPet->nickname,
                'species' => $pet?->species,
                'rarity' => $pet?->rarity ?? 'common',
                'element' => $pet?->element ?? 'nature',
                'model_preset' => $pet?->model_preset ?? 'fox',
                'color_primary' => $pet?->color_primary,
                'color_secondary' => $pet?->color_secondary,
                'generation' => (int) $userPet->generation,
                'final_level' => (int) $userPet->level,
                'hatched_at' => $userPet->hatched_at,
            ]);

            $newGeneration = (int) $userPet->generation + 1;

            $userPet->update([
                'pet_id' => null,
                'nickname' => null,
                'status' => 'egg',
                'life_stage' => 'egg',
                'generation' => $newGeneration,
                'crack_points' => 0,
                'growth_points' => 0,
                'level' => 1,
                'experience' => 0,
                'max_experience' => 100,
                'happiness' => 100,
                'energy' => 100,
                'hatched_at' => null,
                'last_fed_at' => null,
                'last_petted_at' => null,
            ]);

            PetLog::create([
                'user_id' => $user->id,
                'growth_gained' => 0,
                'source_type' => 'breed_event',
                'description' => "{$archived->nickname} (Gen {$archived->generation}, Lv{$archived->final_level}) bertelur — telur generasi ke-{$newGeneration} baru di sarang! 🥚",
            ]);
        });

        $userPet->refresh();

        return [
            'success' => true,
            'message' => "Telur generasi ke-{$userPet->generation} baru saja diletakkan di sarang! 🥚",
            'generation' => (int) $userPet->generation,
            'archived' => $archived,
            'user_pet' => $userPet,
        ];
    }

    /**
     * Koleksi / Hall of Fame pet yang sudah diarsipkan.
     */
    public function archive(User $user)
    {
        return UserPetArchive::where('user_id', $user->id)
            ->latest()
            ->get();
    }

    /**
     * Reset to get a new egg (jalur lama, dipertahankan untuk kompatibilitas).
     */
    public function newEgg(User $user): UserPet
    {
        $userPet = $this->getOrCreateUserPet($user);
        $userPet->update([
            'pet_id' => null,
            'nickname' => null,
            'status' => 'egg',
            'life_stage' => 'egg',
            'crack_points' => 0,
            'growth_points' => 0,
            'level' => 1,
            'experience' => 0,
            'happiness' => 100,
            'energy' => 100,
        ]);

        return $userPet->fresh(['pet']);
    }

    /**
     * Roll a random pet using weighted gacha probabilities.
     */
    private function rollRandomPet(): ?Pet
    {
        $roll = mt_rand(1, 100);

        if ($roll <= 3) {
            $rarity = 'legendary'; // 3%
        } elseif ($roll <= 15) {
            $rarity = 'epic'; // 12%
        } elseif ($roll <= 40) {
            $rarity = 'rare'; // 25%
        } else {
            $rarity = 'common'; // 60%
        }

        $pet = Pet::where('rarity', $rarity)->inRandomOrder()->first();

        // Fallback to any pet if specific rarity not seeded
        if (!$pet) {
            $pet = Pet::inRandomOrder()->first();
        }

        return $pet;
    }
}
