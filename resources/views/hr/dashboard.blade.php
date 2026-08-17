@extends('layouts.hr')

@section('title', 'HR Dashboard')
@section('header-title', 'Dashboard')
@section('header-subtitle', 'Welcome back, {{ auth()->user()->full_name }}!')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-4">
            <div class="hr-stat__icon bg-blue-50">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+12%</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Employees</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalEmployees }}</p>
    </div>

    <div class="hr-stat">
        <div class="flex items-center justify-between mb-4">
            <div class="hr-stat__icon bg-violet-50">
                <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-full">Active</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Divisions</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalDivisions }}</p>
    </div>

    <div class="hr-stat">
        <div class="flex items-center justify-between mb-4">
            <div class="hr-stat__icon bg-indigo-50">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">{{ $activeQuizzes }} active</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Quizzes</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalQuizzes }}</p>
    </div>

    <div class="hr-stat">
        <div class="flex items-center justify-between mb-4">
            <div class="hr-stat__icon bg-amber-50">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-full">Unique</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Participants</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalParticipants }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Activity -->
    <div class="lg:col-span-2 hr-card">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-base font-bold text-slate-900">Recent Activity</h3>
            <a href="{{ route('hr.reports.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold">View All</a>
        </div>
        <div class="p-6">
            @if($recentAttempts->count() > 0)
                <div class="space-y-3">
                    @foreach($recentAttempts as $attempt)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition gap-2">
                            <div class="flex items-center gap-3">
                                <div class="hr-avatar w-10 h-10 text-sm">
                                    {{ strtoupper(substr($attempt->user->full_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900 text-sm">{{ $attempt->user->full_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $attempt->quiz->title }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="hr-badge {{ $attempt->score >= 70 ? 'hr-badge--success' : 'hr-badge--danger' }}">
                                    {{ $attempt->score ?? 'N/A' }}%
                                </span>
                                <p class="text-xs text-slate-400">{{ $attempt->completed_at?->diffForHumans() ?? 'In Progress' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="hr-empty">
                    <div class="hr-empty__icon">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <p class="text-slate-500 font-medium">No recent activity</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Leaderboard -->
    <div class="hr-card">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Leaderboard</h3>
            <span class="text-xs text-slate-400 font-semibold">Top 5</span>
        </div>
        <div class="p-6">
            @if(isset($leaderboard) && $leaderboard->count() > 0)
                <div class="space-y-3">
                    @foreach($leaderboard as $index => $entry)
                        @php
                            $rank = $index + 1;
                            $medal = match($rank) {
                                1 => '<span class="text-lg">&#x1F947;</span>',
                                2 => '<span class="text-lg">&#x1F948;</span>',
                                3 => '<span class="text-lg">&#x1F949;</span>',
                                default => null,
                            };
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-7 h-7 flex-shrink-0 flex items-center justify-center font-bold text-xs rounded-full {{ $rank <= 3 ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-500' }}">
                                    {!! $medal ?? $rank !!}
                                </div>
                                <div class="hr-avatar w-9 h-9 text-xs">
                                    {{ strtoupper(substr($entry->full_name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900 text-sm truncate">{{ $entry->full_name }}</p>
                                    <p class="text-xs text-slate-400 truncate">
                                        {{ $entry->division->name ?? '-' }} &middot; {{ $entry->completed_quizzes_count ?? 0 }} quiz
                                    </p>
                                </div>
                            </div>
                            <span class="hr-badge hr-badge--info whitespace-nowrap">
                                {{ number_format($entry->total_score ?? 0) }} pts
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="hr-empty">
                    <div class="hr-empty__icon">
                        <span class="text-2xl">&#x1F3C6;</span>
                    </div>
                    <p class="text-slate-500 font-medium text-sm">Belum ada data leaderboard</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
