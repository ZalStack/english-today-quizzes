@extends('layouts.hr')

@section('title', 'Video Challenges')
@section('header-title', 'Video Challenges')
@section('header-subtitle', 'Manage video challenge assignments')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div></div>
    <a href="{{ route('hr.video-challenges.create') }}" class="hr-btn-primary w-full sm:w-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Create Challenge
    </a>
</div>

<div class="hr-card overflow-hidden">
    <div class="table-responsive">
        <table class="hr-table">
            <thead>
                <tr>
                    <th>Challenge</th>
                    <th class="hidden sm:table-cell">Deadline</th>
                    <th>Status</th>
                    <th class="hidden md:table-cell">Submissions</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($challenges as $challenge)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="hr-avatar w-9 h-9 text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-slate-900 text-sm truncate max-w-[150px] sm:max-w-xs">{{ $challenge->title }}</span>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell text-slate-500 text-sm">
                            {{ $challenge->deadline?->format('d M Y, H:i') ?? '-' }}
                        </td>
                        <td>
                            @php
                                $isPast = $challenge->deadline && $challenge->deadline->isPast();
                            @endphp
                            <span class="hr-badge {{ $isPast ? 'hr-badge--neutral' : 'hr-badge--success' }}">
                                {{ $isPast ? 'Expired' : 'Active' }}
                            </span>
                        </td>
                        <td class="hidden md:table-cell font-bold text-slate-900">{{ $challenge->submissions_count ?? 0 }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('hr.video-challenges.show', $challenge) }}" class="px-3 py-1.5 text-xs bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition font-semibold">
                                    View
                                </a>
                                <a href="{{ route('hr.video-challenges.edit', $challenge) }}" class="px-3 py-1.5 text-xs bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-semibold">
                                    Edit
                                </a>
                                <form action="{{ route('hr.video-challenges.destroy', $challenge) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-semibold" onclick="return confirm('Delete this challenge?')">
                                        Del
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="hr-empty">
                                <div class="hr-empty__icon">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium">No video challenges yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($challenges instanceof \Illuminate\Pagination\AbstractPaginator && $challenges->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $challenges->links() }}
        </div>
    @endif
</div>
@endsection
