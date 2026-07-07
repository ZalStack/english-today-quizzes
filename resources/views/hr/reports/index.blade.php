@extends('layouts.app')

@section('title', 'Quiz Reports')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">Quiz Reports</h1>
            <p class="text-gray-500 mt-1">View detailed analytics and export quiz results</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($quizzes as $quiz)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 card-hover">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $quiz->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($quiz->status) }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $quiz->title }}</h3>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-blue-600 font-medium">Attempts</p>
                            <p class="text-xl font-bold text-blue-900">{{ $quiz->attempts_count }}</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-purple-600 font-medium">Questions</p>
                            <p class="text-xl font-bold text-purple-900">{{ $quiz->total_questions }}</p>
                        </div>
                    </div>

                    <div class="flex space-x-2">
                        <a href="{{ route('hr.reports.show', $quiz) }}"
                           class="flex-1 px-4 py-2 bg-indigo-600 text-white text-center rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                            View Details
                        </a>
                        <a href="{{ route('hr.reports.export', $quiz) }}"
                           class="flex-1 px-4 py-2 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition text-sm font-medium">
                            Export CSV
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
