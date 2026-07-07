@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Quizzes Taken</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_quizzes_taken'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Average Score</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['average_score'], 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Highest Score</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['highest_score'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Available Quizzes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Available Quizzes</h3>
                </div>
                <div class="p-6">
                    @if($availableQuizzes->count() > 0)
                        <div class="space-y-4">
                            @foreach($availableQuizzes as $quiz)
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $quiz->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ $quiz->category->name }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Available</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($quiz->description, 100) }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">{{ $quiz->duration }} minutes</span>
                                        <a href="{{ route('employee.quizzes.preview', $quiz) }}"
                                            class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition">
                                            View Quiz
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No quizzes available at the moment.</p>
                    @endif

                    <div class="mt-4 text-center">
                        <a href="{{ route('employee.quizzes.join') }}"
                            class="text-indigo-600 hover:text-indigo-800 font-medium">
                            Join Quiz with Enrollment Key →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ongoing Quizzes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Ongoing Quizzes</h3>
                </div>
                <div class="p-6">
                    @if($ongoingQuizzes->count() > 0)
                        <div class="space-y-4">
                            @foreach($ongoingQuizzes as $attempt)
                                <div class="border rounded-lg p-4 bg-yellow-50">
                                    <h4 class="font-semibold text-gray-900">{{ $attempt->quiz->title }}</h4>
                                    <p class="text-sm text-gray-600 mb-2">Started: {{ $attempt->started_at->format('Y-m-d H:i') }}</p>
                                    <a href="{{ route('employee.quizzes.take', $attempt->id) }}"
                                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm">
                                        Continue Quiz
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No ongoing quizzes.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Scores -->
        @if($recentScores->count() > 0)
            <div class="bg-white rounded-lg shadow mt-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Scores</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentScores as $attempt)
                            <div class="flex justify-between items-center border-b pb-3">
                                <div>
                                    <p class="font-semibold">{{ $attempt->quiz->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $attempt->completed_at?->format('Y-m-d H:i') }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full font-semibold text-sm
                                    {{ $attempt->score >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $attempt->score }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Completed Quizzes -->
        @if($completedQuizzes->count() > 0)
            <div class="bg-white rounded-lg shadow mt-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Completed Quizzes</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($completedQuizzes as $attempt)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $attempt->quiz->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $attempt->score >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $attempt->score }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $attempt->completed_at?->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('employee.quizzes.result', $attempt->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                View Result
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
