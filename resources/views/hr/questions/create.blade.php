@extends('layouts.app')

@section('title', 'Add Question')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Add Question to {{ $quiz->title }}</h2>

                <form action="{{ route('hr.quizzes.questions.store', $quiz) }}" method="POST" enctype="multipart/form-data" id="questionForm">
                    @csrf

                    <div class="mb-4">
                        <label for="question_type" class="block text-sm font-medium text-gray-700">Question Type</label>
                        <select name="question_type" id="question_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Type</option>
                            <option value="multiple_choice" {{ old('question_type') === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="true_false" {{ old('question_type') === 'true_false' ? 'selected' : '' }}>True / False</option>
                            <option value="short_answer" {{ old('question_type') === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                            <option value="essay" {{ old('question_type') === 'essay' ? 'selected' : '' }}>Essay</option>
                        </select>
                        @error('question_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="question_text" class="block text-sm font-medium text-gray-700">Question Text</label>
                        <textarea name="question_text" id="question_text" rows="3" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('question_text') }}</textarea>
                        @error('question_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="question_image" class="block text-sm font-medium text-gray-700">Question Image (Optional)</label>
                        <input type="file" name="question_image" id="question_image" accept="image/*"
                            class="mt-1 block w-full">
                        @error('question_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="optionsContainer" class="mb-4" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                        <div id="optionsList">
                            <div class="flex items-center mb-2">
                                <span class="mr-2 text-sm font-semibold">A</span>
                                <input type="text" name="options[]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Option A">
                            </div>
                            <div class="flex items-center mb-2">
                                <span class="mr-2 text-sm font-semibold">B</span>
                                <input type="text" name="options[]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Option B">
                            </div>
                            <div class="flex items-center mb-2">
                                <span class="mr-2 text-sm font-semibold">C</span>
                                <input type="text" name="options[]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Option C">
                            </div>
                            <div class="flex items-center mb-2">
                                <span class="mr-2 text-sm font-semibold">D</span>
                                <input type="text" name="options[]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Option D">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="correct_answer" class="block text-sm font-medium text-gray-700">Correct Answer</label>
                        <input type="text" name="correct_answer" id="correct_answer" value="{{ old('correct_answer') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="For multiple choice, enter the correct option text">
                        @error('correct_answer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="explanation" class="block text-sm font-medium text-gray-700">Explanation (Optional)</label>
                        <textarea name="explanation" id="explanation" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('explanation') }}</textarea>
                        @error('explanation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="points" class="block text-sm font-medium text-gray-700">Points</label>
                            <input type="number" name="points" id="points" value="{{ old('points', 1) }}" required min="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('points')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="order_number" class="block text-sm font-medium text-gray-700">Order Number</label>
                            <input type="number" name="order_number" id="order_number" value="{{ old('order_number', $nextOrder) }}" required min="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('order_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Add Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('question_type').addEventListener('change', function() {
        const optionsContainer = document.getElementById('optionsContainer');
        const correctAnswer = document.getElementById('correct_answer');

        if (this.value === 'multiple_choice') {
            optionsContainer.style.display = 'block';
            correctAnswer.placeholder = 'Enter the correct option text (e.g., Option A text)';
        } else if (this.value === 'true_false') {
            optionsContainer.style.display = 'none';
            correctAnswer.placeholder = 'Enter True or False';
        } else {
            optionsContainer.style.display = 'none';
            correctAnswer.placeholder = 'Enter the correct answer';
        }
    });

    // Trigger on load
    document.getElementById('question_type').dispatchEvent(new Event('change'));
</script>
@endpush
