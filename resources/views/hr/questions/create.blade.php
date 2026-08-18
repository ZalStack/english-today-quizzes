@extends('layouts.hr')

@section('title', 'Add Question - ' . $quiz->title)
@section('header-title', 'Add Question')
@section('header-subtitle', 'Add a new question to ' . $quiz->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Questions
    </a>
</div>

<div class="max-w-4xl">
    <div class="hr-card overflow-hidden">
        {{-- Tabs --}}
        <div class="border-b border-slate-100/80" x-data="{ tab: 'manual' }">
            <div class="flex">
                <button @click="tab = 'manual'" :class="tab === 'manual' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-6 py-3 text-sm font-semibold border-b-2 transition-all duration-200">
                    Manual Input
                </button>
                <button @click="tab = 'pdf'" :class="tab === 'pdf' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-6 py-3 text-sm font-semibold border-b-2 transition-all duration-200">
                    Import from PDF
                </button>
            </div>

            {{-- Manual Tab --}}
            <div x-show="tab === 'manual'" class="p-6 sm:p-8">
                <form action="{{ route('hr.quizzes.questions.store', $quiz) }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="question_type" class="block text-sm font-semibold text-slate-700 mb-2">Question Type</label>
                            <select name="question_type" id="question_type" class="hr-input">
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="true_false">True/False</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>
                        <div>
                            <label for="order_number" class="block text-sm font-semibold text-slate-700 mb-2">Order Number</label>
                            <input type="number" name="order_number" id="order_number" value="{{ old('order_number', $nextOrder ?? 1) }}" min="1" class="hr-input">
                        </div>
                    </div>

                    <div>
                        <label for="question_text" class="block text-sm font-semibold text-slate-700 mb-2">Question Text</label>
                        <textarea name="question_text" id="question_text" rows="3" class="hr-input" placeholder="Enter the question text" required>{{ old('question_text') }}</textarea>
                        @error('question_text')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="question_image" class="block text-sm font-semibold text-slate-700 mb-2">Question Image (optional)</label>
                        <input type="file" name="question_image" id="question_image" accept="image/*" class="hr-input">
                    </div>

                    <div id="options-container" class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-700">Options</label>
                        <div class="flex items-center gap-3">
                            <span class="hr-avatar w-8 h-8 text-xs">A</span>
                            <input type="text" name="options[]" class="flex-1 hr-input" placeholder="Option A">
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="hr-avatar w-8 h-8 text-xs">B</span>
                            <input type="text" name="options[]" class="flex-1 hr-input" placeholder="Option B">
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="hr-avatar w-8 h-8 text-xs">C</span>
                            <input type="text" name="options[]" class="flex-1 hr-input" placeholder="Option C">
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="hr-avatar w-8 h-8 text-xs">D</span>
                            <input type="text" name="options[]" class="flex-1 hr-input" placeholder="Option D">
                        </div>
                    </div>

                    <div>
                        <label for="correct_answer" class="block text-sm font-semibold text-slate-700 mb-2">Correct Answer</label>
                        <textarea name="correct_answer" id="correct_answer" rows="2" class="hr-input" placeholder="Enter the correct answer" required>{{ old('correct_answer') }}</textarea>
                        @error('correct_answer')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="explanation" class="block text-sm font-semibold text-slate-700 mb-2">Explanation (optional)</label>
                        <textarea name="explanation" id="explanation" rows="2" class="hr-input" placeholder="Explain the correct answer">{{ old('explanation') }}</textarea>
                    </div>

                    <div>
                        <label for="points" class="block text-sm font-semibold text-slate-700 mb-2">Points</label>
                        <input type="number" name="points" id="points" value="{{ old('points', 10) }}" min="1" class="hr-input w-32">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100/80">
                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="hr-btn-secondary">Cancel</a>
                        <button type="submit" class="hr-btn-primary">Add Question</button>
                    </div>
                </form>
            </div>

            {{-- PDF Import Tab --}}
            <div x-show="tab === 'pdf'" class="p-6 sm:p-8">
                <form action="{{ route('hr.quizzes.questions.import', $quiz) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div class="bg-primary-50/80 border border-primary-200/80 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-primary-800">
                            <p class="font-semibold">PDF Import</p>
                            <p>Upload a PDF file containing questions. The system will attempt to parse them automatically.</p>
                        </div>
                    </div>

                    <div>
                        <label for="pdf_file" class="block text-sm font-semibold text-slate-700 mb-2">Select PDF File</label>
                        <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" required class="hr-input">
                        @error('pdf_file')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100/80">
                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="hr-btn-secondary">Cancel</a>
                        <button type="submit" class="hr-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Import from PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
