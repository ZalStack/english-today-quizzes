<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Quiz;

class ReportController extends Controller
{
    public function index()
    {
        $totalQuizzes = Quiz::count();
        $totalAttempts = \App\Models\UserQuizAttempt::where('status', 'completed')->count();
        $averageScore = \App\Models\UserQuizAttempt::where('status', 'completed')->avg('score') ?? 0;
        $passingRate = $totalAttempts > 0
            ? round(\App\Models\UserQuizAttempt::where('status', 'completed')->where('score', '>=', 70)->count() / $totalAttempts * 100, 1)
            : 0;

        $quizzes = Quiz::withCount('attempts')->paginate(10);

        return view('hr.reports.index', compact('quizzes', 'totalQuizzes', 'totalAttempts', 'averageScore', 'passingRate'));
    }

    public function show(Quiz $quiz)
    {
        $attempts = $quiz->attempts()->with('user.division')->where('status', 'completed')->paginate(20);

        $allAttempts = $quiz->attempts()->where('status', 'completed');
        $statistics = [
            'total_attempts' => $allAttempts->count(),
            'average_score' => round($allAttempts->avg('score') ?? 0, 2),
            'highest_score' => $allAttempts->max('score') ?? 0,
            'lowest_score' => $allAttempts->min('score') ?? 0,
            'passing_rate' => $allAttempts->where('score', '>=', 70)->count(),
        ];

        return view('hr.reports.show', compact('quiz', 'attempts', 'statistics'));
    }

    public function export(Quiz $quiz)
    {
        $attempts = $quiz->attempts()->with('user.division')->where('status', 'completed')->get();

        $filename = "quiz_report_{$quiz->title}_" . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($attempts) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Employee Name', 'Division', 'Score', 'Correct', 'Wrong', 'Completed At']);

            foreach ($attempts as $attempt) {
                fputcsv($file, [$attempt->user->full_name, $attempt->user->division?->name ?? 'N/A', $attempt->score, $attempt->total_correct, $attempt->total_wrong, $attempt->completed_at?->format('Y-m-d H:i:s')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
