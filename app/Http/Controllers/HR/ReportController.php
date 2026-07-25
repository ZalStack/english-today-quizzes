<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Quiz;

class ReportController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::withCount('attempts')->paginate(10); // atau 15

        return view('hr.reports.index', compact('quizzes'));
    }

    public function show(Quiz $quiz)
    {
        $attempts = $quiz->attempts()->with('user.division')->where('status', 'completed')->paginate(20);

        $statistics = [
            'total_attempts' => $attempts->total(),
            'average_score' => $attempts->avg('score'),
            'highest_score' => $attempts->max('score'),
            'lowest_score' => $attempts->min('score'),
            'passing_rate' => $attempts->where('score', '>=', 70)->count(),
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
