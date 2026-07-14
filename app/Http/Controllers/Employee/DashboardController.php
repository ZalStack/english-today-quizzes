<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get available quizzes (active and not attempted by user)
        $availableQuizzes = Quiz::where('status', 'active')
            ->whereDoesntHave('attempts', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        // Get ongoing quizzes (in progress)
        $ongoingQuizzes = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->with('quiz')
            ->get();

        // Get recent scores (completed)
        $recentScores = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('quiz')
            ->latest('completed_at')
            ->take(5)
            ->get();

        // Statistics
        $statistics = [
            'total_quizzes_taken' => UserQuizAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'average_score' => UserQuizAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->avg('score') ?? 0,
            'highest_score' => UserQuizAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->max('score') ?? 0,
        ];

        return view('employee.dashboard', compact(
            'availableQuizzes',
            'ongoingQuizzes',
            'recentScores',
            'statistics'
        ));
    }
}
