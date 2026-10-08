<?php

namespace App\Services;

use App\Models\Pet;
use App\Models\PetLog;
use App\Models\User;
use App\Models\UserPet;
use Illuminate\Support\Facades\DB;

class PetService
{
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
                'egg_tier' => 'bronze',
                'growth_points' => 35, // Starter bonus progress
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

        return $userPet;
    }

    /**
     * Award growth points / EXP to user's pet when task is completed.
     */
    public function awardGrowth(User $user, int $amount, string $source, string $description = ''): UserPet
    {
        $userPet = $this->getOrCreateUserPet($user);

        DB::transaction(function () use ($user, $userPet, $amount, $source, $description) {
            PetLog::create([
                'user_id' => $user->id,
                'growth_gained' => $amount,
                'source_type' => $source,
                'description' => $description ?: "Mendapatkan +{$amount} EXP dari {$source}",
            ]);

            if ($userPet->isEgg()) {
                $newGrowth = $userPet->growth_points + $amount;
                if ($newGrowth >= $userPet->max_growth_points) {
                    $userPet->growth_points = $userPet->max_growth_points;
                    $userPet->status = 'ready_to_hatch';
                } else {
                    $userPet->growth_points = $newGrowth;
                }
                $userPet->save();
            } elseif ($userPet->isHatched()) {
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
            }
        });

        return $userPet->fresh(['pet']);
    }

    /**
     * Hatch the egg and obtain a random pet based on rarity weights.
     */
    public function hatch(User $user, ?string $nickname = null): array
    {
        $userPet = $this->getOrCreateUserPet($user);

        // If not ready, we allow force hatch for demo/fun or validate
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
            'description' => "Menetaskan telur dan mendapatkan {$rolledPet->species} ({$rolledPet->rarity})!",
        ]);

        $userPet->load('pet');

        return [
            'success' => true,
            'user_pet' => $userPet,
            'pet' => $rolledPet,
            'rarity' => $rolledPet->rarity,
            'message' => "Selamat! Telurmu menetas menjadi {$rolledPet->species} ({$rolledPet->rarity})!",
        ];
    }

    /**
     * Feed the pet.
     */
    public function feed(User $user): array
    {
        $userPet = $this->getOrCreateUserPet($user);

        if (!$userPet->isHatched()) {
            return ['success' => false, 'message' => 'Telur belum menetas! Rawat telur hingga siap menetas.'];
        }

        $userPet->happiness = min(100, $userPet->happiness + 20);
        $userPet->energy = min(100, $userPet->energy + 25);
        $userPet->experience = min($userPet->max_experience, $userPet->experience + 15);
        $userPet->last_fed_at = now();
        $userPet->save();

        return [
            'success' => true,
            'user_pet' => $userPet->fresh(['pet']),
            'message' => "{$userPet->nickname} sangat senang dan kenyang! (+15 EXP, +20 Happiness)",
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
