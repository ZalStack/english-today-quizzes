<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuizAttempt;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $completedAttempts = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();

        $statistics = [
            'total_quizzes_taken' => $completedAttempts->count(),
            'average_score'       => $completedAttempts->avg('score') ?? 0,
            'highest_score'       => $completedAttempts->max('score') ?? 0,
        ];

        $takenQuizIds = UserQuizAttempt::where('user_id', $user->id)->pluck('quiz_id');

        $availableQuizzes = Quiz::where('status', 'active')
            ->whereNotIn('id', $takenQuizIds)
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        $ongoingQuizzes = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'ongoing')
            ->with('quiz')
            ->latest('started_at')
            ->get();

        $recentScores = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('quiz')
            ->latest('completed_at')
            ->take(5)
            ->get();

        $leaderboard = User::where('role', 'employee')
            ->withCount(['quizAttempts as completed_quizzes_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['quizAttempts as total_score' => function ($q) {
                $q->where('status', 'completed');
            }], 'score')
            ->having('completed_quizzes_count', '>', 0)
            ->orderByDesc('total_score')
            ->take(5)
            ->get();

        $currentUserRank = null;

        $allRanked = User::where('role', 'employee')
            ->withCount(['quizAttempts as completed_quizzes_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['quizAttempts as total_score' => function ($q) {
                $q->where('status', 'completed');
            }], 'score')
            ->having('completed_quizzes_count', '>', 0)
            ->orderByDesc('total_score')
            ->get();

        $position = $allRanked->search(fn ($u) => $u->id === $user->id);

        if ($position !== false) {
            $currentUserRank = [
                'rank'        => $position + 1,
                'total_score' => $allRanked[$position]->total_score ?? 0,
            ];
        }

        return view('employee.dashboard', compact(
            'statistics',
            'availableQuizzes',
            'ongoingQuizzes',
            'recentScores',
            'leaderboard',
            'currentUserRank'
        ));
    }
}
