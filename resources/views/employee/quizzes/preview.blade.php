@extends('layouts.app')

@section('title', 'Detail Kuis - ' . $quiz->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-14 sm:pb-16">

    {{-- Page header --}}
    <header class="et-reveal mb-6 sm:mb-8">
        <p class="et-eyebrow">🧩 Detail Kuis</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white break-words">{{ $quiz->title }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Tinjau detail di bawah ini sebelum memulai pengerjaan.</p>
    </header>

    {{-- Quick info: 3 stat cards --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="et-stat et-reveal et-delay-1">
            <div class="flex items-center gap-3">
                <div class="et-stat__icon bg-lime-100 text-lime-600 dark:bg-lime-500/15 dark:text-lime-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="et-badge et-badge-info">Soal</span>
            </div>
            <p class="et-stat__value mt-3">{{ $quiz->questions()->count() }}</p>
            <p class="et-stat__label mt-0.5">Jumlah soal</p>
        </div>

        <div class="et-stat et-reveal et-delay-2">
            <div class="flex items-center gap-3">
                <div class="et-stat__icon bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="et-badge et-badge-warning">Waktu</span>
            </div>
            <p class="et-stat__value mt-3">{{ $quiz->duration }}<span class="text-base font-bold ml-1">menit</span></p>
            <p class="et-stat__label mt-0.5">Durasi pengerjaan</p>
        </div>

        <div class="et-stat et-reveal et-delay-3">
            <div class="flex items-center gap-3">
                <div class="et-stat__icon bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="et-badge et-badge-success">Status</span>
            </div>
            <p class="et-stat__value mt-3 text-emerald-600 dark:text-emerald-400">Aktif</p>
            <p class="et-stat__label mt-0.5">Siap diikuti</p>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        {{-- MAIN: description + instructions --}}
        <div class="lg:col-span-2 space-y-6">
            @if($quiz->description)
                <section class="et-panel et-reveal et-delay-4">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800">
                        <h2 class="et-section-title">📄 Deskripsi Kuis</h2>
                        <p class="et-section-sub">Gambaran umum materi yang diuji</p>
                    </div>
                    <div class="p-5 sm:p-6">
                        <p class="text-sm sm:text-[15px] leading-relaxed text-slate-600 dark:text-slate-300">{{ $quiz->description }}</p>
                    </div>
                </section>
            @endif

            <section class="et-panel et-reveal et-delay-5">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800">
                    <h2 class="et-section-title">📋 Petunjuk Pengerjaan</h2>
                    <p class="et-section-sub">Baca baik-baik sebelum menekan tombol mulai</p>
                </div>
                <div class="p-5 sm:p-6">
                    <ul class="space-y-3">
                        @php
                            $instructions = [
                                'Baca setiap soal dengan cermat sebelum menjawab.',
                                'Kamu punya waktu ' . $quiz->duration . ' menit untuk menyelesaikan kuis ini.',
                                'Jawabanmu tersimpan otomatis selama pengerjaan berlangsung.',
                                'Kamu bisa meninjau jawaban sebelum mengirim.',
                                'Setelah dikirim, jawaban tidak dapat diubah lagi.',
                            ];
                        @endphp
                        @foreach($instructions as $i => $text)
                            <li class="flex items-start gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-800">
                                <span class="shrink-0 w-7 h-7 rounded-xl bg-gradient-to-br from-lime-500 to-sky-500 text-white text-[12px] font-extrabold flex items-center justify-center shadow-md shadow-lime-500/30">{{ $i + 1 }}</span>
                                <span class="text-[13.5px] leading-snug text-slate-600 dark:text-slate-300">{{ $text }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        </div>

        {{-- SIDEBAR: summary + start --}}
        <aside class="space-y-6">
            <div class="et-panel et-reveal et-delay-6">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800">
                    <h2 class="et-section-title">🚀 Siap Memulai?</h2>
                    <p class="et-section-sub">Pastikan koneksi dan fokusmu stabil</p>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="flex flex-wrap gap-2">
                        <span class="et-chip">❓ {{ $quiz->questions()->count() }} soal</span>
                        <span class="et-chip">⏱️ {{ $quiz->duration }} menit</span>
                        @if($quiz->category)
                            <span class="et-chip">🗂️ {{ $quiz->category->name }}</span>
                        @endif
                    </div>

                    <div class="mt-5 p-4 rounded-2xl border border-amber-200/70 dark:border-amber-500/25 bg-amber-50/70 dark:bg-amber-500/10">
                        <p class="text-[13px] leading-snug text-amber-800 dark:text-amber-200">
                            ⚠️ Timer langsung berjalan begitu kamu menekan tombol <strong>Mulai Kuis</strong>. Selesaikan dalam satu sesi.
                        </p>
                    </div>

                    <form action="{{ route('employee.quizzes.start', $quiz) }}" method="POST" class="mt-5">
                        @csrf
                        <button type="submit"
                                class="et-btn et-btn-primary et-btn-lg et-btn-block et-press et-wiggle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Mulai Kuis
                        </button>
                    </form>

                    <a href="{{ route('employee.quizzes.join') }}"
                       class="et-btn et-btn-outline et-btn-block et-press mt-3 justify-center">← Kembali ke Join</a>

                    <p class="et-hint text-center mt-3">Butuh kuis lain? Masukkan kode enrollment yang berbeda.</p>
                </div>
            </div>

            <div class="et-panel et-reveal et-delay-7">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800">
                    <h2 class="et-section-title">🎯 Skor &amp; Pembahasan</h2>
                </div>
                <div class="p-5 sm:p-6 space-y-2.5">
                    <div class="flex items-center justify-between gap-3 text-[13px]">
                        <span class="text-slate-500 dark:text-slate-400">Nilai ditampilkan</span>
                        <span class="et-badge {{ $quiz->show_score ? 'et-badge-success' : 'et-badge-neutral' }}">{{ $quiz->show_score ? 'Ya' : 'Tidak' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-[13px]">
                        <span class="text-slate-500 dark:text-slate-400">Kunci jawaban</span>
                        <span class="et-badge {{ $quiz->show_correct_answer ? 'et-badge-success' : 'et-badge-neutral' }}">{{ $quiz->show_correct_answer ? 'Ditampilkan' : 'Disembunyikan' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-[13px]">
                        <span class="text-slate-500 dark:text-slate-400">Pembahasan soal</span>
                        <span class="et-badge {{ $quiz->show_explanation ? 'et-badge-success' : 'et-badge-neutral' }}">{{ $quiz->show_explanation ? 'Ditampilkan' : 'Disembunyikan' }}</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
