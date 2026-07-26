@extends('layouts.app')

@section('title', 'Quiz Result')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 sm:p-8">
                <!-- Result Header -->
                <div class="text-center mb-6 sm:mb-8">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">Quiz Completed!</h2>
                    <p class="text-gray-600 text-sm sm:text-base">{{ $attempt->quiz->title }}</p>
                </div>

                <!-- Score Card -->
                @if($attempt->quiz->show_score)
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-4 sm:p-8 mb-6 sm:mb-8 text-white text-center">
                        <p class="text-base sm:text-lg mb-2 opacity-90">Your Score</p>
                        <p class="text-5xl sm:text-7xl font-extrabold mb-3 sm:mb-4">{{ $attempt->score }}%</p>
                        <div class="grid grid-cols-3 gap-3 sm:gap-4 max-w-md mx-auto">
                            <div class="bg-white/20 rounded-xl p-2 sm:p-3">
                                <p class="text-[10px] sm:text-xs opacity-90">Correct</p>
                                <p class="text-xl sm:text-2xl font-bold">{{ $attempt->total_correct }}</p>
                            </div>
                            <div class="bg-white/20 rounded-xl p-2 sm:p-3">
                                <p class="text-[10px] sm:text-xs opacity-90">Wrong</p>
                                <p class="text-xl sm:text-2xl font-bold">{{ $attempt->total_wrong }}</p>
                            </div>
                            <div class="bg-white/20 rounded-xl p-2 sm:p-3">
                                <p class="text-[10px] sm:text-xs opacity-90">Time</p>
                                <p class="text-xl sm:text-2xl font-bold">{{ (int) $attempt->started_at->diffInMinutes($attempt->completed_at) }}m</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Answer Review -->
                @if($attempt->quiz->show_correct_answer || $attempt->quiz->show_wrong_answer)
                    <div class="space-y-3 sm:space-y-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4">Answer Review</h3>

                        @foreach($attempt->answers as $index => $answer)
                            <div class="border-2 rounded-xl p-4 sm:p-6 {{ $answer->is_correct ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-3">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base flex-1">
                                        <span class="text-indigo-600 font-bold mr-2">{{ $index + 1 }}.</span>
                                        {{ $answer->question->question_text }}
                                    </h4>
                                    <span class="px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-bold {{ $answer->is_correct ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }} whitespace-nowrap">
                                        {{ $answer->points_earned }}/{{ $answer->question->points }} pts
                                    </span>
                                </div>

                                @if($attempt->quiz->show_correct_answer)
                                    <div class="bg-white rounded-lg p-2 sm:p-3 mb-2">
                                        <p class="text-xs sm:text-sm font-medium text-green-700">✓ Correct Answer: {{ $answer->question->correct_answer }}</p>
                                    </div>
                                @endif

                                @if($attempt->quiz->show_wrong_answer && !$answer->is_correct)
                                    <div class="bg-white rounded-lg p-2 sm:p-3 mb-2">
                                        <p class="text-xs sm:text-sm font-medium text-red-700">✗ Your Answer: {{ $answer->answer }}</p>
                                    </div>
                                @endif

                                @if($attempt->quiz->show_explanation && $answer->question->explanation)
                                    <div class="bg-blue-50 rounded-lg p-2 sm:p-3">
                                        <p class="text-xs sm:text-sm text-blue-700"><strong>💡 Explanation:</strong> {{ $answer->question->explanation }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4 mt-6 sm:mt-8">
                    <a href="{{ route('employee.dashboard') }}"
                       class="w-full sm:w-auto px-4 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-bold text-center text-sm sm:text-base">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('employee.quizzes.history') }}"
                       class="w-full sm:w-auto px-4 sm:px-8 py-2.5 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold text-center text-sm sm:text-base">
                        View History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
