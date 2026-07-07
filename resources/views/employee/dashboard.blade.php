@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">
                Welcome back, <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ auth()->user()->full_name }}</span>!
            </h1>
            <p class="text-gray-500 mt-1">Track your progress and continue learning.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Quizzes Taken</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $statistics['total_quizzes_taken'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Average Score</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($statistics['average_score'], 1) }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Highest Score</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $statistics['highest_score'] }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Available Quizzes -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Available Quizzes</h3>
                        <a href="{{ route('employee.quizzes.join') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center">
                            Join with Key
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($availableQuizzes->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($availableQuizzes->take(4) as $quiz)
                                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-lg transition-all duration-300 hover:border-indigo-300">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 mb-1">{{ $quiz->title }}</h4>
                                                <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full">{{ $quiz->category->name }}</span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-4">{{ Str::limit($quiz->description, 80) }}</p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center text-sm text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $quiz->duration }} min
                                            </div>
                                            <a href="{{ route('employee.quizzes.preview', $quiz) }}" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm rounded-lg hover:shadow-lg transition-all duration-300">
                                                Start Quiz
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-lg font-medium">No quizzes available</p>
                                <p class="text-gray-400 text-sm mt-1">Join a quiz using an enrollment key</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ongoing Quizzes -->
                @if($ongoingQuizzes->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mt-8">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900">Ongoing Quizzes</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($ongoingQuizzes as $attempt)
                                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-5">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $attempt->quiz->title }}</h4>
                                                <p class="text-sm text-gray-500">Started {{ $attempt->started_at->diffForHumans() }}</p>
                                            </div>
                                            <a href="{{ route('employee.quizzes.take', $attempt->id) }}" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-medium">
                                                Continue
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recent Scores -->
            <div class="space-y-8">
                @if($recentScores->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900">Recent Scores</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($recentScores as $attempt)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900 text-sm">{{ $attempt->quiz->title }}</p>
                                            <p class="text-xs text-gray-400">{{ $attempt->completed_at?->diffForHumans() }}</p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-sm font-bold {{ $attempt->score >= 70 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $attempt->score }}%
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quick Links -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Quick Links</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('employee.quizzes.join') }}" class="flex items-center p-3 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition">
                            <span class="text-2xl mr-3">🔑</span>
                            <div>
                                <p class="font-medium text-gray-900">Join Quiz</p>
                                <p class="text-sm text-gray-500">Use enrollment key</p>
                            </div>
                        </a>
                        <a href="{{ route('employee.quizzes.history') }}" class="flex items-center p-3 bg-purple-50 rounded-xl hover:bg-purple-100 transition">
                            <span class="text-2xl mr-3">📜</span>
                            <div>
                                <p class="font-medium text-gray-900">View History</p>
                                <p class="text-sm text-gray-500">Past assessments</p>
                            </div>
                        </a>
                        <a href="{{ route('employee.profile.edit') }}" class="flex items-center p-3 bg-green-50 rounded-xl hover:bg-green-100 transition">
                            <span class="text-2xl mr-3">⚙️</span>
                            <div>
                                <p class="font-medium text-gray-900">Edit Profile</p>
                                <p class="text-sm text-gray-500">Update information</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
