@extends('layouts.app')

@section('title', $materi->title . ' - Materi')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-14 sm:pb-16">

    {{-- ================= KEMBALI ================= --}}
    <a href="{{ route('employee.materi.index') }}"
       class="et-reveal et-press mb-4 inline-flex items-center gap-1.5 text-sm font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Materi
    </a>

    @php
        $isPdf = strtolower($materi->file_type) === 'pdf';
        $isPpt = in_array(strtolower($materi->file_type), ['ppt', 'pptx']);
        $fileIcon = $isPdf ? '📄' : ($isPpt ? '📊' : '📁');
        $uploaderName = $materi->uploader->full_name ?? $materi->uploader->name ?? '';
        $uploaderInitial = strtoupper(substr(trim($uploaderName ?: 'U'), 0, 1));
    @endphp

    {{-- ================= HERO INFO MATERI ================= --}}
    <section class="et-hero et-reveal shadow-2xl shadow-sky-900/20 mb-5 sm:mb-6">
        <div class="et-hero__grid"></div>

        <div class="relative z-10 p-5 sm:p-7 lg:p-8">
            <div class="flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 et-glass rounded-3xl flex items-center justify-center shrink-0 text-3xl sm:text-4xl et-mascot et-mascot--bounce">
                    {{ $fileIcon }}
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="et-glass rounded-full px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-wider">{{ strtoupper($materi->file_type) }}</span>
                        <span class="et-glass rounded-full px-3 py-1.5 text-[11px] font-bold">💾 {{ $materi->file_size_formatted }}</span>
                        <span class="et-glass rounded-full px-3 py-1.5 text-[11px] font-bold">📅 {{ $materi->created_at->format('d M Y') }}</span>
                        <span class="et-glass rounded-full px-3 py-1.5 text-[11px] font-bold">🕐 {{ $materi->created_at->format('H:i') }}</span>
                    </div>

                    <h1 class="mt-3 text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight leading-tight break-words">{{ $materi->title }}</h1>
                    <p class="mt-2 text-sm text-white/80 max-w-2xl">Materi ini dibagikan khusus untuk mendukung proses belajarmu bersama English Today.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= DETAIL & AKSI ================= --}}
    <section class="et-panel et-reveal et-delay-1">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800">
            <h2 class="et-section-title">📖 Tentang Materi Ini</h2>
            <p class="et-section-sub">Baca deskripsinya dulu sebelum mulai dipelajari</p>
        </div>

        <div class="p-5 sm:p-6">
            @if($materi->description)
                <div class="rounded-2xl bg-lime-50/70 dark:bg-lime-500/10 border border-lime-200/70 dark:border-lime-500/25 p-4 sm:p-5">
                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $materi->description }}</p>
                </div>
            @else
                <div class="rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-dashed border-slate-200 dark:border-slate-800 p-4 sm:p-5">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Materi ini tidak memiliki deskripsi. Kamu tetap bisa melihat dan mengunduh filenya di bawah ini.</p>
                </div>
            @endif

            {{-- Tombol aksi --}}
            <div class="mt-5 flex flex-col sm:flex-row gap-2.5">
                <button type="button" onclick="togglePreview()" id="previewToggleBtn"
                        class="et-btn et-btn-primary et-wiggle et-press sm:flex-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span id="previewToggleText">Lihat File</span>
                </button>
                <a href="{{ route('employee.materi.download', $materi) }}"
                   class="et-btn et-btn-outline et-press sm:flex-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </a>
            </div>

            {{-- Pratinjau file inline (tersembunyi, bisa di-toggle) --}}
            <div id="filePreviewSection" class="hidden mt-5">
                <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800">
                    @if($isPdf)
                        <iframe src="{{ $fileUrl }}"
                                title="Pratinjau {{ $materi->title }}"
                                loading="lazy"
                                class="w-full block bg-white"
                                style="height: 80vh; min-height: 420px;"
                                frameborder="0">
                        </iframe>
                    @elseif($isPpt)
                        <iframe src="https://docs.google.com/gview?url={{ urlencode(asset('storage/' . $materi->file_path)) }}&embedded=true"
                                title="Pratinjau {{ $materi->title }}"
                                loading="lazy"
                                class="w-full block bg-white"
                                style="height: 80vh; min-height: 420px;"
                                frameborder="0">
                        </iframe>
                    @else
                        <div class="et-empty py-10">
                            <div class="et-empty__icon" style="width:64px;height:64px;">🗂️</div>
                            <p class="et-empty__title text-[15px]">Pratinjau tidak tersedia</p>
                            <p class="et-empty__text text-[12.5px]">Tipe file {{ strtoupper($materi->file_type) }} belum mendukung pratinjau inline. Gunakan tombol Download untuk membukanya.</p>
                        </div>
                    @endif
                </div>
                <p class="et-hint text-center">
                    Gunakan scroll untuk menavigasi halaman.
                    @if($isPdf)
                        Klik ikon download di pojok kanan atas PDF untuk menyimpan.
                    @endif
                </p>
            </div>

            {{-- Kredit uploader & tombol kembali --}}
            <div class="mt-5 pt-4 border-t border-dashed border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-sky-500 text-white flex items-center justify-center text-xs font-extrabold shrink-0">{{ $uploaderInitial }}</span>
                    <div class="min-w-0">
                        <p class="text-[10.5px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Diupload oleh</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">{{ $uploaderName }}</p>
                    </div>
                </div>

                <a href="{{ route('employee.materi.index') }}" class="et-btn et-btn-soft et-btn-sm et-press self-start sm:self-auto">
                    📚 Lihat Materi Lainnya
                </a>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    let previewVisible = false;

    function togglePreview() {
        const section = document.getElementById('filePreviewSection');
        const btn = document.getElementById('previewToggleText');

        previewVisible = !previewVisible;

        if (previewVisible) {
            section.classList.remove('hidden');
            btn.textContent = 'Sembunyikan File';
        } else {
            section.classList.add('hidden');
            btn.textContent = 'Lihat File';
        }
    }
</script>
@endpush
@endsection
