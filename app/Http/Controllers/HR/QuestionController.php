<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Quiz $quiz)
    {
        $questions = $quiz->questions()->orderBy('order_number')->paginate(20);
        return view('hr.questions.index', compact('quiz', 'questions'));
    }

    public function create(Quiz $quiz)
    {
        $nextOrder = $quiz->questions()->max('order_number') + 1;
        return view('hr.questions.create', compact('quiz', 'nextOrder'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'options' => 'required_if:question_type,multiple_choice|nullable|array',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'order_number' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('question_image')) {
            $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
        }

        $validated['quiz_id'] = $quiz->id;

        if ($validated['question_type'] === 'true_false') {
            $validated['options'] = ['True', 'False'];
        }

        Question::create($validated);

        // Update total questions count
        $quiz->update([
            'total_questions' => $quiz->questions()->count()
        ]);

        return redirect()->route('hr.quizzes.questions.index', $quiz)
            ->with('success', 'Question added successfully.');
    }

    public function edit(Quiz $quiz, Question $question)
    {
        return view('hr.questions.edit', compact('quiz', 'question'));
    }

    public function update(Request $request, Quiz $quiz, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'options' => 'required_if:question_type,multiple_choice|nullable|array',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'order_number' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('question_image')) {
            if ($question->question_image) {
                \Storage::disk('public')->delete($question->question_image);
            }
            $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
        }

        $question->update($validated);

        return redirect()->route('hr.quizzes.questions.index', $quiz)
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        if ($question->question_image) {
            \Storage::disk('public')->delete($question->question_image);
        }

        $question->delete();

        $quiz->update([
            'total_questions' => $quiz->questions()->count()
        ]);

        return redirect()->route('hr.quizzes.questions.index', $quiz)
            ->with('success', 'Question deleted successfully.');
    }
}
