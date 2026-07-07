@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            @if($quiz->thumbnail)
                <img src="{{ asset('storage/' . $quiz->thumbnail) }}" alt="{{ $quiz->title }}" class="w-full h-64 object-cover">
            @else
                <div class="w-full h-64 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-6xl text-white font-bold">{{ substr($quiz->title, 0, 1) }}</span>
                </div>
            @endif

            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $quiz->title }}</h2>
                        <p class="text-gray-600">{{ $quiz->description }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-blue-600 font-medium">Duration</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $quiz->duration }} min</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-green-600 font-medium">Questions</p>
                        <p class="text-2xl font-bold text-green-900">{{ $quiz->total_questions }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-purple-600 font-medium">Category</p>
                        <p class="text-lg font-bold text-purple-900">{{ $quiz->category->name }}</p>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-yellow-800 mb-2">📋 Instructions:</h3>
                    <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                        <li>You have {{ $quiz->duration }} minutes to complete this quiz</li>
                        <li>The quiz contains {{ $quiz->total_questions }} questions</li>
                        <li>You cannot pause the timer once started</li>
                        <li>Your answers will be auto-saved as you progress</li>
                        <li>The quiz will auto-submit when time runs out</li>
                        <li>Ensure you have a stable internet connection</li>
                    </ul>
                </div>

                <form action="{{ route('employee.quizzes.start', $quiz) }}" method="POST" class="text-center">
                    @csrf
                    <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold text-lg">
                        Start Quiz Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
