@extends('layouts.app')

@section('title', 'Edit Question')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-indigo-600 to-purple-600">
                <h2 class="text-2xl font-bold text-white">Edit Question</h2>
                <p class="text-indigo-100 mt-1">Update question for {{ $quiz->title }}</p>
            </div>

            <form action="{{ route('hr.quizzes.questions.update', [$quiz, $question]) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="question_type" class="block text-sm font-semibold text-gray-700 mb-2">Question Type</label>
                        <select name="question_type" id="question_type" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <option value="multiple_choice" {{ $question->question_type === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="true_false" {{ $question->question_type === 'true_false' ? 'selected' : '' }}>True / False</option>
                            <option value="short_answer" {{ $question->question_type === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                            <option value="essay" {{ $question->question_type === 'essay' ? 'selected' : '' }}>Essay</option>
                        </select>
                    </div>
                    <div>
                        <label for="points" class="block text-sm font-semibold text-gray-700 mb-2">Points</label>
                        <input type="number" name="points" id="points" value="{{ old('points', $question->points) }}" required min="1"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label for="order_number" class="block text-sm font-semibold text-gray-700 mb-2">Order</label>
                        <input type="number" name="order_number" id="order_number" value="{{ old('order_number', $question->order_number) }}" required min="1"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                </div>

                <div>
                    <label for="question_text" class="block text-sm font-semibold text-gray-700 mb-2">Question Text</label>
                    <textarea name="question_text" id="question_text" rows="4" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">{{ old('question_text', $question->question_text) }}</textarea>
                    @error('question_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="question_image" class="block text-sm font-semibold text-gray-700 mb-2">Question Image</label>
                    <input type="file" name="question_image" id="question_image" accept="image/*"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @if($question->question_image)
                        <p class="mt-1 text-sm text-gray-500">Current image uploaded</p>
                    @endif
                </div>

                <div id="optionsContainer" style="{{ $question->question_type === 'multiple_choice' ? '' : 'display: none;' }}">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Answer Options</label>
                    <div class="space-y-3">
                        @php
                            $options = $question->options ?? [];
                        @endphp
                        @for($i = 0; $i < 4; $i++)
                            <div class="flex items-center space-x-3">
                                <span class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-700 font-bold text-sm">
                                    {{ chr(65 + $i) }}
                                </span>
                                <input type="text" name="options[]" value="{{ $options[$i] ?? '' }}"
                                    class="flex-1 px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="Option {{ chr(65 + $i) }}">
                            </div>
                        @endfor
                    </div>
                </div>

                <div>
                    <label for="correct_answer" class="block text-sm font-semibold text-gray-700 mb-2">Correct Answer</label>
                    <input type="text" name="correct_answer" id="correct_answer" value="{{ old('correct_answer', $question->correct_answer) }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('correct_answer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="explanation" class="block text-sm font-semibold text-gray-700 mb-2">Explanation (Optional)</label>
                    <textarea name="explanation" id="explanation" rows="3"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">{{ old('explanation', $question->explanation) }}</textarea>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('hr.quizzes.questions.index', $quiz) }}"
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                        Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('question_type').addEventListener('change', function() {
        const optionsContainer = document.getElementById('optionsContainer');
        optionsContainer.style.display = this.value === 'multiple_choice' ? 'block' : 'none';
    });
</script>
@endpush
