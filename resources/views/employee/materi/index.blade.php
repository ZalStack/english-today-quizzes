@extends('layouts.app')

@section('title', 'Materi Pembelajaran')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-14 sm:pb-16">

    {{-- ================= HEADER ================= --}}
    <header class="et-reveal mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div class="min-w-0">
                <p class="et-eyebrow">📚 Perpustakaan Materi</p>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Materi Pembelajaran</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Akses materi yang sudah dibagikan oleh HRD kapan saja.</p>
            </div>

            @if($materi->total() > 0)
                <span class="et-badge et-badge-info self-start sm:self-auto shrink-0">🗂️ {{ $materi->total() }} materi tersedia</span>
            @endif
        </div>
    </header>

    {{-- ================= DAFTAR MATERI ================= --}}
    @if($materi->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @foreach($materi as $i => $item)
                @php
                    $isPdf = strtolower($item->file_type) === 'pdf';
                    $isPpt = in_array(strtolower($item->file_type), ['ppt', 'pptx']);
                    $fileIcon = $isPdf ? '📄' : ($isPpt ? '📊' : '📁');
                    $typeBadge = $isPdf ? 'et-badge-danger' : ($isPpt ? 'et-badge-warning' : 'et-badge-info');
                    $accentBar = $isPdf
                        ? 'from-rose-500 to-orange-400'
                        : ($isPpt ? 'from-amber-500 to-yellow-400' : 'from-lime-500 to-emerald-400');
                    $fileTile = $isPdf
                        ? 'bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400'
                        : ($isPpt ? 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400' : 'bg-lime-100 text-lime-600 dark:bg-lime-500/15 dark:text-lime-400');
                @endphp

                <article class="et-card et-card-hover et-focus-ring et-reveal et-delay-{{ min(8, $i + 1) }} overflow-hidden flex flex-col">
                    <div class="h-1.5 bg-gradient-to-r {{ $accentBar }}"></div>

                    <div class="p-4 sm:p-5 flex flex-col flex-1">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-2xl shrink-0 flex items-center justify-center text-2xl border border-transparent et-hover-pop {{ $fileTile }}">
                                {{ $fileIcon }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-[15px] leading-snug text-slate-900 dark:text-slate-100 line-clamp-2">{{ $item->title }}</h3>
                                <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                    <span class="et-badge {{ $typeBadge }}">{{ strtoupper($item->file_type) }}</span>
                                    <span class="et-badge et-badge-neutral">💾 {{ $item->file_size_formatted }}</span>
                                </div>
                            </div>
                        </div>

                        <p class="mt-3 text-[13px] leading-relaxed text-slate-500 dark:text-slate-400 line-clamp-2">
                            {{ $item->description ?: 'Materi ini belum memiliki deskripsi.' }}
                        </p>

                        <div class="mt-auto pt-4 border-t border-dashed border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2.5">
                            <span class="inline-flex items-center gap-1.5 text-[11.5px] font-semibold text-slate-400 dark:text-slate-500">
                                📅 {{ $item->created_at->format('d M Y') }}
                            </span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('employee.materi.show', $item) }}"
                                   class="et-btn et-btn-soft et-btn-sm et-press">
                                    Detail
                                </a>
                                <a href="{{ route('employee.materi.download', $item) }}"
                                   class="et-btn et-btn-primary et-btn-sm et-press">
                                    ⬇️ Download
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if($materi->hasPages())
            <div class="mt-6 sm:mt-8 et-reveal et-delay-1">
                {{ $materi->links() }}
            </div>
        @endif
    @else
        {{-- ================= EMPTY STATE ================= --}}
        <div class="et-panel et-reveal">
            <div class="et-empty">
                <div class="et-empty__icon et-mascot et-mascot--bounce">📚</div>
                <p class="et-empty__title">Belum ada materi</p>
                <p class="et-empty__text">Belum ada materi yang dibagikan. Pantau terus, ya — materi baru dari HRD akan muncul di halaman ini.</p>
                <a href="{{ route('employee.dashboard') }}" class="et-btn et-btn-primary mt-5 et-press">🏠 Kembali ke Dashboard</a>
            </div>
        </div>
    @endif
</div>
@endsection
