<?php

namespace Database\Seeders;

use App\Models\ShopItem;
use Illuminate\Database\Seeder;

class ShopItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ── Alat Pecah Telur ──────────────────────────────────────
            [
                'name' => 'Palu Kayu',
                'slug' => 'palu-kayu',
                'category' => 'tool',
                'description' => 'Palu kayu ringan. Lemah tapi murah — permulaan yang sempurna untuk memecahkan cangkang telur.',
                'icon' => '🔨',
                'price' => 15,
                'power' => 5,
                'uses' => 4,
                'sort_order' => 1,
            ],
            [
                'name' => 'Palu Perak',
                'slug' => 'palu-perak',
                'category' => 'tool',
                'description' => 'Palu berkepala perak yang lebih berat. Retakan lebih besar dalam sekali pukul!',
                'icon' => '🛠️',
                'price' => 40,
                'power' => 14,
                'uses' => 4,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pahat Batu',
                'slug' => 'pahat-batu',
                'category' => 'tool',
                'description' => 'Pahat tajam untuk mengupas cangkang telur pelan-pelan tapi pasti.',
                'icon' => '🪨',
                'price' => 65,
                'power' => 22,
                'uses' => 4,
                'sort_order' => 3,
            ],
            [
                'name' => 'Lampu Penetas',
                'slug' => 'lampu-petas',
                'category' => 'tool',
                'description' => 'Hangatkan dan lembutkan cangkang telur. Banyak sekali isi pakainya!',
                'icon' => '💡',
                'price' => 85,
                'power' => 18,
                'uses' => 8,
                'sort_order' => 4,
            ],
            [
                'name' => 'Bor Tangan',
                'slug' => 'bor-tangan',
                'category' => 'tool',
                'description' => 'Bor manual presisi tinggi. Melubangi cangkang dalam sekali tekan.',
                'icon' => '🔧',
                'price' => 110,
                'power' => 34,
                'uses' => 4,
                'sort_order' => 5,
            ],
            [
                'name' => 'Bor Turbo',
                'slug' => 'bor-turbo',
                'category' => 'tool',
                'description' => 'Bor berkecepatan tinggi berbahan bakar poin. Sangat powerful!',
                'icon' => '⚡',
                'price' => 160,
                'power' => 52,
                'uses' => 4,
                'sort_order' => 6,
            ],
            [
                'name' => 'Sinar Surya',
                'slug' => 'sinar-surya',
                'category' => 'tool',
                'description' => 'Sinar matahari terkonsentrasi — hampir langsung memecahkan telur sekaligus!',
                'icon' => '☀️',
                'price' => 230,
                'power' => 80,
                'uses' => 3,
                'sort_order' => 7,
            ],

            // ── Makanan Perawatan ──────────────────────────────────────
            [
                'name' => 'Susu Botol',
                'slug' => 'susu-botol',
                'category' => 'food',
                'description' => 'Susu hangat untuk bayi hewan peliharaanmu. Manis dan lembut.',
                'icon' => '🍶',
                'price' => 15,
                'effect_exp' => 30,
                'effect_happiness' => 8,
                'effect_energy' => 10,
                'sort_order' => 11,
            ],
            [
                'name' => 'Snack Wortel',
                'slug' => 'snack-wortel',
                'category' => 'food',
                'description' => 'Wortel renyah kesukaan semua hewan. Bikin ngemil tambah semangat!',
                'icon' => '🥕',
                'price' => 25,
                'effect_exp' => 50,
                'effect_happiness' => 12,
                'effect_energy' => 8,
                'sort_order' => 12,
            ],
            [
                'name' => 'Vitamin Kekuatan',
                'slug' => 'vitamin-kuat',
                'category' => 'food',
                'description' => 'Vitamin khusus agar peliharaanmu cepat tumbuh besar dan kuat.',
                'icon' => '💊',
                'price' => 45,
                'effect_exp' => 90,
                'effect_happiness' => 6,
                'effect_energy' => 15,
                'sort_order' => 13,
            ],
            [
                'name' => 'Kue Keberuntungan',
                'slug' => 'kue-hoki',
                'category' => 'food',
                'description' => 'Kue ajaib dengan bintang di atasnya. EXP besar + kebahagiaan melimpah!',
                'icon' => '🍰',
                'price' => 65,
                'effect_exp' => 130,
                'effect_happiness' => 20,
                'effect_energy' => 10,
                'sort_order' => 14,
            ],
            [
                'name' => 'Pakan Premium',
                'slug' => 'pakan-premium',
                'category' => 'food',
                'description' => 'Pakan super lengkap bergizi. Pertumbuhan tercepat menuju dewasa!',
                'icon' => '🍖',
                'price' => 95,
                'effect_exp' => 210,
                'effect_happiness' => 15,
                'effect_energy' => 25,
                'sort_order' => 15,
            ],
        ];

        foreach ($items as $item) {
            ShopItem::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
