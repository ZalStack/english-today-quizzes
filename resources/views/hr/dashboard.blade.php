@extends('layouts.app')

@section('title', 'HR Dashboard')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">
                Welcome back, <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ auth()->user()->full_name }}</span>!
            </h1>
            <p class="text-gray-500 text-sm sm:text-base mt-1">Here's what's happening with your assessments today.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500">Total Employees</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $totalEmployees }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 sm:mt-4 flex items-center text-xs sm:text-sm">
                    <span class="text-green-500 font-medium">↑ 12%</span>
                    <span class="text-gray-400 ml-2">from last month</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500">Total Divisions</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $totalDivisions }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 sm:mt-4 flex items-center text-xs sm:text-sm">
                    <span class="text-gray-400">Active departments</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500">Total Quizzes</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $totalQuizzes }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 sm:mt-4 flex flex-wrap items-center text-xs sm:text-sm gap-1">
                    <span class="text-green-500 font-medium">{{ $activeQuizzes }} active</span>
                    <span class="text-gray-400">{{ $completedQuizzes }} completed</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500">Participants</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $totalParticipants }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 sm:mt-4 flex items-center text-xs sm:text-sm">
                    <span class="text-green-500 font-medium">Unique users</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Recent Activity -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Recent Activity</h3>
                    <a href="{{ route('hr.reports.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">View All →</a>
                </div>
                <div class="p-4 sm:p-6">
                    @if($recentAttempts->count() > 0)
                        <div class="space-y-3 sm:space-y-4">
                            @foreach($recentAttempts as $attempt)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition gap-2">
                                    <div class="flex items-center space-x-3 sm:space-x-4">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-sm sm:text-base">
                                            {{ strtoupper(substr($attempt->user->full_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm sm:text-base">{{ $attempt->user->full_name }}</p>
                                            <p class="text-xs sm:text-sm text-gray-500">{{ $attempt->quiz->title }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4">
                                        <span class="px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-semibold {{ $attempt->score >= 70 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $attempt->score ?? 'N/A' }}%
                                        </span>
                                        <p class="text-xs text-gray-400">{{ $attempt->completed_at?->diffForHumans() ?? 'In Progress' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 sm:py-8">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-sm sm:text-base">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Leaderboard -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 flex items-center">
                        <span class="mr-2">🏆</span> Leaderboard
                    </h3>
                    <span class="text-xs text-gray-400 font-medium">Top 5</span>
                </div>
                <div class="p-4 sm:p-6">
                    @if(isset($leaderboard) && $leaderboard->count() > 0)
                        <div class="space-y-2 sm:space-y-3">
                            @foreach($leaderboard as $index => $entry)
                                @php
                                    $rank = $index + 1;
                                    $medal = match($rank) {
                                        1 => '🥇',
                                        2 => '🥈',
                                        3 => '🥉',
                                        default => null,
                                    };
                                @endphp
                                <div class="flex items-center justify-between p-2 sm:p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition gap-2">
                                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                                        <div class="w-6 h-6 sm:w-8 sm:h-8 flex-shrink-0 flex items-center justify-center font-bold text-xs sm:text-sm rounded-full {{ $rank <= 3 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200 text-gray-500' }}">
                                            {{ $medal ?? $rank }}
                                        </div>
                                        <div class="w-7 h-7 sm:w-9 sm:h-9 flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs sm:text-sm font-bold">
                                            {{ strtoupper(substr($entry->full_name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 text-xs sm:text-sm truncate">{{ $entry->full_name }}</p>
                                            <p class="text-xs text-gray-400 truncate">
                                                {{ $entry->division->name ?? '-' }} · {{ $entry->completed_quizzes_count ?? 0 }} quiz
                                            </p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap">
                                        {{ number_format($entry->total_score ?? 0) }} pts
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 sm:py-8">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <span class="text-2xl">🏆</span>
                            </div>
                            <p class="text-gray-500 text-sm sm:text-base">Belum ada data leaderboard</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
