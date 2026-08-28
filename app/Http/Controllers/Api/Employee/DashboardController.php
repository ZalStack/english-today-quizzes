<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use App\Models\VideoChallenge;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $userId = auth()->id();

        $availableQuizzes = Quiz::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->count();

        $attempts = UserQuizAttempt::where('user_id', $userId)->count();
        $completed = UserQuizAttempt::where('user_id', $userId)->where('status', 'completed')->count();
        $averageScore = UserQuizAttempt::where('user_id', $userId)->where('status', 'completed')->avg('score');

        $videoChallenges = VideoChallenge::where('is_active', true)->count();

        return $this->success([
            'available_quizzes' => $availableQuizzes,
            'total_attempts' => $attempts,
            'completed_quizzes' => $completed,
            'average_score' => round($averageScore ?? 0, 2),
            'active_video_challenges' => $videoChallenges,
        ]);
    }
}
