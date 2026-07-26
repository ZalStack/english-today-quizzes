@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">
                Welcome back, <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ auth()->user()->full_name }}</span>!
            </h1>
            <p class="text-gray-500 text-sm sm:text-base mt-1">Track your progress and continue learning.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500">Quizzes Taken</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $statistics['total_quizzes_taken'] }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500">Average Score</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ number_format($statistics['average_score'], 1) }}%</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500">Highest Score</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $statistics['highest_score'] }}%</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Available Quizzes -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex flex-wrap justify-between items-center gap-2">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900">Available Quizzes</h3>
                        <a href="{{ route('employee.quizzes.join') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center">
                            Join with Key
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="p-4 sm:p-6">
                        @if($availableQuizzes->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($availableQuizzes as $quiz)
                                    <div class="border border-gray-200 rounded-xl p-4 sm:p-5 hover:shadow-lg transition-all duration-300 hover:border-indigo-300">
                                        <div class="flex items-start justify-between mb-2 sm:mb-3">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $quiz->title }}</h4>
                                                <span class="text-[10px] sm:text-xs px-2 py-0.5 sm:py-1 bg-indigo-100 text-indigo-700 rounded-full inline-block">{{ $quiz->category->name }}</span>
                                            </div>
                                        </div>
                                        <p class="text-xs sm:text-sm text-gray-500 mb-3 sm:mb-4 line-clamp-2">{{ Str::limit($quiz->description, 80) }}</p>
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <div class="flex items-center text-xs sm:text-sm text-gray-400">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $quiz->duration }} min
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                @if($quiz->enroll_key)
                                                    <span class="text-[10px] sm:text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Key: {{ $quiz->enroll_key }}</span>
                                                @endif
                                                <a href="{{ route('employee.quizzes.join') }}"
                                                   class="px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-xs sm:text-sm rounded-lg hover:shadow-lg transition-all duration-300 whitespace-nowrap">
                                                    Join
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 sm:py-8">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm sm:text-base font-medium">No quizzes available</p>
                                <p class="text-gray-400 text-xs sm:text-sm mt-1">Join a quiz using an enrollment key</p>
                                <a href="{{ route('employee.quizzes.join') }}" class="mt-3 sm:mt-4 inline-block px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 text-sm sm:text-base">
                                    Join with Key
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ongoing Quizzes -->
                @if($ongoingQuizzes->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mt-6 sm:mt-8">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900">Ongoing Quizzes</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="space-y-3 sm:space-y-4">
                                @foreach($ongoingQuizzes as $attempt)
                                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-4 sm:p-5">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base">{{ $attempt->quiz->title }}</h4>
                                                <p class="text-xs sm:text-sm text-gray-500">Started {{ $attempt->started_at->diffForHumans() }}</p>
                                            </div>
                                            <a href="{{ route('employee.quizzes.take', $attempt->id) }}" class="px-4 sm:px-6 py-1.5 sm:py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition font-medium text-center">
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

            <!-- Right Sidebar -->
            <div class="space-y-6 sm:space-y-8">
                <!-- Recent Scores -->
                @if($recentScores->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900">Recent Scores</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="space-y-3 sm:space-y-4">
                                @foreach($recentScores as $attempt)
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition gap-2">
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 text-xs sm:text-sm truncate">{{ $attempt->quiz->title }}</p>
                                            <p class="text-[10px] sm:text-xs text-gray-400">{{ $attempt->completed_at?->diffForHumans() }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-bold {{ $attempt->score >= 70 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} whitespace-nowrap">
                                            {{ $attempt->score }}%
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3 sm:mt-4 text-center">
                                <a href="{{ route('employee.quizzes.history') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                    View All History →
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Leaderboard -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 flex items-center">
                            <span class="mr-2">🏆</span> Leaderboard
                        </h3>
                        <span class="text-[10px] sm:text-xs text-gray-400 font-medium">Top 5</span>
                    </div>
                    <div class="p-4 sm:p-6">
                        @if(isset($leaderboard) && $leaderboard->count() > 0)
                            <div class="space-y-2 sm:space-y-3">
                                @foreach($leaderboard as $index => $entry)
                                    @php
                                        $rank = $index + 1;
                                        $isCurrentUser = $entry->id === auth()->id();
                                        $medal = match($rank) {
                                            1 => '🥇',
                                            2 => '🥈',
                                            3 => '🥉',
                                            default => null,
                                        };
                                    @endphp
                                    <div class="flex items-center justify-between p-2 sm:p-3 rounded-xl transition {{ $isCurrentUser ? 'bg-indigo-50 border border-indigo-200' : 'bg-gray-50 hover:bg-gray-100' }}">
                                        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                                            <div class="w-6 h-6 sm:w-8 sm:h-8 flex-shrink-0 flex items-center justify-center font-bold text-xs sm:text-sm rounded-full {{ $rank <= 3 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200 text-gray-500' }}">
                                                {{ $medal ?? $rank }}
                                            </div>
                                            <div class="w-7 h-7 sm:w-9 sm:h-9 flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs sm:text-sm font-bold">
                                                {{ strtoupper(substr($entry->full_name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 text-xs sm:text-sm truncate">
                                                    {{ $entry->full_name }}
                                                    @if($isCurrentUser)
                                                        <span class="text-[10px] sm:text-xs text-indigo-500 font-normal">(You)</span>
                                                    @endif
                                                </p>
                                                <p class="text-[10px] sm:text-xs text-gray-400">{{ $entry->completed_quizzes_count ?? 0 }} quiz selesai</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap">
                                            {{ number_format($entry->total_score ?? 0) }} pts
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if(isset($currentUserRank) && $currentUserRank && $currentUserRank['rank'] > 5)
                                <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-dashed border-gray-200">
                                    <div class="flex items-center justify-between p-2 sm:p-3 rounded-xl bg-indigo-50 border border-indigo-200">
                                        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                                            <div class="w-6 h-6 sm:w-8 sm:h-8 flex-shrink-0 flex items-center justify-center font-bold text-xs sm:text-sm rounded-full bg-gray-200 text-gray-500">
                                                {{ $currentUserRank['rank'] }}
                                            </div>
                                            <div class="w-7 h-7 sm:w-9 sm:h-9 flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs sm:text-sm font-bold">
                                                {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 text-xs sm:text-sm truncate">{{ auth()->user()->full_name }} <span class="text-[10px] sm:text-xs text-indigo-500 font-normal">(You)</span></p>
                                                <p class="text-[10px] sm:text-xs text-gray-400">Peringkat kamu saat ini</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap">
                                            {{ number_format($currentUserRank['total_score'] ?? 0) }} pts
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-6 sm:py-8">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                    <span class="text-2xl">🏆</span>
                                </div>
                                <p class="text-gray-500 text-xs sm:text-sm font-medium">Belum ada data leaderboard</p>
                                <p class="text-gray-400 text-[10px] sm:text-xs mt-1">Selesaikan quiz untuk masuk peringkat</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
