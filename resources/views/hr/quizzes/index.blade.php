@extends('layouts.hr')

@section('title', 'Manage Quizzes')
@section('header-title', 'Quizzes')
@section('header-subtitle', 'Create and manage assessment quizzes')

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
            <div class="hr-stat__icon bg-emerald-50">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">Active</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Active</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $activeQuizzes }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-amber-50">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">Draft</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $draftQuizzes }}</p>
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
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h3 class="text-base font-bold text-slate-900 tracking-tight">All Quizzes</h3>
    <a href="{{ route('hr.quizzes.create') }}" class="hr-btn-primary w-full sm:w-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Create Quiz
    </a>
</div>

<div class="hr-card overflow-hidden">
    <div class="table-responsive">
        <table class="hr-table">
            <thead>
                <tr>
                    <th>Quiz</th>
                    <th class="hidden sm:table-cell">Category</th>
                    <th>Status</th>
                    <th class="hidden md:table-cell">Duration</th>
                    <th>Attempts</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $quiz)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($quiz->thumbnail)
                                    <img src="{{ asset('storage/'.$quiz->thumbnail) }}" class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                                @else
                                    <div class="hr-avatar w-9 h-9 text-xs">
                                        {{ strtoupper(substr($quiz->title, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="font-semibold text-slate-900 text-sm truncate max-w-[120px] sm:max-w-xs">{{ $quiz->title }}</span>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell text-slate-500">{{ $quiz->category->name ?? '-' }}</td>
                        <td>
                            <span class="hr-badge {{ $quiz->status === 'active' ? 'hr-badge--success' : ($quiz->status === 'draft' ? 'hr-badge--warning' : 'hr-badge--neutral') }}">
                                {{ ucfirst($quiz->status) }}
                            </span>
                        </td>
                        <td class="hidden md:table-cell text-slate-500">{{ $quiz->duration }} min</td>
                        <td class="font-bold text-slate-900">{{ $quiz->attempts_count }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('hr.quizzes.show', $quiz) }}" class="px-3 py-1.5 text-xs bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-all duration-200 font-semibold">
                                    View
                                </a>
                                <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all duration-200 font-semibold">
                                    Q
                                </a>
                                <a href="{{ route('hr.quizzes.edit', $quiz) }}" class="px-3 py-1.5 text-xs bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-all duration-200 font-semibold">
                                    Edit
                                </a>
                                <form action="{{ route('hr.quizzes.destroy', $quiz) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all duration-200 font-semibold" onclick="return confirm('Delete this quiz?')">
                                        Del
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="hr-empty">
                                <div class="hr-empty__icon">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium">No quizzes yet</p>
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
