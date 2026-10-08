<?php

namespace Database\Seeders;

use App\Models\Pet;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        $pets = [
            // Common Pets (60% weight)
            [
                'name' => 'Barnaby',
                'species' => 'Wise Owl',
                'rarity' => 'common',
                'element' => 'nature',
                'description' => 'A calm and intelligent owl that loves reading grammar books and spotting vocabulary tricks.',
                'color_primary' => '#8D6E63',
                'color_secondary' => '#D7CCC8',
                'model_preset' => 'owl',
                'base_power' => 12,
            ],
            [
                'name' => 'Mochi',
                'species' => 'Study Cat',
                'rarity' => 'common',
                'element' => 'nature',
                'description' => 'A cozy cat that purrs happily whenever you submit quiz answers on time.',
                'color_primary' => '#FFB74D',
                'color_secondary' => '#FFF3E0',
                'model_preset' => 'cat',
                'base_power' => 10,
            ],
            [
                'name' => 'Pippin',
                'species' => 'Swift Otter',
                'rarity' => 'common',
                'element' => 'water',
                'description' => 'An energetic river otter who swims through complex sentences with playful ease.',
                'color_primary' => '#4FC3F7',
                'color_secondary' => '#B3E5FC',
                'model_preset' => 'otter',
                'base_power' => 14,
            ],

            // Rare Pets (25% weight)
            [
                'name' => 'Kitsune',
                'species' => 'Mystic Fox',
                'rarity' => 'rare',
                'element' => 'electric',
                'description' => 'A quick-witted fox with glowing ears that emits sparks of inspiration during difficult tests.',
                'color_primary' => '#FF7043',
                'color_secondary' => '#FFE082',
                'model_preset' => 'fox',
                'base_power' => 25,
            ],
            [
                'name' => 'Frosty',
                'species' => 'Crystal Penguin',
                'rarity' => 'rare',
                'element' => 'water',
                'description' => 'A charming arctic companion whose cool composure keeps your stress levels low.',
                'color_primary' => '#29B6F6',
                'color_secondary' => '#E1F5FE',
                'model_preset' => 'owl',
                'base_power' => 22,
            ],

            // Epic Pets (12% weight)
            [
                'name' => 'Ignis',
                'species' => 'Solar Phoenix',
                'rarity' => 'epic',
                'element' => 'fire',
                'description' => 'A legendary avian wrapped in blazing solar energy. Rises stronger from every incorrect answer.',
                'color_primary' => '#FF3D00',
                'color_secondary' => '#FFEA00',
                'model_preset' => 'phoenix',
                'base_power' => 50,
            ],
            [
                'name' => 'Aether',
                'species' => 'Shadow Wolf',
                'rarity' => 'epic',
                'element' => 'cosmic',
                'description' => 'A mysterious wolf imbued with cosmic starlight, howling whenever you achieve a high score.',
                'color_primary' => '#7E57C2',
                'color_secondary' => '#B388FF',
                'model_preset' => 'fox',
                'base_power' => 55,
            ],

            // Legendary Pets (3% weight)
            [
                'name' => 'Draco Cyberus',
                'species' => 'Cyber Dragon',
                'rarity' => 'legendary',
                'element' => 'cosmic',
                'description' => 'An ultra-rare mythical dragon radiating neon cybernetic power and supreme linguistic mastery.',
                'color_primary' => '#00E5FF',
                'color_secondary' => '#76FF03',
                'model_preset' => 'dragon',
                'base_power' => 100,
            ],
            [
                'name' => 'Aurelia',
                'species' => 'Golden Griffin',
                'rarity' => 'legendary',
                'element' => 'fire',
                'description' => 'An emperor-tier celestial beast made of pure golden brilliance. The guardian of top learners.',
                'color_primary' => '#FFD700',
                'color_secondary' => '#FFF8E1',
                'model_preset' => 'phoenix',
                'base_power' => 105,
            ],
        ];

        foreach ($pets as $pet) {
            Pet::updateOrCreate(
                ['species' => $pet['species']],
                $pet
            );
        }
    }
}
