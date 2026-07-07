@extends('layouts.app')

@section('title', 'Quiz Result')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Quiz Completed!</h2>
                        <p class="text-gray-600">{{ $attempt->quiz->title }}</p>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="mb-4 p-4 bg-yellow-100 text-yellow-700 rounded-lg">
                            {{ session('info') }}
                        </div>
                    @endif

                    <!-- Score Card -->
                    @if ($attempt->quiz->show_score)
                        <div
                            class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-8 mb-8 text-white text-center">
                            <p class="text-lg mb-2">Your Score</p>
                            <p class="text-6xl font-bold mb-2">{{ $attempt->score }}%</p>
                            <div class="flex justify-center space-x-8 mt-4">
                                <div>
                                    <p class="text-sm">Correct</p>
                                    <p class="text-2xl font-bold">{{ $attempt->total_correct }}</p>
                                </div>
                                <div>
                                    <p class="text-sm">Wrong</p>
                                    <p class="text-2xl font-bold">{{ $attempt->total_wrong }}</p>
                                </div>
                                <div>
                                    <p class="text-sm">Time</p>
                                    <p class="text-2xl font-bold">
                                        {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} min</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Answer Review -->
                    @if ($attempt->quiz->show_correct_answer || $attempt->quiz->show_wrong_answer)
                        <div class="space-y-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Answer Review</h3>

                            @foreach ($attempt->answers as $index => $answer)
                                <div
                                    class="border rounded-lg p-4 {{ $answer->is_correct ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-semibold mb-2">
                                                {{ $index + 1 }}. {{ $answer->question->question_text }}
                                            </h4>

                                            @if ($answer->question->question_image)
                                                <img src="{{ asset('storage/' . $answer->question->question_image) }}"
                                                    alt="Question" class="mb-3 max-w-md rounded">
                                            @endif

                                            <div class="ml-4 space-y-1">
                                                @if ($answer->question->options && is_array($answer->question->options))
                                                    <div class="mb-2">
                                                        <p class="text-sm font-medium mb-1">Options:</p>
                                                        @foreach ($answer->question->options as $key => $option)
                                                            <p
                                                                class="text-sm ml-2 {{ $option === $answer->question->correct_answer ? 'text-green-600 font-semibold' : 'text-gray-600' }}">
                                                                {{ chr(65 + $key) }}. {{ $option }}
                                                                @if ($option === $answer->question->correct_answer)
                                                                    ← Correct Answer
                                                                @endif
                                                                @if ($option === $answer->answer && $option !== $answer->question->correct_answer)
                                                                    ← Your Answer
                                                                @endif
                                                            </p>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if ($attempt->quiz->show_correct_answer)
                                                    <p class="text-sm">
                                                        <span class="font-medium">Correct Answer:</span>
                                                        <span
                                                            class="text-green-600">{{ $answer->question->correct_answer }}</span>
                                                    </p>
                                                @endif

                                                @if ($attempt->quiz->show_wrong_answer && !$answer->is_correct)
                                                    <p class="text-sm">
                                                        <span class="font-medium">Your Answer:</span>
                                                        <span class="text-red-600">{{ $answer->answer }}</span>
                                                    </p>
                                                @endif

                                                @if ($answer->is_correct)
                                                    <p class="text-sm text-green-600">✓ Correct</p>
                                                @else
                                                    <p class="text-sm text-red-600">✗ Wrong</p>
                                                @endif
                                            </div>

                                            @if ($attempt->quiz->show_explanation && $answer->question->explanation)
                                                <div class="mt-3 bg-blue-50 p-3 rounded">
                                                    <p class="text-sm text-blue-700">
                                                        <strong>Explanation:</strong> {{ $answer->question->explanation }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                        <span
                                            class="text-sm font-semibold ml-4 {{ $answer->is_correct ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $answer->points_earned }}/{{ $answer->question->points }} pts
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex justify-center space-x-4 mt-8">
                        <a href="{{ route('employee.dashboard') }}"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Back to Dashboard
                        </a>
                        <a href="{{ route('employee.quizzes.history') }}"
                            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            View History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
