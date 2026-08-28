<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use App\Models\UserAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class QuizController extends Controller
{
    use ApiResponseTrait;

    public function join()
    {
        return $this->success(['message' => 'Provide enroll_key to enroll']);
    }

    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'enroll_key' => 'required|string',
        ]);

        $quiz = Quiz::where('enroll_key', $validated['enroll_key'])->first();
        if (!$quiz) {
            return $this->error('Invalid enroll key', 404);
        }

        return $this->success([
            'quiz' => $quiz,
            'message' => 'Enrollment successful. You can now start the quiz.'
        ]);
    }

    public function preview($quizId)
    {
        $quiz = Quiz::with(['category', 'questions'])->find($quizId);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }
        return $this->success($quiz);
    }

    public function start(Request $request, $quizId)
    {
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }

        if ($quiz->status !== 'active') {
            return $this->error('Quiz is not available', 403);
        }
        if ($quiz->start_date && $quiz->start_date > now()) {
            return $this->error('Quiz has not started yet', 403);
        }
        if ($quiz->end_date && $quiz->end_date < now()) {
            return $this->error('Quiz has ended', 403);
        }

        $attempt = UserQuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quizId,
            'started_at' => now(),
            'ends_at' => $quiz->duration ? now()->addMinutes($quiz->duration) : null,
            'status' => 'in_progress',
        ]);

        return $this->success([
            'attempt_id' => $attempt->id,
            'ends_at' => $attempt->ends_at,
        ], 'Quiz started');
    }

    public function take($attemptId)
    {
        $attempt = UserQuizAttempt::with(['quiz.questions' => function ($q) {
            $q->orderBy('order_number');
        }])->find($attemptId);

        if (!$attempt) {
            return $this->error('Attempt not found', 404);
        }

        $questions = $attempt->quiz->questions;
        $userAnswers = UserAnswer::where('attempt_id', $attemptId)->get()->keyBy('question_id');

        $data = $questions->map(function ($question) use ($userAnswers) {
            return [
                'question' => $question,
                'user_answer' => $userAnswers->get($question->id),
            ];
        });

        return $this->success([
            'attempt' => $attempt,
            'questions' => $data,
        ]);
    }

    public function saveAnswer(Request $request, $attemptId)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'nullable|string',
        ]);

        $attempt = UserQuizAttempt::find($attemptId);
        if (!$attempt) {
            return $this->error('Attempt not found', 404);
        }
        if ($attempt->status !== 'in_progress') {
            return $this->error('Attempt is not in progress', 403);
        }

        $question = Question::where('id', $validated['question_id'])->where('quiz_id', $attempt->quiz_id)->first();
        if (!$question) {
            return $this->error('Question does not belong to this quiz', 400);
        }

        $userAnswer = UserAnswer::updateOrCreate(
            [
                'attempt_id' => $attemptId,
                'question_id' => $validated['question_id'],
            ],
            [
                'answer' => $validated['answer'],
                'is_correct' => $this->checkAnswer($question, $validated['answer']),
                'points_earned' => $this->calculatePoints($question, $validated['answer']),
            ]
        );

        return $this->success($userAnswer, 'Answer saved');
    }

    public function submit($attemptId)
    {
        $attempt = UserQuizAttempt::with('answers.question')->find($attemptId);
        if (!$attempt) {
            return $this->error('Attempt not found', 404);
        }
        if ($attempt->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }
        if ($attempt->status !== 'in_progress') {
            return $this->error('Attempt already submitted', 403);
        }

        $totalCorrect = $attempt->answers->where('is_correct', true)->count();
        $totalWrong = $attempt->answers->where('is_correct', false)->count();
        $totalPoints = $attempt->quiz->questions()->sum('points');
        $earnedPoints = $attempt->answers->sum('points_earned');
        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;

        $attempt->update([
            'completed_at' => now(),
            'score' => $score,
            'total_correct' => $totalCorrect,
            'total_wrong' => $totalWrong,
            'status' => 'completed',
        ]);

        return $this->success([
            'attempt' => $attempt,
            'score' => $score,
            'correct' => $totalCorrect,
            'wrong' => $totalWrong,
        ], 'Quiz submitted');
    }

    public function unansweredCount($attemptId)
    {
        $attempt = UserQuizAttempt::with('quiz.questions')->find($attemptId);
        if (!$attempt) {
            return $this->error('Attempt not found', 404);
        }

        $totalQuestions = $attempt->quiz->questions->count();
        $answered = UserAnswer::where('attempt_id', $attemptId)->count();
        $unanswered = $totalQuestions - $answered;

        return $this->success([
            'total_questions' => $totalQuestions,
            'answered' => $answered,
            'unanswered' => $unanswered,
        ]);
    }

    public function result($attemptId)
    {
        $attempt = UserQuizAttempt::with(['quiz', 'answers.question', 'user'])->find($attemptId);
        if (!$attempt) {
            return $this->error('Attempt not found', 404);
        }
        return $this->success($attempt);
    }

    public function history(Request $request)
    {
        $attempts = UserQuizAttempt::where('user_id', auth()->id())
            ->with('quiz')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($attempts);
    }

    // Helpers
    private function checkAnswer($question, $answer)
    {
        return $question->correct_answer === $answer;
    }

    private function calculatePoints($question, $answer)
    {
        if ($this->checkAnswer($question, $answer)) {
            return $question->points ?? 1;
        }
        return 0;
    }
}
