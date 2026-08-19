@extends('layouts.hr')

@section('title', 'Materi')
@section('header-title', 'Materi')
@section('header-subtitle', 'Upload dan kelola materi untuk karyawan')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-stagger">
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-primary-50">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">Total</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Materi</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $totalMateri }}</p>
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
        <p class="text-sm font-medium text-slate-500">Aktif</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $activeMateri }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-red-50">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">PDF</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $pdfCount }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-orange-50">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">PPT</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $pptCount }}</p>
    </div>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h3 class="text-base font-bold text-slate-900 tracking-tight">Semua Materi</h3>
    <a href="{{ route('hr.materi.create') }}" class="hr-btn-primary w-full sm:w-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Upload Materi
    </a>
</div>

<div class="table-responsive">
    <div class="hr-card overflow-hidden">
        @forelse($materi as $item)
            @if($loop->first && $loop->count === 1)
            <div class="p-5">
            @elseif($loop->first)
            <div class="p-5 border-b border-slate-100/80">
            @elseif(!$loop->last)
            <div class="p-5 border-b border-slate-100/80">
            @else
            <div class="p-5">
            @endif
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        @php
                            $iconBg = match($item->file_type) {
                                'pdf' => 'bg-red-50',
                                'ppt', 'pptx' => 'bg-orange-50',
                                default => 'bg-blue-50',
                            };
                            $iconColor = match($item->file_type) {
                                'pdf' => 'text-red-600',
                                'ppt', 'pptx' => 'text-orange-600',
                                default => 'text-blue-600',
                            };
                        @endphp
                        <div class="w-11 h-11 {{ $iconBg }} rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-slate-900 text-sm truncate">{{ $item->title }}</h4>
                            <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                <span class="hr-badge {{ $item->file_type === 'pdf' ? 'hr-badge--danger' : 'hr-badge--warning' }} text-[10px] px-1.5 py-0.5">
                                    {{ strtoupper($item->file_type) }}
                                </span>
                                <span class="text-[11px] text-slate-400">{{ $item->file_size_formatted }}</span>
                                <span class="text-[11px] text-slate-400">&middot;</span>
                                <span class="text-[11px] text-slate-400">{{ $item->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="hr-badge {{ $item->is_active ? 'hr-badge--success' : 'hr-badge--neutral' }}">
                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        <a href="{{ route('hr.materi.download', $item) }}" class="px-3 py-2 bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-all duration-200 text-xs font-semibold" title="Download">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                        <a href="{{ route('hr.materi.edit', $item) }}" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-all duration-200 text-xs font-semibold" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form action="{{ route('hr.materi.destroy', $item) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition-all duration-200 text-xs font-semibold" onclick="return confirm('Hapus materi ini?')" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="hr-empty__icon mx-auto mb-6" style="width:80px;height:80px;">
                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Materi</h3>
                <p class="text-slate-500 mb-6">Upload materi pertama untuk karyawan</p>
                <a href="{{ route('hr.materi.create') }}" class="hr-btn-primary inline-flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Upload Materi
                </a>
            </div>
        @endforelse
    </div>
</div>

@if($materi instanceof \Illuminate\Pagination\AbstractPaginator && $materi->hasPages())
    <div class="mt-6">
        {{ $materi->links() }}
    </div>
@endif
@endsection
