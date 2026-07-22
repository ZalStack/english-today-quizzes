@extends('layouts.app')

@section('title', 'Quiz Result')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8">
                <!-- Result Header -->
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Quiz Completed!</h2>
                    <p class="text-gray-600">{{ $attempt->quiz->title }}</p>
                </div>

                <!-- Score Card -->
                @if($attempt->quiz->show_score)
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 mb-8 text-white text-center">
                        <p class="text-lg mb-2 opacity-90">Your Score</p>
                        <p class="text-7xl font-extrabold mb-4">{{ $attempt->score }}%</p>
                        <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
                            <div class="bg-white/20 rounded-xl p-3">
                                <p class="text-xs opacity-90">Correct</p>
                                <p class="text-2xl font-bold">{{ $attempt->total_correct }}</p>
                            </div>
                            <div class="bg-white/20 rounded-xl p-3">
                                <p class="text-xs opacity-90">Wrong</p>
                                <p class="text-2xl font-bold">{{ $attempt->total_wrong }}</p>
                            </div>
                            <div class="bg-white/20 rounded-xl p-3">
                                <p class="text-xs opacity-90">Time</p>
                                <p class="text-2xl font-bold">{{ (int) $attempt->started_at->diffInMinutes($attempt->completed_at) }}m</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Answer Review -->
                @if($attempt->quiz->show_correct_answer || $attempt->quiz->show_wrong_answer)
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Answer Review</h3>

                        @foreach($attempt->answers as $index => $answer)
                            <div class="border-2 rounded-xl p-6 {{ $answer->is_correct ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                                <div class="flex justify-between items-start mb-3">
                                    <h4 class="font-semibold text-gray-900 flex-1">
                                        <span class="text-indigo-600 font-bold mr-2">{{ $index + 1 }}.</span>
                                        {{ $answer->question->question_text }}
                                    </h4>
                                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $answer->is_correct ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                        {{ $answer->points_earned }}/{{ $answer->question->points }} pts
                                    </span>
                                </div>

                                @if($attempt->quiz->show_correct_answer)
                                    <div class="bg-white rounded-lg p-3 mb-2">
                                        <p class="text-sm font-medium text-green-700">✓ Correct Answer: {{ $answer->question->correct_answer }}</p>
                                    </div>
                                @endif

                                @if($attempt->quiz->show_wrong_answer && !$answer->is_correct)
                                    <div class="bg-white rounded-lg p-3 mb-2">
                                        <p class="text-sm font-medium text-red-700">✗ Your Answer: {{ $answer->answer }}</p>
                                    </div>
                                @endif

                                @if($attempt->quiz->show_explanation && $answer->question->explanation)
                                    <div class="bg-blue-50 rounded-lg p-3">
                                        <p class="text-sm text-blue-700"><strong>💡 Explanation:</strong> {{ $answer->question->explanation }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
                    <a href="{{ route('employee.dashboard') }}"
                       class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-bold text-center">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('employee.quizzes.history') }}"
                       class="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold text-center">
                        View History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
