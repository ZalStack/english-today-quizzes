@extends('layouts.hr')

@section('title', 'Reports')
@section('header-title', 'Reports')
@section('header-subtitle', 'View quiz performance reports')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-stagger">
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-primary-50">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">Total</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Quizzes</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $totalQuizzes }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-blue-50">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Attempts</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $totalAttempts }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-emerald-50">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">Average Score</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($averageScore, 1) }}%</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-amber-50">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">Passing Rate</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $passingRate }}%</p>
    </div>
</div>

<div class="hr-card overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100/80">
        <h3 class="text-base font-bold text-slate-900 tracking-tight">Quiz Reports</h3>
    </div>
    <div class="table-responsive">
        <table class="hr-table">
            <thead>
                <tr>
                    <th>Quiz</th>
                    <th class="hidden sm:table-cell">Category</th>
                    <th>Participants</th>
                    <th class="hidden md:table-cell">Avg Score</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $quiz)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="hr-avatar w-9 h-9 text-xs">
                                    {{ strtoupper(substr($quiz->title, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-900 text-sm truncate max-w-[150px] sm:max-w-xs">{{ $quiz->title }}</span>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell text-slate-500">{{ $quiz->category->name ?? '-' }}</td>
                        <td>
                            <span class="hr-badge hr-badge--info">{{ $quiz->attempts_count }}</span>
                        </td>
                        <td class="hidden md:table-cell font-semibold text-slate-900">
                            {{ $quiz->attempts_count > 0 ? number_format($quiz->attempts->avg('score'), 1) : '-' }}%
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('hr.reports.show', $quiz) }}" class="px-3 py-1.5 text-xs bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-all duration-200 font-semibold">
                                    View
                                </a>
                                <a href="{{ route('hr.reports.export', $quiz) }}" class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all duration-200 font-semibold">
                                    Export
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="hr-empty">
                                <div class="hr-empty__icon">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium">No quiz reports available</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($quizzes instanceof \Illuminate\Pagination\AbstractPaginator && $quizzes->hasPages())
        <div class="px-6 py-4 border-t border-slate-100/80">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>
@endsection
