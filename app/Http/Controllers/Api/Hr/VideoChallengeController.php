<?php
// app/Http/Controllers/Api/Hr/VideoChallengeController.php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\VideoChallenge;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class VideoChallengeController extends Controller
{
    use ApiResponseTrait;

    /**
     * GET /api/hr/video-challenges
     * Daftar semua video challenge + jumlah submission tiap challenge.
     */
    public function index()
    {
        $challenges = VideoChallenge::with('creator')
            ->withCount('submissions')
            ->latest()
            ->get();

        return $this->success($challenges);
    }

    /**
     * POST /api/hr/video-challenges
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $validated['is_active'] ?? true;

        $challenge = VideoChallenge::create($validated);

        return $this->success($challenge, 'Video challenge berhasil dibuat', 201);
    }

    /**
     * GET /api/hr/video-challenges/{videoChallenge}
     * Detail satu challenge lengkap dengan semua submission (siapa yang sudah submit + link videonya).
     */
    public function show(VideoChallenge $videoChallenge)
    {
        $videoChallenge->load(['creator', 'submissions.user']);

        return $this->success($videoChallenge);
    }

    /**
     * PUT/PATCH /api/hr/video-challenges/{videoChallenge}
     */
    public function update(Request $request, VideoChallenge $videoChallenge)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $videoChallenge->update($validated);

        return $this->success(
            $videoChallenge->fresh(['creator', 'submissions.user']),
            'Video challenge berhasil diupdate'
        );
    }

    /**
     * DELETE /api/hr/video-challenges/{videoChallenge}
     */
    public function destroy(VideoChallenge $videoChallenge)
    {
        $videoChallenge->delete();

        return $this->success(null, 'Video challenge berhasil dihapus');
    }
}
