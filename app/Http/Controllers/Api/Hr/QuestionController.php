<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    use ApiResponseTrait;

    public function index($quizId)
    {
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }
        $questions = $quiz->questions()->orderBy('order_number')->get();
        return $this->success($questions);
    }

    public function store(Request $request, $quizId)
    {
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_image' => 'nullable|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'options' => 'nullable|array',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'nullable|integer',
            'order_number' => 'nullable|integer',
        ]);
        $validated['quiz_id'] = $quizId;
        $question = Question::create($validated);
        return $this->success($question, 'Question created', 201);
    }

    public function show($quizId, $questionId)
    {
        $question = Question::where('quiz_id', $quizId)->find($questionId);
        if (!$question) {
            return $this->error('Question not found', 404);
        }
        return $this->success($question);
    }

    public function update(Request $request, $quizId, $questionId)
    {
        $question = Question::where('quiz_id', $quizId)->find($questionId);
        if (!$question) {
            return $this->error('Question not found', 404);
        }
        $validated = $request->validate([
            'question_text' => 'sometimes|string',
            'question_image' => 'nullable|string',
            'question_type' => 'sometimes|in:multiple_choice,true_false,essay',
            'options' => 'nullable|array',
            'correct_answer' => 'sometimes|string',
            'explanation' => 'nullable|string',
            'points' => 'nullable|integer',
            'order_number' => 'nullable|integer',
        ]);
        $question->update($validated);
        return $this->success($question, 'Question updated');
    }

    public function destroy($quizId, $questionId)
    {
        $question = Question::where('quiz_id', $quizId)->find($questionId);
        if (!$question) {
            return $this->error('Question not found', 404);
        }
        $question->delete();
        return $this->success(null, 'Question deleted');
    }

    // Import from PDF (simulasi)
    public function importFromPdf(Request $request, $quizId)
    {
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 422, $validator->errors());
        }

        // Simulasi parsing PDF => data sementara
        $tempId = uniqid('import_');
        $extractedQuestions = [
            [
                'question_text' => 'Sample question 1 from PDF',
                'question_type' => 'multiple_choice',
                'options' => ['A', 'B', 'C', 'D'],
                'correct_answer' => 'A',
                'explanation' => 'Explanation 1',
                'points' => 1,
            ],
            [
                'question_text' => 'Sample question 2 from PDF',
                'question_type' => 'true_false',
                'options' => null,
                'correct_answer' => 'True',
                'explanation' => 'Explanation 2',
                'points' => 1,
            ],
        ];
        Cache::put($tempId, $extractedQuestions, now()->addMinutes(30));

        return $this->success([
            'temp_id' => $tempId,
            'questions' => $extractedQuestions,
            'total' => count($extractedQuestions),
        ], 'File processed, ready to confirm import');
    }

    public function confirmImport(Request $request, $quizId)
    {
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            return $this->error('Quiz not found', 404);
        }

        $validated = $request->validate([
            'temp_id' => 'required|string',
        ]);

        $questions = Cache::get($validated['temp_id']);
        if (!$questions) {
            return $this->error('Invalid or expired temp_id', 400);
        }

        foreach ($questions as $q) {
            $q['quiz_id'] = $quizId;
            Question::create($q);
        }

        Cache::forget($validated['temp_id']);

        return $this->success(null, 'Questions imported successfully');
    }

    public function cancelImport(Request $request, $quizId)
    {
        $validated = $request->validate([
            'temp_id' => 'required|string',
        ]);
        Cache::forget($validated['temp_id']);
        return $this->success(null, 'Import cancelled');
    }
}
