@extends('layouts.hr')

@section('title', 'Reports')
@section('header-title', 'Reports')
@section('header-subtitle', 'View quiz performance reports')

@section('content')
<div class="hr-card overflow-hidden">
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
                                <a href="{{ route('hr.reports.show', $quiz) }}" class="px-3 py-1.5 text-xs bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition font-semibold">
                                    View
                                </a>
                                <a href="{{ route('hr.reports.export', $quiz) }}" class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition font-semibold">
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
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>
@endsection
