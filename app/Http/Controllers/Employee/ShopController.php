<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ShopItem;
use App\Services\PetService;
use App\Services\PointService;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected ShopService $shopService;
    protected PetService $petService;
    protected PointService $pointService;

    public function __construct(ShopService $shopService, PetService $petService, PointService $pointService)
    {
        $this->shopService = $shopService;
        $this->petService = $petService;
        $this->pointService = $pointService;
    }

    /**
     * Halaman Toko: katalog alat & makanan + inventaris + saldo.
     */
    public function index()
    {
        $user = auth()->user();

        // Memastikan pet ada & welcome pack terklaim (sekali seumur hidup)
        $this->petService->getOrCreateUserPet($user);
        $user->refresh();

        $catalog = $this->shopService->catalog();

        return view('employee.shop.index', [
            'tools' => $catalog->filter(fn ($i) => $i->isTool())->values(),
            'foods' => $catalog->filter(fn ($i) => $i->isFood())->values(),
            'inventory' => $this->shopService->inventory($user),
            'balance' => (int) $user->points,
            'userPet' => $user->userPet()->with('pet')->first(),
        ]);
    }

    /**
     * Beli item (AJAX).
     */
    public function buy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|integer|exists:shop_items,id',
        ]);

        $item = ShopItem::findOrFail($validated['item_id']);
        $result = $this->shopService->buy(auth()->user(), $item);

        $result['item'] = [
            'id' => $item->id,
            'name' => $item->name,
            'icon' => $item->icon,
            'category' => $item->category,
        ];

        return response()->json($result);
    }

    /**
     * Pakai makanan dari inventaris toko (AJAX).
     */
    public function useFood(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|integer|exists:shop_items,id',
        ]);

        $item = ShopItem::findOrFail($validated['item_id']);

        if (!$item->isFood()) {
            return response()->json([
                'success' => false,
                'message' => 'Item ini bukan makanan — gunakan alat di halaman Pet Sanctuary 🥚',
            ]);
        }

        $result = $this->shopService->useFood(auth()->user(), $item);
        $result['item'] = [
            'id' => $item->id,
            'name' => $item->name,
            'icon' => $item->icon,
            'category' => $item->category,
        ];

        return response()->json($result);
    }

    /**
     * Riwayat poin (untuk modal).
     */
    public function pointsHistory(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'balance' => (int) $user->points,
            'transactions' => $this->pointService->history($user)->map(fn ($tx) => [
                'amount' => (int) $tx->amount,
                'type' => $tx->type,
                'type_label' => $tx->type_meta['label'],
                'type_icon' => $tx->type_meta['icon'],
                'balance_after' => (int) $tx->balance_after,
                'description' => $tx->description,
                'created_at' => $tx->created_at->diffForHumans(),
            ]),
        ]);
    }
}
