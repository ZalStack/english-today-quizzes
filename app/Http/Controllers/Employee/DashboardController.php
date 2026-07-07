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

        $availableQuizzes = Quiz::where('status', 'active')
            ->whereDoesntHave('attempts', function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'completed');
            })
            ->with('category')
            ->get();

        $ongoingQuizzes = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->with('quiz.category')
            ->get();

        $completedQuizzes = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('quiz.category')
            ->latest('completed_at')
            ->take(10)
            ->get();

        $recentScores = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest('completed_at')
            ->take(5)
            ->get();

        $statistics = [
            'total_quizzes_taken' => UserQuizAttempt::where('user_id', $user->id)->count(),
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
            'completedQuizzes',
            'recentScores',
            'statistics'
        ));
    }
}
