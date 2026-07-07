<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with(['category', 'creator'])
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->paginate(10);
        return view('hr.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $categories = QuizCategory::all();
        return view('hr.quizzes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:quiz_categories,id',
            'duration' => 'required|integer|min:1',
            'enroll_key' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'show_score' => 'boolean',
            'show_correct_answer' => 'boolean',
            'show_wrong_answer' => 'boolean',
            'show_explanation' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        Quiz::create($validated);

        return redirect()->route('hr.quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load(['category', 'questions' => function($query) {
            $query->orderBy('order_number');
        }, 'attempts.user']);

        return view('hr.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $categories = QuizCategory::all();
        return view('hr.quizzes.edit', compact('quiz', 'categories'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:quiz_categories,id',
            'duration' => 'required|integer|min:1',
            'enroll_key' => 'nullable|string|max:50',
            'status' => 'required|in:draft,active,completed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'show_score' => 'boolean',
            'show_correct_answer' => 'boolean',
            'show_wrong_answer' => 'boolean',
            'show_explanation' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($quiz->thumbnail) {
                \Storage::disk('public')->delete($quiz->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $quiz->update($validated);

        return redirect()->route('hr.quizzes.index')
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->attempts()->count() > 0) {
            return back()->with('error', 'Cannot delete quiz with existing attempts.');
        }

        if ($quiz->thumbnail) {
            \Storage::disk('public')->delete($quiz->thumbnail);
        }

        $quiz->delete();

        return redirect()->route('hr.quizzes.index')
            ->with('success', 'Quiz deleted successfully.');
    }
}
