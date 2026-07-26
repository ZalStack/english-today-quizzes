@extends('layouts.app')

@section('title', 'Quiz Preview - ' . $quiz->title)

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 sm:px-8 py-4 sm:py-6 bg-gradient-to-r from-indigo-600 to-purple-600">
                <h2 class="text-xl sm:text-2xl font-bold text-white">{{ $quiz->title }}</h2>
                <p class="text-indigo-100 text-sm mt-1">Review the quiz details before starting</p>
            </div>

            <div class="p-4 sm:p-8">
                @if(session('error'))
                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl flex items-center text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('info') }}
                    </div>
                @endif

                <!-- Quiz Info -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8">
                    <div class="bg-gray-50 rounded-xl p-3 sm:p-4 text-center">
                        <p class="text-[10px] sm:text-xs text-gray-500 font-medium">Questions</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $quiz->questions()->count() }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 sm:p-4 text-center">
                        <p class="text-[10px] sm:text-xs text-gray-500 font-medium">Duration</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $quiz->duration }} min</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 sm:p-4 text-center">
                        <p class="text-[10px] sm:text-xs text-gray-500 font-medium">Status</p>
                        <p class="text-xl sm:text-2xl font-bold text-green-600">Active</p>
                    </div>
                </div>

                <!-- Description -->
                @if($quiz->description)
                    <div class="mb-6 sm:mb-8 p-3 sm:p-4 bg-gray-50 rounded-xl">
                        <h3 class="font-semibold text-gray-700 text-sm sm:text-base mb-1 sm:mb-2">Description</h3>
                        <p class="text-gray-600 text-sm sm:text-base">{{ $quiz->description }}</p>
                    </div>
                @endif

                <!-- Instructions -->
                <div class="mb-6 sm:mb-8 p-3 sm:p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <h3 class="font-semibold text-blue-900 text-sm sm:text-base mb-2">📋 Instructions</h3>
                    <ul class="text-xs sm:text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Read each question carefully before answering</li>
                        <li>You have {{ $quiz->duration }} minutes to complete the quiz</li>
                        <li>Your answers will be saved automatically</li>
                        <li>You can review your answers before submitting</li>
                        <li>Once submitted, you cannot change your answers</li>
                    </ul>
                </div>

                <!-- Start Button -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('employee.quizzes.join') }}" class="text-gray-500 hover:text-gray-700 transition text-sm sm:text-base">
                        ← Back to Join
                    </a>
                    <form action="{{ route('employee.quizzes.start', $quiz) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                                class="w-full sm:w-auto px-4 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium text-sm sm:text-base flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Start Quiz
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
