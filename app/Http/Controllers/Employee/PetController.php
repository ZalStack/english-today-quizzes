<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\PetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetController extends Controller
{
    protected PetService $petService;

    public function __construct(PetService $petService)
    {
        $this->petService = $petService;
    }

    /**
     * Display full-screen Pet Sanctuary view.
     */
    public function index()
    {
        $userPet = $this->petService->getOrCreateUserPet(auth()->user());
        return view('employee.pet.index', compact('userPet'));
    }

    /**
     * Get current status JSON for 3D Game Canvas.
     */
    public function getStatus(): JsonResponse
    {
        $userPet = $this->petService->getOrCreateUserPet(auth()->user());

        return response()->json([
            'success' => true,
            'status' => $userPet->status,
            'is_egg' => $userPet->isEgg(),
            'is_ready_to_hatch' => $userPet->isReadyToHatch(),
            'is_hatched' => $userPet->isHatched(),
            'growth_points' => $userPet->growth_points,
            'max_growth_points' => $userPet->max_growth_points,
            'growth_percentage' => $userPet->growth_percentage,
            'crack_stage' => $userPet->crack_stage,
            'level' => $userPet->level,
            'experience' => $userPet->experience,
            'max_experience' => $userPet->max_experience,
            'exp_percentage' => $userPet->exp_percentage,
            'happiness' => $userPet->happiness,
            'energy' => $userPet->energy,
            'nickname' => $userPet->nickname,
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
     * Hatch the egg.
     */
    public function hatch(Request $request): JsonResponse
    {
        $nickname = $request->input('nickname');
        $result = $this->petService->hatch(auth()->user(), $nickname);
        return response()->json($result);
    }

    /**
     * Feed the pet.
     */
    public function feed(): JsonResponse
    {
        $result = $this->petService->feed(auth()->user());
        return response()->json($result);
    }

    /**
     * Pet / Stroke the pet or egg.
     */
    public function pet(): JsonResponse
    {
        $result = $this->petService->pet(auth()->user());
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
            'message' => "+{$amount} Growth Points berhasil ditambahkan!",
        ]);
    }

    /**
     * Reset to get a new egg.
     */
    public function newEgg(): JsonResponse
    {
        $userPet = $this->petService->getOrCreateUserPet(auth()->user());
        $userPet->update([
            'pet_id' => null,
            'nickname' => null,
            'status' => 'egg',
            'growth_points' => 10,
            'level' => 1,
            'experience' => 0,
            'happiness' => 100,
            'energy' => 100,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Telur baru telah diletakkan di sarang 3D kamu!',
        ]);
    }
}
