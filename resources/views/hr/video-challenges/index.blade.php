@extends('layouts.hr')

@section('title', 'Video Challenges')
@section('header-title', 'Video Challenges')
@section('header-subtitle', 'Manage video challenge assignments')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8 animate-stagger">
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-primary-50">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">Total</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Challenges</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $totalChallenges }}</p>
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
        <p class="text-sm font-medium text-slate-500">Active Challenges</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $activeChallenges }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-blue-50">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Submissions</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $totalSubmissions }}</p>
    </div>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h3 class="text-base font-bold text-slate-900 tracking-tight">All Challenges</h3>
    <a href="{{ route('hr.video-challenges.create') }}" class="hr-btn-primary w-full sm:w-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Create Challenge
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($challenges as $challenge)
        @php
            $isPast = $challenge->deadline && $challenge->deadline->isPast();
            $totalEmp = $challenge->total_employees_snapshot ?? 0;
            $totalSub = $challenge->total_submitted_snapshot ?? $challenge->submissions_count ?? 0;
            $progress = $totalEmp > 0 ? round(($totalSub / $totalEmp) * 100) : 0;
        @endphp
        <div class="hr-card overflow-hidden card-hover group">
            <div class="relative h-2 bg-gradient-to-r from-primary-500 to-primary-300">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-green-400 transition-all duration-500" style="width: {{ $progress }}%"></div>
            </div>
            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-primary-100 transition-colors">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 text-sm truncate">{{ $challenge->title }}</h3>
                            @if($challenge->description)
                                <p class="text-xs text-slate-500 truncate mt-0.5">{{ $challenge->description }}</p>
                            @endif
                        </div>
                    </div>
                    <span class="hr-badge {{ $isPast ? 'hr-badge--neutral' : 'hr-badge--success' }} ml-2 flex-shrink-0">
                        {{ $isPast ? 'Expired' : 'Active' }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="text-center p-2 bg-slate-50/80 rounded-lg">
                        <p class="text-lg font-bold text-slate-900 tracking-tight">{{ $totalEmp }}</p>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase">Employees</p>
                    </div>
                    <div class="text-center p-2 bg-emerald-50/80 rounded-lg">
                        <p class="text-lg font-bold text-emerald-600 tracking-tight">{{ $totalSub }}</p>
                        <p class="text-[10px] font-semibold text-emerald-600 uppercase">Submitted</p>
                    </div>
                    <div class="text-center p-2 bg-amber-50/80 rounded-lg">
                        <p class="text-lg font-bold text-amber-600 tracking-tight">{{ $totalEmp - $totalSub }}</p>
                        <p class="text-[10px] font-semibold text-amber-600 uppercase">Pending</p>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-slate-500 font-medium">Progress</span>
                        <span class="font-bold text-slate-700">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-slate-200/80 rounded-full h-2">
                        <div class="bg-gradient-to-r from-emerald-500 to-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                @if($challenge->deadline)
                    <p class="text-xs text-slate-400 mb-4">
                        <svg class="inline w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Deadline: {{ $challenge->deadline->format('d M Y, H:i') }}
                    </p>
                @endif

                <div class="flex gap-2">
                    <a href="{{ route('hr.video-challenges.show', $challenge) }}" class="flex-1 hr-btn-primary text-xs py-2 justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Detail
                    </a>
                    <a href="{{ route('hr.video-challenges.edit', $challenge) }}" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-all duration-200 text-xs font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form action="{{ route('hr.video-challenges.destroy', $challenge) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition-all duration-200 text-xs font-semibold" onclick="return confirm('Delete this challenge?')">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <div class="hr-card p-12 text-center">
                <div class="hr-empty__icon mx-auto mb-6" style="width:80px;height:80px;">
                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No Video Challenges Yet</h3>
                <p class="text-slate-500 mb-6">Create your first video challenge to get started</p>
                <a href="{{ route('hr.video-challenges.create') }}" class="hr-btn-primary inline-flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Challenge
                </a>
            </div>
        </div>
    @endforelse
</div>

@if($challenges instanceof \Illuminate\Pagination\AbstractPaginator && $challenges->hasPages())
    <div class="mt-6">
        {{ $challenges->links() }}
    </div>
@endif
@endsection
