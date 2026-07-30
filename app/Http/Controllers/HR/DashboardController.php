<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuizAttempt;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $totalDivisions = Division::count();
        $totalQuizzes   = Quiz::count();
        $activeQuizzes  = Quiz::where('status', 'active')->count();
        $completedQuizzes = Quiz::where('status', 'completed')->count();

        $totalParticipants = UserQuizAttempt::distinct('user_id')->count('user_id');

        $recentAttempts = UserQuizAttempt::with(['user', 'quiz'])
            ->latest('created_at')
            ->take(8)
            ->get();

        $leaderboard = User::where('role', 'employee')
            ->with('division')
            ->withCount(['quizAttempts as completed_quizzes_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['quizAttempts as total_score' => function ($q) {
                $q->where('status', 'completed');
            }], 'score')
            ->having('completed_quizzes_count', '>', 0)
            ->orderByDesc('total_score')
            ->take(10)
            ->get();

        return view('hr.dashboard', compact(
            'totalEmployees',
            'totalDivisions',
            'totalQuizzes',
            'activeQuizzes',
            'completedQuizzes',
            'totalParticipants',
            'recentAttempts',
            'leaderboard'
        ));
    }
}
