<?php

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

        $quiz = Quiz::where('enroll_key', $request->enroll_key)
            ->where('status', 'active')
            ->first();

        if (!$quiz) {
            return back()->with('error', 'Invalid enrollment key or quiz is not available.');
        }

        // Check if already enrolled
        $existingAttempt = UserQuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('employee.quizzes.start', $existingAttempt->id);
        }

        return redirect()->route('employee.quizzes.preview', $quiz->id);
    }

    public function preview(Quiz $quiz)
    {
        return view('employee.quizzes.preview', compact('quiz'));
    }

    public function start(Quiz $quiz)
    {
        // Check for existing in-progress attempt
        $attempt = UserQuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            $attempt = UserQuizAttempt::create([
                'user_id' => auth()->id(),
                'quiz_id' => $quiz->id,
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
        }

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
        $questions = $quiz->questions()->orderBy('order_number')->get();

        // Check if time is up
        $startTime = Carbon::parse($attempt->started_at);
        $endTime = $startTime->addMinutes($quiz->duration);

        if (now()->greaterThan($endTime)) {
            $this->submitQuiz($attempt);
            return redirect()->route('employee.quizzes.result', $attempt->id)
                ->with('info', 'Time is up! Your quiz has been submitted automatically.');
        }

        $remainingSeconds = now()->diffInSeconds($endTime, false);

        return view('employee.quizzes.take', compact('attempt', 'quiz', 'questions', 'remainingSeconds'));
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
