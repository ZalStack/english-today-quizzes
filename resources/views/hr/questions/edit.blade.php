@extends('layouts.hr')

@section('title', 'Edit Question')
@section('header-title', 'Edit Question')
@section('header-subtitle', 'Update question information')

@section('content')
<div class="mb-6">
    <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Questions
    </a>
</div>

<div class="max-w-4xl">
    <div class="hr-card overflow-hidden">
        <form action="{{ route('hr.quizzes.questions.update', [$quiz, $question]) }}" method="POST" class="p-6 sm:p-8 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="question_type" class="block text-sm font-semibold text-slate-700 mb-2">Question Type</label>
                    <select name="question_type" id="question_type" class="hr-input">
                        <option value="multiple_choice" {{ $question->question_type === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                        <option value="short_answer" {{ $question->question_type === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                        <option value="true_false" {{ $question->question_type === 'true_false' ? 'selected' : '' }}>True/False</option>
                        <option value="essay" {{ $question->question_type === 'essay' ? 'selected' : '' }}>Essay</option>
                    </select>
                </div>
                <div>
                    <label for="order_number" class="block text-sm font-semibold text-slate-700 mb-2">Order Number</label>
                    <input type="number" name="order_number" id="order_number" value="{{ old('order_number', $question->order_number) }}" min="1" class="hr-input">
                </div>
            </div>

            <div>
                <label for="question_text" class="block text-sm font-semibold text-slate-700 mb-2">Question Text</label>
                <textarea name="question_text" id="question_text" rows="3" class="hr-input" required>{{ old('question_text', $question->question_text) }}</textarea>
                @error('question_text')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="question_image" class="block text-sm font-semibold text-slate-700 mb-2">Question Image (optional)</label>
                @if($question->question_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $question->question_image) }}" class="w-24 h-24 rounded-xl object-cover">
                    </div>
                @endif
                <input type="file" name="question_image" id="question_image" accept="image/*" class="hr-input">
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-semibold text-slate-700">Options</label>
                @if($question->options && is_array($question->options))
                    @foreach($question->options as $key => $option)
                        <div class="flex items-center gap-3">
                            <span class="hr-avatar w-8 h-8 text-xs">{{ chr(65 + $key) }}</span>
                            <input type="text" name="options[]" value="{{ $option }}" class="flex-1 hr-input">
                        </div>
                    @endforeach
                @else
                    @foreach(['A', 'B', 'C', 'D'] as $letter)
                        <div class="flex items-center gap-3">
                            <span class="hr-avatar w-8 h-8 text-xs">{{ $letter }}</span>
                            <input type="text" name="options[]" class="flex-1 hr-input" placeholder="Option {{ $letter }}">
                        </div>
                    @endforeach
                @endif
            </div>

            <div>
                <label for="correct_answer" class="block text-sm font-semibold text-slate-700 mb-2">Correct Answer</label>
                <textarea name="correct_answer" id="correct_answer" rows="2" class="hr-input" required>{{ old('correct_answer', $question->correct_answer) }}</textarea>
                @error('correct_answer')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="explanation" class="block text-sm font-semibold text-slate-700 mb-2">Explanation (optional)</label>
                <textarea name="explanation" id="explanation" rows="2" class="hr-input">{{ old('explanation', $question->explanation) }}</textarea>
            </div>

            <div>
                <label for="points" class="block text-sm font-semibold text-slate-700 mb-2">Points</label>
                <input type="number" name="points" id="points" value="{{ old('points', $question->points) }}" min="1" class="hr-input w-32">
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="hr-btn-secondary">Cancel</a>
                <button type="submit" class="hr-btn-primary">Update Question</button>
            </div>
        </form>
    </div>
</div>
@endsection
