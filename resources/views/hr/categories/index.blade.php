@extends('layouts.hr')

@section('title', 'Quiz Categories')
@section('header-title', 'Quiz Categories')
@section('header-subtitle', 'Organize quizzes by categories')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8 animate-stagger">
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-primary-50">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">Total</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Categories</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $totalCategories }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-blue-50">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Quizzes</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $totalQuizzesInCategories }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-emerald-50">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">Avg per Category</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $averagePerCategory }}</p>
    </div>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h3 class="text-base font-bold text-slate-900 tracking-tight">All Categories</h3>
    <a href="{{ route('hr.categories.create') }}" class="hr-btn-primary w-full sm:w-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Category
    </a>
</div>

<div class="hr-card overflow-hidden">
    <div class="table-responsive">
        <table class="hr-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="hidden sm:table-cell">Description</th>
                    <th>Quizzes</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="hr-avatar w-9 h-9 text-xs">
                                    {{ strtoupper(substr($category->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-900 text-sm">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell text-slate-500">{{ $category->description ?? '-' }}</td>
                        <td>
                            <span class="hr-badge hr-badge--info">{{ $category->quizzes_count }}</span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('hr.categories.edit', $category) }}" class="px-3 py-1.5 text-xs bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-all duration-200 font-semibold">
                                    Edit
                                </a>
                                <form action="{{ route('hr.categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all duration-200 font-semibold" onclick="return confirm('Delete this category?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="hr-empty">
                                <div class="hr-empty__icon">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium">No categories yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories instanceof \Illuminate\Pagination\AbstractPaginator && $categories->hasPages())
        <div class="px-6 py-4 border-t border-slate-100/80">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
