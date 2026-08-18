@extends('layouts.hr')

@section('title', 'Report - ' . $quiz->title)
@section('header-title', $quiz->title . ' Report')
@section('header-subtitle', 'Detailed performance report')

@section('content')
<div class="mb-6">
    <a href="{{ route('hr.reports.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Reports
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8 animate-stagger">
    <div class="hr-stat">
        <p class="text-sm font-medium text-slate-500">Total Participants</p>
        <p class="text-3xl font-bold text-slate-900 mt-1 tracking-tight">{{ $attempts->count() }}</p>
    </div>
    <div class="hr-stat">
        <p class="text-sm font-medium text-slate-500">Average Score</p>
        <p class="text-3xl font-bold text-slate-900 mt-1 tracking-tight">{{ $attempts->count() > 0 ? number_format($attempts->avg('score'), 1) : '0' }}%</p>
    </div>
    <div class="hr-stat">
        <p class="text-sm font-medium text-slate-500">Pass Rate</p>
        <p class="text-3xl font-bold text-slate-900 mt-1 tracking-tight">{{ $attempts->count() > 0 ? number_format($attempts->where('score', '>=', 70)->count() / $attempts->count() * 100, 1) : '0' }}%</p>
    </div>
</div>

<div class="flex justify-between items-center mb-6">
    <h3 class="text-base font-bold text-slate-900 tracking-tight">Participants</h3>
    <a href="{{ route('hr.reports.export', $quiz) }}" class="hr-btn-primary text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Export CSV
    </a>
</div>

<div class="hr-card overflow-hidden">
    <div class="table-responsive">
        <table class="hr-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th class="hidden sm:table-cell">Division</th>
                    <th>Score</th>
                    <th>Status</th>
                    <th class="hidden md:table-cell">Completed</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $attempt)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="hr-avatar w-9 h-9 text-xs">
                                    {{ strtoupper(substr($attempt->user->full_name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-900 text-sm">{{ $attempt->user->full_name }}</span>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell text-slate-500">{{ $attempt->user->division->name ?? '-' }}</td>
                        <td class="font-bold text-slate-900">{{ $attempt->score ?? 'N/A' }}%</td>
                        <td>
                            <span class="hr-badge {{ ($attempt->score ?? 0) >= 70 ? 'hr-badge--success' : 'hr-badge--danger' }}">
                                {{ ($attempt->score ?? 0) >= 70 ? 'Pass' : 'Fail' }}
                            </span>
                        </td>
                        <td class="hidden md:table-cell text-slate-500 text-sm">{{ $attempt->completed_at?->format('d M Y, H:i') ?? 'In Progress' }}</td>
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
                                <p class="text-slate-500 font-medium">No participants yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($attempts instanceof \Illuminate\Pagination\AbstractPaginator && $attempts->hasPages())
        <div class="px-6 py-4 border-t border-slate-100/80">
            {{ $attempts->links() }}
        </div>
    @endif
</div>
@endsection
