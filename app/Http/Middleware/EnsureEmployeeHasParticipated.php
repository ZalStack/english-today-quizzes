<?php

namespace App\Http\Middleware;

use App\Services\PetService;
use Closure;
use Illuminate\Http\Request;

/**
 * Gerbang "case real" untuk Sanctuary Pet & Toko.
 *
 * Karyawan harus menyelesaikan minimal 1 kuis DAN mengumpulkan
 * 1 video challenge sebelum ekonomi poin / peliharaan terbuka.
 */
class EnsureEmployeeHasParticipated
{
    public function __construct(protected PetService $petService) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if ($user && $this->petService->hasParticipated($user)) {
            return $next($request);
        }

        // Request AJAX / JSON (mis. game canvas) → tolak dengan JSON.
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'locked' => true,
                'message' => 'Selesaikan minimal 1 kuis dan kumpulkan 1 video challenge dulu untuk membuka Sanctuary Pet & Toko! 📚🎬',
            ], 403);
        }

        // Request halaman web (mis. link Toko dari navbar) → arahkan ke gerbang.
        return redirect()->route('employee.pet.index')->with('sanctuary_locked', true);
    }
}
