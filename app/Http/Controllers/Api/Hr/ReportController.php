<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Traits\ApiResponseTrait;

class ReportController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $quizzes = Quiz::withCount('attempts')->get();
        return $this->success($quizzes);
    }

    public function show($quizId)
    {
        $quiz = Quiz::with(['attempts.user', 'attempts.answers'])->find($quizId);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }

        $attempts = $quiz->attempts;
        $totalAttempts = $attempts->count();
        $avgScore = $attempts->avg('score');
        $passing = $attempts->filter(function ($a) {
            return $a->score >= 70;
        })->count();

        return $this->success([
            'quiz' => $quiz,
            'total_attempts' => $totalAttempts,
            'average_score' => $avgScore,
            'passing_count' => $passing,
        ]);
    }

    public function export($quizId)
    {
        $quiz = Quiz::with(['attempts.user', 'attempts.answers.question'])->find($quizId);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }

        $data = [];
        foreach ($quiz->attempts as $attempt) {
            $data[] = [
                'user' => $attempt->user->name,
                'email' => $attempt->user->email,
                'score' => $attempt->score,
                'correct' => $attempt->total_correct,
                'wrong' => $attempt->total_wrong,
                'completed_at' => $attempt->completed_at,
            ];
        }
        return $this->success($data, 'Export data');
    }
}
