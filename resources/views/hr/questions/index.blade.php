@extends('layouts.app')

@section('title', 'Questions - ' . $quiz->title)

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <a href="{{ route('hr.quizzes.show', $quiz) }}" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-extrabold text-gray-900">{{ $quiz->title }}</h1>
                </div>
                <p class="text-gray-500">Manage questions for this quiz</p>
            </div>
            <a href="{{ route('hr.quizzes.questions.create', $quiz) }}" class="mt-4 sm:mt-0 inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Question
            </a>
        </div>

        <!-- Quiz Info Card -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 mb-8 text-white">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-indigo-200 text-sm">Total Questions</p>
                    <p class="text-2xl font-bold">{{ $quiz->total_questions }}</p>
                </div>
                <div>
                    <p class="text-indigo-200 text-sm">Duration</p>
                    <p class="text-2xl font-bold">{{ $quiz->duration }} min</p>
                </div>
                <div>
                    <p class="text-indigo-200 text-sm">Status</p>
                    <p class="text-2xl font-bold capitalize">{{ $quiz->status }}</p>
                </div>
                <div>
                    <p class="text-indigo-200 text-sm">Enroll Key</p>
                    <p class="text-2xl font-bold">{{ $quiz->enroll_key ?? 'None' }}</p>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Questions List -->
        @if($questions->count() > 0)
            <div class="space-y-4">
                @foreach($questions as $index => $question)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">
                                        {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                    </span>
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                        {{ $question->points }} pts
                                    </span>
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ $question->question_text }}</h3>

                                @if($question->question_image)
                                    <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question Image" class="mb-3 rounded-lg max-w-md">
                                @endif

                                @if($question->options && is_array($question->options))
                                    <div class="bg-gray-50 rounded-xl p-4 ml-4">
                                        <p class="text-sm font-semibold text-gray-700 mb-2">Options:</p>
                                        <div class="space-y-2">
                                            @foreach($question->options as $key => $option)
                                                <div class="flex items-center space-x-2">
                                                    <span class="w-6 h-6 rounded-full {{ $option === $question->correct_answer ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center text-white text-xs font-bold">
                                                        {{ chr(65 + $key) }}
                                                    </span>
                                                    <span class="text-sm {{ $option === $question->correct_answer ? 'text-green-700 font-semibold' : 'text-gray-600' }}">
                                                        {{ $option }}
                                                        @if($option === $question->correct_answer)
                                                            <span class="text-green-600 ml-1">✓</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-green-50 rounded-xl p-4 ml-4 border border-green-200">
                                        <p class="text-sm font-semibold text-green-800">Correct Answer:</p>
                                        <p class="text-green-700">{{ $question->correct_answer }}</p>
                                    </div>
                                @endif

                                @if($question->explanation)
                                    <div class="bg-blue-50 rounded-xl p-4 mt-3 border border-blue-200">
                                        <p class="text-sm font-semibold text-blue-800">Explanation:</p>
                                        <p class="text-blue-700">{{ $question->explanation }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex lg:flex-col space-x-2 lg:space-x-0 lg:space-y-2">
                                <a href="{{ route('hr.quizzes.questions.edit', [$quiz, $question]) }}"
                                   class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition text-sm font-medium text-center">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('hr.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium"
                                            onclick="return confirm('Delete this question?')">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $questions->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Questions Yet</h3>
                <p class="text-gray-500 mb-6">Start building your quiz by adding questions</p>
                <a href="{{ route('hr.quizzes.questions.create', $quiz) }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add First Question
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
