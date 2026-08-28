<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class QuizController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $quizzes = Quiz::with(['category', 'creator'])->get();
        return $this->success($quizzes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'category_id' => 'required|exists:quiz_categories,id',
            'duration' => 'nullable|integer',
            'total_questions' => 'nullable|integer',
            'status' => 'sometimes|in:draft,active,completed',
            'enroll_key' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'show_score' => 'boolean',
            'show_correct_answer' => 'boolean',
            'show_wrong_answer' => 'boolean',
            'show_explanation' => 'boolean',
        ]);
        $validated['created_by'] = auth()->id();
        $quiz = Quiz::create($validated);
        return $this->success($quiz, 'Quiz created', 201);
    }

    public function show($id)
    {
        $quiz = Quiz::with(['category', 'creator', 'questions'])->find($id);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }
        return $this->success($quiz);
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::find($id);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'category_id' => 'required|exists:quiz_categories,id',
            'duration' => 'nullable|integer',
            'total_questions' => 'nullable|integer',
            'status' => 'sometimes|in:draft,active,completed',
            'enroll_key' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'show_score' => 'boolean',
            'show_correct_answer' => 'boolean',
            'show_wrong_answer' => 'boolean',
            'show_explanation' => 'boolean',
        ]);
        $quiz->update($validated);
        return $this->success($quiz, 'Quiz updated');
    }

    public function destroy($id)
    {
        $quiz = Quiz::find($id);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }
        $quiz->delete();
        return $this->success(null, 'Quiz deleted');
    }
}
