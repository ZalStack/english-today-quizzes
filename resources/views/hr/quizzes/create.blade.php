@extends('layouts.app')

@section('title', 'Create Quiz')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-indigo-600 to-purple-600">
                <h2 class="text-2xl font-bold text-white">Create New Quiz</h2>
                <p class="text-indigo-100 mt-1">Set up a new assessment for your employees</p>
            </div>

            <form action="{{ route('hr.quizzes.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Quiz Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="e.g., English Grammar Test - Level 1">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Brief description about this quiz">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                        <select name="category_id" id="category_id" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration" class="block text-sm font-semibold text-gray-700 mb-2">Duration (minutes)</label>
                        <input type="number" name="duration" id="duration" value="{{ old('duration', 30) }}" required min="1"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="enroll_key" class="block text-sm font-semibold text-gray-700 mb-2">Enrollment Key</label>
                        <input type="text" name="enroll_key" id="enroll_key" value="{{ old('enroll_key') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Leave empty for no key">
                        @error('enroll_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="thumbnail" class="block text-sm font-semibold text-gray-700 mb-2">Thumbnail Image</label>
                        <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('thumbnail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                        <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                        <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                </div>

                <!-- Result Display Settings -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Result Display Settings</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="flex items-center space-x-3 p-3 bg-white rounded-lg border cursor-pointer hover:border-indigo-300 transition">
                            <input type="checkbox" name="show_score" value="1" {{ old('show_score', true) ? 'checked' : '' }}
                                class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700">Show Score</span>
                        </label>
                        <label class="flex items-center space-x-3 p-3 bg-white rounded-lg border cursor-pointer hover:border-indigo-300 transition">
                            <input type="checkbox" name="show_correct_answer" value="1" {{ old('show_correct_answer', true) ? 'checked' : '' }}
                                class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700">Show Correct Answers</span>
                        </label>
                        <label class="flex items-center space-x-3 p-3 bg-white rounded-lg border cursor-pointer hover:border-indigo-300 transition">
                            <input type="checkbox" name="show_wrong_answer" value="1" {{ old('show_wrong_answer', true) ? 'checked' : '' }}
                                class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700">Show Wrong Answers</span>
                        </label>
                        <label class="flex items-center space-x-3 p-3 bg-white rounded-lg border cursor-pointer hover:border-indigo-300 transition">
                            <input type="checkbox" name="show_explanation" value="1" {{ old('show_explanation') ? 'checked' : '' }}
                                class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700">Show Explanations</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('hr.quizzes.index') }}"
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                        Create Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
