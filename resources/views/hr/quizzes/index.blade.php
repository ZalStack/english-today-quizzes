@extends('layouts.hr')

@section('title', 'Manage Quizzes')
@section('header-title', 'Quizzes')
@section('header-subtitle', 'Create and manage assessment quizzes')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div></div>
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
                                <a href="{{ route('hr.quizzes.show', $quiz) }}" class="px-3 py-1.5 text-xs bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition font-semibold">
                                    View
                                </a>
                                <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition font-semibold">
                                    Q
                                </a>
                                <a href="{{ route('hr.quizzes.edit', $quiz) }}" class="px-3 py-1.5 text-xs bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-semibold">
                                    Edit
                                </a>
                                <form action="{{ route('hr.quizzes.destroy', $quiz) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-semibold" onclick="return confirm('Delete this quiz?')">
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
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>
@endsection
