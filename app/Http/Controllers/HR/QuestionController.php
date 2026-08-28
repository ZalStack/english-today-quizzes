<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Services\PdfQuestionParser;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * ✅ Hitung distribusi poin otomatis (total max 100)
     */
    private function calculatePointsDistribution(int $totalQuestions, int $maxPoints = 100): array
    {
        if ($totalQuestions <= 0) return [];
        
        $basePoints = floor($maxPoints / $totalQuestions);
        $remainder = $maxPoints % $totalQuestions;
        
        $distribution = [];
        for ($i = 0; $i < $totalQuestions; $i++) {
            // Soal awal dapat +1 poin sampai sisa habis
            $distribution[] = $basePoints + ($i < $remainder ? 1 : 0);
        }
        
        return $distribution;
    }

    /**
     * ✅ Recalculate poin semua soal dalam quiz agar total = 100
     */
    private function recalculateQuizPoints(Quiz $quiz, int $maxPoints = 100): void
    {
        $questions = $quiz->questions()->orderBy('order_number')->get();
        $totalQuestions = $questions->count();
        
        if ($totalQuestions === 0) return;
        
        $distribution = $this->calculatePointsDistribution($totalQuestions, $maxPoints);
        
        foreach ($questions as $index => $question) {
            $question->update(['points' => $distribution[$index]]);
        }
    }

    public function index(Quiz $quiz)
    {
        $quiz->load('questions'); // ✅ Load questions untuk sum points
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
            'points' => 'nullable|integer|min:1', // ✅ nullable, akan di-calculate otomatis
            'order_number' => 'nullable|integer|min:1',
            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        if ($request->hasFile('question_image')) {
            $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
        }

        $validated['quiz_id'] = $quiz->id;

        if ($validated['question_type'] === 'true_false') {
            $validated['options'] = ['True', 'False'];
        }

        // Auto-set order_number jika tidak ada
        if (empty($validated['order_number'])) {
            $validated['order_number'] = ($quiz->questions()->max('order_number') ?? 0) + 1;
        }

        // Auto-set points jika tidak ada (temporary, akan di-recalculate)
        if (empty($validated['points'])) {
            $validated['points'] = 1;
        }

        Question::create($validated);

        // ✅ Recalculate semua poin agar total = 100
        $this->recalculateQuizPoints($quiz, 100);

        $quiz->update([
            'total_questions' => $quiz->questions()->count()
        ]);

        return redirect()->route('hr.quizzes.questions.index', $quiz)
            ->with('success', 'Question added successfully. Total points: ' . $quiz->questions()->sum('points') . '/100');
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
            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
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

        // ✅ Recalculate semua poin agar total = 100
        $this->recalculateQuizPoints($quiz, 100);

        return redirect()->route('hr.quizzes.questions.index', $quiz)
            ->with('success', 'Question deleted successfully. Total points: ' . $quiz->questions()->sum('points') . '/100');
    }

    public function importFromPdf(Request $request, Quiz $quiz)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $file = $request->file('pdf_file');
            $filePath = $file->getPathname();
            
            $parser = new PdfQuestionParser();
            $questions = $parser->parse($filePath);
            
            if (empty($questions)) {
                return back()->with('error', 'Tidak ada soal yang terdeteksi dari PDF. Pastikan format sesuai panduan.');
            }
            
            session([
                'imported_questions' => $questions,
                'import_quiz_id' => $quiz->id,
                'show_import_preview' => true
            ]);
            
            $count = count($questions);
            
            return redirect()->route('hr.quizzes.questions.index', $quiz)
                ->with('success', "Berhasil mendeteksi {$count} soal dari PDF. Silakan review di bagian bawah halaman.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses PDF: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Confirm and save imported questions dengan auto-calculate points
     */
    public function confirmImport(Request $request, Quiz $quiz)
    {
        $editedQuestions = $request->input('questions', []);
        
        // Filter hanya yang di-check
        $questionsToImport = array_filter($editedQuestions, fn($q) => isset($q['import']));
        $totalToImport = count($questionsToImport);
        
        if ($totalToImport === 0) {
            return back()->with('error', 'Tidak ada soal yang dipilih untuk diimport.');
        }
        
        // ✅ Urutkan: Multiple Choice dulu, baru True/False, lalu Short Answer, Essay
        $typeOrder = ['multiple_choice' => 1, 'true_false' => 2, 'short_answer' => 3, 'essay' => 4];
        uasort($questionsToImport, function($a, $b) use ($typeOrder) {
            $orderA = $typeOrder[$a['question_type']] ?? 99;
            $orderB = $typeOrder[$b['question_type']] ?? 99;
            return $orderA <=> $orderB;
        });
        
        // ✅ Hitung distribusi poin otomatis (total max 100)
        $pointsDistribution = $this->calculatePointsDistribution($totalToImport, 100);
        
        // Hitung order_number awal
        $startOrder = $quiz->questions()->max('order_number') ?? 0;
        
        $count = 0;
        $index = 0;
        
        foreach ($questionsToImport as $qData) {
            $options = !empty($qData['options']) ? array_filter($qData['options'], fn($o) => !empty(trim($o))) : null;
            
            if ($qData['question_type'] === 'true_false') {
                $options = ['True', 'False'];
            }
            
            // ✅ Gunakan poin dari distribusi otomatis
            $autoPoints = $pointsDistribution[$index];
            
            // User bisa override jika mau (optional)
            $userPoints = isset($qData['points']) && (int) $qData['points'] > 0 
                ? (int) $qData['points'] 
                : $autoPoints;
            
            Question::create([
                'quiz_id' => $quiz->id,
                'question_type' => $qData['question_type'],
                'question_text' => $qData['question_text'],
                'options' => $options,
                'correct_answer' => $qData['correct_answer'],
                'points' => $userPoints,
                'order_number' => $startOrder + $index + 1,
                'explanation' => !empty($qData['explanation']) ? $qData['explanation'] : null,
            ]);
            
            $count++;
            $index++;
        }
        
        $quiz->update([
            'total_questions' => $quiz->questions()->count()
        ]);
        
        session()->forget(['imported_questions', 'import_quiz_id', 'show_import_preview']);
        
        return redirect()->route('hr.quizzes.questions.index', $quiz)
            ->with('success', "Berhasil mengimport {$count} soal. Total poin: " . $quiz->questions()->sum('points') . "/100");
    }

    public function cancelImport(Quiz $quiz)
    {
        session()->forget([
            'imported_questions',
            'import_quiz_id',
            'show_import_preview'
        ]);
        
        return redirect()->route('hr.quizzes.questions.index', $quiz)
            ->with('info', 'Import dibatalkan.');
    }
}