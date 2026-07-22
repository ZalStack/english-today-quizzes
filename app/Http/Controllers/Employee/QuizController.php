<?php
// app/Http/Controllers/Employee/QuizController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\UserQuizAttempt;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function join()
    {
        return view('employee.quizzes.join');
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'enroll_key' => 'required|string',
        ]);

        // ✅ PERBAIKAN: Cari quiz dengan enroll_key yang sesuai DAN status active
        $quiz = Quiz::where('enroll_key', $request->enroll_key)
            ->where('status', 'active')
            ->first();

        if (!$quiz) {
            return back()->with('error', 'Invalid enrollment key or quiz is not available.');
        }

        // ✅ PERBAIKAN: Cek apakah employee sudah pernah mengikuti quiz ini (completed)
        $completedAttempt = UserQuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'completed')
            ->first();

        if ($completedAttempt) {
            return redirect()->route('employee.quizzes.result', $completedAttempt->id)
                ->with('info', 'You have already completed this quiz.');
        }

        // Check if already enrolled (attempt in progress)
        $existingAttempt = UserQuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('employee.quizzes.take', $existingAttempt->id);
        }

        // ✅ PERBAIKAN: Redirect ke preview dengan quiz
        return redirect()->route('employee.quizzes.preview', $quiz->id);
    }

    public function preview(Quiz $quiz)
    {
        // ✅ PERBAIKAN: Cek apakah quiz active
        if ($quiz->status !== 'active') {
            return redirect()->route('employee.quizzes.join')
                ->with('error', 'This quiz is not available.');
        }

        // Check if user already completed this quiz
        $completedAttempt = UserQuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'completed')
            ->first();

        if ($completedAttempt) {
            return redirect()->route('employee.quizzes.result', $completedAttempt->id)
                ->with('info', 'You have already completed this quiz.');
        }

        return view('employee.quizzes.preview', compact('quiz'));
    }

    public function start(Quiz $quiz)
    {
        // ✅ PERBAIKAN: Cek apakah quiz active
        if ($quiz->status !== 'active') {
            return redirect()->route('employee.quizzes.join')
                ->with('error', 'This quiz is not available.');
        }

        // Check if user already completed this quiz
        $completedAttempt = UserQuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'completed')
            ->first();

        if ($completedAttempt) {
            return redirect()->route('employee.quizzes.result', $completedAttempt->id)
                ->with('info', 'You have already completed this quiz.');
        }

        // Cek attempt yang sedang berjalan
        $existingAttempt = UserQuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereIn('status', ['in_progress', 'pending_review'])
            ->first();

        if ($existingAttempt) {
            return redirect()->route('employee.quizzes.take', $existingAttempt->id);
        }

        // ✅ PERBAIKAN: Cek apakah quiz memiliki questions
        if ($quiz->questions()->count() === 0) {
            return redirect()->route('employee.quizzes.join')
                ->with('error', 'This quiz has no questions yet.');
        }

        // Buat attempt baru
        $attempt = UserQuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'ends_at' => now()->addMinutes($quiz->duration),
            'status' => 'in_progress',
        ]);

        return redirect()->route('employee.quizzes.take', $attempt->id);
    }

    public function take(UserQuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('employee.quizzes.result', $attempt->id);
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->questions()->orderByRaw("FIELD(question_type, 'multiple_choice', 'true_false', 'short_answer', 'essay')")->orderBy('order_number')->get();

        // Check if time is up - use ends_at from attempt, fallback to started_at + duration
        $endTime = $attempt->ends_at ?? $attempt->started_at->copy()->addMinutes($quiz->duration);

        if (now()->greaterThan($endTime)) {
            $this->submitQuiz($attempt);
            return redirect()->route('employee.quizzes.result', $attempt->id)
                ->with('info', 'Time is up! Your quiz has been submitted automatically.');
        }

        $remainingSeconds = max(0, (int) now()->diffInSeconds($endTime, false));
        $answeredQuestionIds = UserAnswer::where('attempt_id', $attempt->id)
            ->pluck('question_id')
            ->toArray();

        return view('employee.quizzes.take', compact('attempt', 'quiz', 'questions', 'remainingSeconds', 'answeredQuestionIds'));
    }

    public function saveAnswer(Request $request, UserQuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required',
        ]);

        $question = Question::find($request->question_id);

        $isCorrect = false;
        if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
            $isCorrect = strtolower(trim($request->answer)) === strtolower(trim($question->correct_answer));
        } elseif ($question->question_type === 'short_answer') {
            $isCorrect = strtolower(trim($request->answer)) === strtolower(trim($question->correct_answer));
        } elseif ($question->question_type === 'essay') {
            // Essay always not automatically correct
            $isCorrect = false;
        }

        UserAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $request->question_id,
            ],
            [
                'answer' => $request->answer,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function submit(UserQuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        $this->submitQuiz($attempt);

        return redirect()->route('employee.quizzes.result', $attempt->id)
            ->with('success', 'Quiz submitted successfully!');
    }

    private function submitQuiz(UserQuizAttempt $attempt)
    {
        $attempt->load('answers');
        $answers = $attempt->answers;
        $totalPoints = $attempt->quiz->questions()->sum('points');
        $earnedPoints = $answers->sum('points_earned');

        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;

        $attempt->update([
            'completed_at' => now(),
            'score' => $score,
            'total_correct' => $answers->where('is_correct', true)->count(),
            'total_wrong' => $answers->where('is_correct', false)->count(),
            'status' => 'completed',
        ]);
    }

    public function unansweredCount(UserQuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        $totalQuestions = $attempt->quiz->questions()->count();
        $answeredQuestions = UserAnswer::where('attempt_id', $attempt->id)->count();
        $unanswered = $totalQuestions - $answeredQuestions;

        return response()->json([
            'unanswered' => $unanswered,
            'total' => $totalQuestions,
        ]);
    }

    public function result(UserQuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        $attempt->load(['quiz', 'answers.question']);

        return view('employee.quizzes.result', compact('attempt'));
    }

    public function history()
    {
        $attempts = UserQuizAttempt::where('user_id', auth()->id())
            ->with('quiz.category')
            ->latest()
            ->paginate(10);

        return view('employee.quizzes.history', compact('attempts'));
    }
}
