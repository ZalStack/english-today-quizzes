<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Division;
use App\Models\UserQuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $totalDivisions = Division::count();
        $totalQuizzes = Quiz::count();
        $activeQuizzes = Quiz::where('status', 'active')->count();
        $completedQuizzes = Quiz::where('status', 'completed')->count();
        $totalParticipants = UserQuizAttempt::distinct('user_id')->count();

        $recentAttempts = UserQuizAttempt::with(['user', 'quiz'])
            ->latest()
            ->take(10)
            ->get();

        $quizStats = Quiz::withCount(['attempts as total_attempts'])
            ->withAvg('attempts as average_score', 'score')
            ->take(5)
            ->get();

        return view('hr.dashboard', compact(
            'totalEmployees',
            'totalDivisions',
            'totalQuizzes',
            'activeQuizzes',
            'completedQuizzes',
            'totalParticipants',
            'recentAttempts',
            'quizStats'
        ));
    }
}
