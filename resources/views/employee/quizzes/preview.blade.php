@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Thumbnail -->
            @if($quiz->thumbnail)
                <img src="{{ asset('storage/' . $quiz->thumbnail) }}" alt="{{ $quiz->title }}" class="w-full h-64 object-cover">
            @else
                <div class="w-full h-64 bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center">
                    <div class="text-center text-white">
                        <svg class="w-20 h-20 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-2xl font-bold">{{ $quiz->title }}</p>
                    </div>
                </div>
            @endif

            <div class="p-8">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $quiz->title }}</h2>
                    <p class="text-gray-600">{{ $quiz->description }}</p>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-blue-600 font-medium">Duration</p>
                        <p class="text-xl font-bold text-blue-900">{{ $quiz->duration }} min</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-green-600 font-medium">Questions</p>
                        <p class="text-xl font-bold text-green-900">{{ $quiz->total_questions }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-4 text-center">
                        <svg class="w-8 h-8 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <p class="text-sm text-purple-600 font-medium">Category</p>
                        <p class="text-sm font-bold text-purple-900">{{ $quiz->category->name }}</p>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6 mb-8">
                    <h3 class="font-bold text-yellow-800 text-lg mb-3">📋 Important Instructions</h3>
                    <ul class="space-y-2 text-sm text-yellow-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            You have <strong>{{ $quiz->duration }} minutes</strong> to complete this quiz
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            The quiz contains <strong>{{ $quiz->total_questions }} questions</strong>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <strong>Timer starts immediately</strong> and cannot be paused
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Your answers are <strong>auto-saved</strong> as you progress
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Quiz <strong>auto-submits</strong> when time runs out
                        </li>
                    </ul>
                </div>

                <form action="{{ route('employee.quizzes.start', $quiz) }}" method="POST" class="text-center">
                    @csrf
                    <button type="submit"
                            class="px-10 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-xl rounded-xl hover:shadow-2xl transition-all duration-300 font-bold transform hover:scale-105">
                        Start Quiz Now
                    </button>
                    <p class="text-sm text-gray-500 mt-3">Clicking start will begin the timer immediately</p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
