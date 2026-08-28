<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\VideoChallenge;
use App\Models\VideoSubmission;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class VideoChallengeController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $challenges = VideoChallenge::where('is_active', true)->with('submissions')->get();
        return $this->success($challenges);
    }

    public function submit(Request $request, $challengeId)
    {
        $validated = $request->validate([
            'link' => 'required|url',
            'notes' => 'nullable|string',
        ]);

        $challenge = VideoChallenge::find($challengeId);
        if (!$challenge) {
            return $this->error('Challenge not found', 404);
        }

        $existing = VideoSubmission::where('challenge_id', $challengeId)
            ->where('user_id', auth()->id())
            ->first();
        if ($existing) {
            return $this->error('You have already submitted for this challenge', 409);
        }

        $submission = VideoSubmission::create([
            'challenge_id' => $challengeId,
            'user_id' => auth()->id(),
            'link' => $validated['link'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return $this->success($submission, 'Submission successful', 201);
    }
}
