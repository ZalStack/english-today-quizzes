<?php

namespace App\Services;

use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Support\Collection;

class ShopService
{
    protected PetService $petService;
    protected PointService $pointService;

    public function __construct(PetService $petService, PointService $pointService)
    {
        $this->petService = $petService;
        $this->pointService = $pointService;
    }

    /**
     * Katalog toko aktif, diurutkan per kategori.
     */
    public function catalog(): Collection
    {
        return ShopItem::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Inventaris user (item dengan sisa stok > 0), beserta meta shop item.
     */
    public function inventory(User $user): Collection
    {
        return UserItem::where('user_id', $user->id)
            ->where('quantity', '>', 0)
            ->with('shopItem')
            ->get()
            ->sortBy('shopItem.sort_order')
            ->values();
    }

    /**
     * Alat milik user yang masih berfungsi (untuk pemecah telur).
     */
    public function tools(User $user): Collection
    {
        return $this->inventory($user)
            ->filter(fn ($item) => $item->shopItem && $item->shopItem->isTool())
            ->values();
    }

    /**
     * Makanan milik user yang masih tersisa.
     */
    public function foods(User $user): Collection
    {
        return $this->inventory($user)
            ->filter(fn ($item) => $item->shopItem && $item->shopItem->isFood())
            ->values();
    }

    /**
     * Beli item toko. Sukses: stok bertambah (item->uses per pembelian).
     *
     * @return array{success: bool, message: string, balance: int, quantity_left: int}
     */
    public function buy(User $user, ShopItem $item): array
    {
        if (!$item->is_active) {
            return ['success' => false, 'message' => 'Item tidak lagi tersedia.', 'balance' => (int) $user->points, 'quantity_left' => 0];
        }

        $spend = $this->pointService->spend(
            $user,
            $item->price,
            'shop_purchase',
            "Membeli {$item->name} ({$item->icon}) seharga {$item->price} poin"
        );

        if (!$spend['success']) {
            return [
                'success' => false,
                'message' => $spend['message'],
                'balance' => $spend['balance'],
                'quantity_left' => 0,
            ];
        }

        $userItem = UserItem::firstOrCreate(
            ['user_id' => $user->id, 'shop_item_id' => $item->id],
            ['quantity' => 0]
        );
        $userItem->increment('quantity', max(1, $item->uses));
        $userItem->refresh();

        return [
            'success' => true,
            'message' => "Berhasil membeli {$item->name} {$item->icon}! (+{$userItem->quantity} stok)",
            'balance' => $spend['balance'],
            'quantity_left' => (int) $userItem->quantity,
        ];
    }

    /**
     * Pakai alat pecah telur pada telur milik user.
     *
     * @return array{success: bool, message: string, power: int, quantity_left: int, crack_points: int, crack_percentage: int, status: string, ready_to_hatch: bool, tool_name: string}
     */
    public function useTool(User $user, ShopItem $item): array
    {
        if (!$item->isTool()) {
            return ['success' => false, 'message' => 'Item ini bukan alat pecah telur.'];
        }

        $userItem = UserItem::where('user_id', $user->id)
            ->where('shop_item_id', $item->id)
            ->where('quantity', '>', 0)
            ->first();

        if (!$userItem) {
            return ['success' => false, 'message' => "Stok {$item->name} habis — beli lagi di Toko 🛒"];
        }

        $userPet = $this->petService->getOrCreateUserPet($user);

        if ($userPet->isReadyToHatch()) {
            return ['success' => false, 'message' => 'Telur sudah 100% retak — pecahkan sekarang! 🎉'];
        }

        if (!$userPet->isEgg()) {
            return ['success' => false, 'message' => 'Telur sudah menetas — tidak bisa dipukul lagi!'];
        }

        $userItem->decrement('quantity');
        if ($userItem->quantity <= 0) {
            $userItem->delete();
        }

        $userPet = $this->petService->applyCrack($user, (int) $item->power, "alat: {$item->name}");

        return [
            'success' => true,
            'message' => "Pukul dengan {$item->name} {$item->icon}! (+{$item->power} retak)",
            'power' => (int) $item->power,
            'quantity_left' => max(0, (int) $userItem->quantity),
            'crack_points' => (int) $userPet->crack_points,
            'crack_percentage' => $userPet->crack_percentage,
            'status' => $userPet->status,
            'ready_to_hatch' => $userPet->isReadyToHatch(),
            'tool_name' => $item->name,
        ];
    }

    /**
     * Pakai makanan untuk peliharaan (menaikkan EXP/happiness/energy).
     *
     * @return array{success: bool, message: string, quantity_left: int, exp_gained: int, stage_up: ?array}
     */
    public function useFood(User $user, ShopItem $item): array
    {
        if (!$item->isFood()) {
            return ['success' => false, 'message' => 'Item ini bukan makanan.'];
        }

        $userItem = UserItem::where('user_id', $user->id)
            ->where('shop_item_id', $item->id)
            ->where('quantity', '>', 0)
            ->first();

        if (!$userItem) {
            return ['success' => false, 'message' => "Stok {$item->name} habis — beli lagi di Toko 🛒"];
        }

        $result = $this->petService->feedWithEffects(
            $user,
            (int) $item->effect_exp,
            (int) $item->effect_happiness,
            (int) $item->effect_energy,
            "{$item->name} {$item->icon}"
        );

        if (!$result['success']) {
            return $result;
        }

        $userItem->decrement('quantity');
        if ($userItem->quantity <= 0) {
            $userItem->delete();
        }

        return [
            'success' => true,
            'message' => $result['message'],
            'quantity_left' => max(0, (int) $userItem->quantity),
            'exp_gained' => $result['exp_gained'] ?? 0,
            'stage_up' => $result['stage_up'] ?? null,
            'level' => $result['level'] ?? 1,
            'experience' => $result['experience'] ?? 0,
            'max_experience' => $result['max_experience'] ?? 100,
            'happiness' => $result['happiness'] ?? 0,
            'energy' => $result['energy'] ?? 0,
            'life_stage' => $result['life_stage'] ?? 'baby',
        ];
    }
}
