<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\UserPetArchive;
use App\Services\PetService;
use App\Services\PointService;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetController extends Controller
{
    protected PetService $petService;
    protected ShopService $shopService;
    protected PointService $pointService;

    public function __construct(PetService $petService, ShopService $shopService, PointService $pointService)
    {
        $this->petService = $petService;
        $this->shopService = $shopService;
        $this->pointService = $pointService;
    }

    /**
     * Display full-screen Pet Sanctuary view.
     */
    public function index()
    {
        $user = auth()->user();
        $userPet = $this->petService->getOrCreateUserPet($user);
        $archive = $this->petService->archive($user);

        return view('employee.pet.index', [
            'userPet' => $userPet,
            'archive' => $archive,
            'balance' => (int) $user->points,
            'tools' => $this->shopService->tools($user),
            'foods' => $this->shopService->foods($user),
        ]);
    }

    /**
     * Get current status JSON for 3D Game Canvas.
     */
    public function getStatus(): JsonResponse
    {
        $user = auth()->user();
        $userPet = $this->petService->getOrCreateUserPet($user);

        $nextStageLevel = null;
        if ($userPet->isHatched() && $userPet->life_stage !== 'adult') {
            $nextStageLevel = PetService::STAGE_LEVELS[$userPet->life_stage] ?? null;
        }

        return response()->json([
            'success' => true,
            'status' => $userPet->status,
            'is_egg' => $userPet->isEgg(),
            'is_ready_to_hatch' => $userPet->isReadyToHatch(),
            'is_hatched' => $userPet->isHatched(),
            'life_stage' => $userPet->life_stage,
            'life_stage_label' => $userPet->life_stage_label,
            'life_stage_icon' => $userPet->life_stage_icon,
            'is_adult' => $userPet->isAdult(),
            'generation' => (int) $userPet->generation,
            'next_stage_level' => $nextStageLevel,
            'crack_points' => (int) $userPet->crack_points,
            'crack_percentage' => $userPet->crack_percentage,
            'crack_stage' => $userPet->crack_stage,
            'growth_points' => $userPet->growth_points,
            'max_growth_points' => $userPet->max_growth_points,
            'growth_percentage' => $userPet->growth_percentage,
            'level' => $userPet->level,
            'experience' => $userPet->experience,
            'max_experience' => $userPet->max_experience,
            'exp_percentage' => $userPet->exp_percentage,
            'happiness' => $userPet->happiness,
            'energy' => $userPet->energy,
            'nickname' => $userPet->nickname,
            'points' => (int) $user->points,
            'tools' => $this->shopService->tools($user)->map(fn ($item) => [
                'id' => $item->shop_item_id,
                'name' => $item->shopItem->name,
                'icon' => $item->shopItem->icon,
                'power' => (int) $item->shopItem->power,
                'quantity' => (int) $item->quantity,
            ])->values(),
            'foods' => $this->shopService->foods($user)->map(fn ($item) => [
                'id' => $item->shop_item_id,
                'name' => $item->shopItem->name,
                'icon' => $item->shopItem->icon,
                'quantity' => (int) $item->quantity,
            ])->values(),
            'archive_count' => UserPetArchive::where('user_id', $user->id)->count(),
            'pet' => $userPet->pet ? [
                'name' => $userPet->pet->name,
                'species' => $userPet->pet->species,
                'rarity' => $userPet->pet->rarity,
                'element' => $userPet->pet->element,
                'element_icon' => $userPet->pet->element_icon,
                'description' => $userPet->pet->description,
                'color_primary' => $userPet->pet->color_primary,
                'color_secondary' => $userPet->pet->color_secondary,
                'model_preset' => $userPet->pet->model_preset,
                'base_power' => $userPet->pet->base_power,
            ] : null,
        ]);
    }

    /**
     * Pukul telur dengan alat dari inventaris.
     */
    public function useTool(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|integer|exists:shop_items,id',
        ]);

        $item = \App\Models\ShopItem::findOrFail($validated['item_id']);
        $result = $this->shopService->useTool(auth()->user(), $item);

        return response()->json($result);
    }

    /**
     * Ketukan tangan kosong (retak +1, gratis tapi lambat).
     */
    public function crack(): JsonResponse
    {
        $userPet = $this->petService->tapCrack(auth()->user());

        return response()->json([
            'success' => $userPet->isEgg() || $userPet->isReadyToHatch(),
            'message' => $userPet->isReadyToHatch()
                ? 'Cangkang telur akhirnya retak sempurna! 🔨🎉'
                : 'Telur bergoyang... (+1 retak, pelan tapi pasti)',
            'crack_points' => (int) $userPet->crack_points,
            'crack_percentage' => $userPet->crack_percentage,
            'status' => $userPet->status,
            'ready_to_hatch' => $userPet->isReadyToHatch(),
        ]);
    }

    /**
     * Hatch the egg.
     */
    public function hatch(Request $request): JsonResponse
    {
        $nickname = $request->input('nickname');
        $result = $this->petService->hatch(auth()->user(), $nickname);
        return response()->json($result);
    }

    /**
     * Feed the pet (pakai makanan termurah di inventaris).
     */
    public function feed(): JsonResponse
    {
        $result = $this->petService->feed(auth()->user());

        if (isset($result['user_pet'])) {
            $result['user_pet'] = $result['user_pet']->only([
                'nickname', 'level', 'experience', 'max_experience', 'happiness', 'energy', 'life_stage',
            ]);
        }

        return response()->json($result);
    }

    /**
     * Pet / Stroke the pet or egg.
     */
    public function pet(): JsonResponse
    {
        $result = $this->petService->pet(auth()->user());

        if (isset($result['user_pet']) && is_object($result['user_pet'])) {
            $result['user_pet'] = $result['user_pet']->only(['nickname', 'happiness', 'energy', 'life_stage']);
        }

        return response()->json($result);
    }

    /**
     * Berkembang biak: pet dewasa → telur generasi berikutnya + arsip.
     */
    public function breed(): JsonResponse
    {
        $result = $this->petService->breed(auth()->user());

        return response()->json($result);
    }

    /**
     * Add test/dev growth points for instant testing.
     */
    public function addPoints(Request $request): JsonResponse
    {
        $amount = (int) $request->input('amount', 25);
        $userPet = $this->petService->awardGrowth(auth()->user(), $amount, 'bonus_task', 'Manual Bonus / Task Completion');

        return response()->json([
            'success' => true,
            'growth_points' => $userPet->growth_points,
            'growth_percentage' => $userPet->growth_percentage,
            'status' => $userPet->status,
            'level' => $userPet->level,
            'experience' => $userPet->experience,
            'message' => "+{$amount} EXP berhasil ditambahkan!",
        ]);
    }

    /**
     * Reset to get a new egg.
     */
    public function newEgg(): JsonResponse
    {
        $userPet = $this->petService->newEgg(auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'Telur baru telah diletakkan di sarang 3D kamu!',
            'life_stage' => $userPet->life_stage,
            'generation' => (int) $userPet->generation,
        ]);
    }
}
