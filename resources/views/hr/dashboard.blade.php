@extends('layouts.hr')

@section('title', 'HR Dashboard')
@section('header-title', 'Dashboard')
@section('header-subtitle')
    Ringkasan aktivitas HR hari ini
@endsection

@php
    $greet = now()->hour < 11 ? 'Selamat pagi' : (now()->hour < 15 ? 'Selamat siang' : (now()->hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $todayLabel = $days[now()->dayOfWeek] . ', ' . now()->day . ' ' . $months[now()->month] . ' ' . now()->year;
@endphp

@section('content')
{{-- Welcome Hero --}}
<div class="hr-hero">
    <span class="hr-hero__eyebrow">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ $todayLabel }}
    </span>

    <h2 class="hr-hero__title">{{ $greet }}, {{ auth()->user()->full_name }}!</h2>
    <p class="hr-hero__subtitle">
        Pantau karyawan, kuis, dan materi pelatihan dalam satu tempat. Semua yang Anda butuhkan untuk mengelola tim hari ini.
    </p>

    <div class="hr-quick-grid">
        <a href="{{ route('hr.employees.create') }}" class="hr-quick-card">
            <span class="hr-quick-card__icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </span>
            <span>Tambah Karyawan<span class="hr-quick-card__meta">Buat akun baru</span></span>
        </a>

        <a href="{{ route('hr.quizzes.create') }}" class="hr-quick-card">
            <span class="hr-quick-card__icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </span>
            <span>Buat Kuis<span class="hr-quick-card__meta">Soal &amp; evaluasi</span></span>
        </a>

        <a href="{{ route('hr.reports.index') }}" class="hr-quick-card">
            <span class="hr-quick-card__icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </span>
            <span>Lihat Laporan<span class="hr-quick-card__meta">Skor &amp; partisipasi</span></span>
        </a>

        <a href="https://sikekar.read1kpmseikhlasnya.com" target="_blank" rel="noopener noreferrer"
           class="hr-quick-card" title="Buka portal SIKEKAR di tab baru">
            <span class="hr-quick-card__icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-8.995-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </span>
            <span>Portal SIKEKAR<span class="hr-quick-card__meta">Buka di tab baru ↗</span></span>
        </a>
    </div>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-8 hr-stagger">
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-4">
            <div class="hr-stat__icon bg-blue-50">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="hr-badge hr-badge--info">Karyawan</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Employees</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-1 tracking-tight">{{ $totalEmployees }}</p>
    </div>

    <div class="hr-stat">
        <div class="flex items-center justify-between mb-4">
            <div class="hr-stat__icon bg-indigo-50">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="hr-badge hr-badge--neutral">Struktur</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Divisions</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-1 tracking-tight">{{ $totalDivisions }}</p>
    </div>

    <div class="hr-stat">
        <div class="flex items-center justify-between mb-4">
            <div class="hr-stat__icon bg-emerald-50">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <span class="hr-badge hr-badge--success">{{ $activeQuizzes }} aktif</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Quizzes</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-1 tracking-tight">{{ $totalQuizzes }}</p>
    </div>

    <div class="hr-stat">
        <div class="flex items-center justify-between mb-4">
            <div class="hr-stat__icon bg-amber-50">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="hr-badge hr-badge--warning">Unik</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Participants</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-1 tracking-tight">{{ $totalParticipants }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6">
    {{-- Recent Activity --}}
    <div class="lg:col-span-2 hr-card overflow-hidden">
        <div class="hr-card__header">
            <div>
                <h3 class="hr-card__title">Recent Activity</h3>
                <p class="hr-card__sub">Percobaan kuis terbaru dari karyawan</p>
            </div>
            <a href="{{ route('hr.reports.index') }}" class="hr-btn-xs hr-btn-xs--primary">
                View All
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="hr-card__body">
            @if($recentAttempts->count() > 0)
                <div class="space-y-3">
                    @foreach($recentAttempts as $attempt)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50/80 rounded-2xl hover:bg-blue-50/60 transition-all duration-200 gap-3 border border-transparent hover:border-blue-100">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="hr-avatar w-10 h-10 text-sm">
                                    {{ strtoupper(substr($attempt->user->full_name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900 text-sm truncate">{{ $attempt->user->full_name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ $attempt->quiz->title }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 sm:flex-shrink-0">
                                <span class="hr-badge {{ ($attempt->score ?? 0) >= 70 ? 'hr-badge--success' : 'hr-badge--danger' }}">
                                    {{ $attempt->score ?? 'N/A' }}%
                                </span>
                                <p class="text-xs text-slate-400 whitespace-nowrap">{{ $attempt->completed_at?->diffForHumans() ?? 'In Progress' }}</p>
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
                    <p class="hr-empty__title">Belum ada aktivitas</p>
                    <p class="hr-empty__desc">Percobaan kuis akan muncul di sini</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Leaderboard --}}
    <div class="hr-card overflow-hidden">
        <div class="hr-card__header">
            <div>
                <h3 class="hr-card__title">Leaderboard</h3>
                <p class="hr-card__sub">Peringkat top performer</p>
            </div>
            <span class="hr-badge hr-badge--warning">Top 5</span>
        </div>
        <div class="hr-card__body">
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
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/80 hover:bg-blue-50/60 transition-all duration-200 gap-3 border border-transparent hover:border-blue-100">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-7 h-7 flex-shrink-0 flex items-center justify-center font-bold text-xs rounded-lg {{ $rank <= 3 ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-500' }}">
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
                    <p class="hr-empty__title">Belum ada data</p>
                    <p class="hr-empty__desc">Leaderboard akan terisi setelah ada kuis yang diselesaikan</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
