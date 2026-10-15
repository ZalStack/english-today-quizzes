@extends('layouts.app')

@section('title', 'Hasil Kuis')

@section('content')
@php
    $scoreValue = $attempt->score !== null ? (float) $attempt->score : 0;
    $ringCircumference = 2 * pi() * 42; // r = 42
    $ringOffset = $ringCircumference - ($ringCircumference * min(100, max(0, $scoreValue)) / 100);
    $durationMinutes = $attempt->completed_at ? (int) $attempt->started_at->diffInMinutes($attempt->completed_at) : null;
    $isPassed = $attempt->score !== null && $attempt->score >= 70;
    $canReview = $attempt->quiz->show_correct_answer || $attempt->quiz->show_wrong_answer;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-14 sm:pb-16">

    {{-- Page header --}}
    <header class="et-reveal mb-6 sm:mb-8">
        <p class="et-eyebrow">🏁 Hasil Kuis</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
            @if($attempt->quiz->show_score && $isPassed)
                Kuis Selesai — kamu lulus! 🎉
            @elseif($attempt->quiz->show_score)
                Kuis Selesai — hampir sampai! 💪
            @else
                Kuis Selesai — kerja bagus! ✨
            @endif
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 break-words">{{ $attempt->quiz->title }}</p>
    </header>

    {{-- ================= POIN YANG DIDAPAT ================= --}}
    @php
        $earnedPoints = session('quiz_points');
        $pointsBonus  = session('quiz_points_bonus');
        $stageUpMsg   = session('pet_stage_up');
    @endphp

    @if($earnedPoints !== null)
        <section class="et-reveal-pop mb-6 sm:mb-8">
            <div class="relative overflow-hidden rounded-3xl border-2 border-amber-300 dark:border-amber-400/40 bg-gradient-to-r from-amber-50 via-yellow-50 to-orange-50 dark:from-amber-500/15 dark:via-yellow-500/10 dark:to-orange-500/10 shadow-lg shadow-amber-500/10 p-5 sm:p-6">
                <div class="flex items-center gap-4 sm:gap-5">
                    {{-- Koin besar + koin terbang (sekali jalan) --}}
                    <div class="relative shrink-0 w-14 h-14 sm:w-16 sm:h-16 flex items-center justify-center" aria-hidden="true">
                        <span class="text-5xl sm:text-6xl leading-none et-float">🪙</span>
                        <span class="et-coin-fly absolute inset-0 flex items-center justify-center text-5xl">🪙</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-300">Poin masuk ke dompetmu</p>
                        <p class="mt-0.5 text-3xl sm:text-4xl font-extrabold leading-none bg-gradient-to-r from-amber-500 via-yellow-500 to-orange-500 dark:from-amber-300 dark:via-yellow-200 dark:to-orange-300 bg-clip-text text-transparent">
                            +{{ number_format((int) $earnedPoints, 0, ',', '.') }} POIN!
                        </p>
                        @if($pointsBonus !== null)
                            <p class="mt-1.5 text-[12.5px] font-semibold text-slate-500 dark:text-slate-400">
                                Termasuk bonus +{{ number_format((int) $pointsBonus, 0, ',', '.') }} dari skor ✨
                            </p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('employee.shop.index') }}"
                   class="mt-4 inline-flex et-btn et-btn-warning et-btn-sm et-press et-focus-ring">
                    🛒 Belanja poinmu di Toko
                </a>
            </div>
        </section>
    @endif

    {{-- ================= ANNOUNCEMENT NAIK TAHAP ================= --}}
    @if($stageUpMsg)
        <section class="et-reveal-pop mb-6 sm:mb-8">
            <div class="rounded-3xl border-2 border-lime-300 dark:border-lime-400/40 bg-gradient-to-r from-lime-50 via-sky-50 to-emerald-50 dark:from-lime-500/15 dark:via-sky-500/10 dark:to-emerald-500/10 shadow-lg shadow-lime-500/10 px-5 py-4 flex items-center gap-3.5">
                <span class="text-3xl sm:text-4xl leading-none et-wiggle shrink-0" aria-hidden="true">🎉</span>
                <div class="min-w-0">
                    <p class="text-[11px] font-extrabold uppercase tracking-widest text-lime-600 dark:text-lime-400">Kabar Gembira!</p>
                    <p class="mt-0.5 text-[15px] sm:text-base font-extrabold text-slate-800 dark:text-slate-100 leading-snug break-words">{{ $stageUpMsg }}</p>
                </div>
            </div>
        </section>
    @endif

    {{-- Hero: score ring --}}
    @if($attempt->quiz->show_score)
        <section class="et-reveal et-delay-1 mb-6 sm:mb-8">
            <div class="et-hero shadow-2xl shadow-sky-900/20">
                <div class="et-hero__grid"></div>

                {{-- Falling confetti (celebration) --}}
                @if($isPassed)
                    <div class="et-confetti" aria-hidden="true">
                        @foreach([8, 22, 35, 49, 61, 73, 86, 94] as $i)
                            <i style="left: {{ $i }}%; background: {{ ['#fff','#ffc800','#ff9600','#ce82ff'][$loop->index % 4] }};
                                       animation-duration: {{ 3.4 + ($i % 5) * .6 }}s;
                                       animation-delay: {{ ($i % 7) * .5 }}s;"></i>
                        @endforeach
                    </div>
                @endif

                <div class="relative z-10 p-6 sm:p-8 lg:p-10">
                    <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 lg:gap-10">
                        {{-- Ring --}}
                        <div class="relative shrink-0">
                            <span class="et-mascot et-mascot--bounce absolute -top-3 -right-3 w-11 h-11 text-2xl z-10" aria-hidden="true">{{ $isPassed ? '🎉' : '💪' }}</span>
                            <svg viewBox="0 0 100 100" class="w-32 h-32 sm:w-40 sm:h-40 -rotate-90">
                                <circle cx="50" cy="50" r="42" fill="none" stroke-width="9"
                                        class="stroke-white/25"/>
                                <circle cx="50" cy="50" r="42" fill="none" stroke-width="9" stroke-linecap="round"
                                        class="stroke-white transition-all duration-1000"
                                        stroke-dasharray="{{ number_format($ringCircumference, 2) }}"
                                        stroke-dashoffset="{{ number_format($ringOffset, 2) }}"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-3xl sm:text-4xl font-extrabold leading-none">{{ $attempt->score !== null ? $attempt->score . '%' : '—' }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-white/75 mt-1">Nilai</span>
                            </div>
                        </div>

                        {{-- Meta --}}
                        <div class="flex-1 min-w-0 text-center sm:text-left">
                            <span class="inline-flex items-center gap-2 et-glass rounded-full px-3 py-1.5 text-[11px] font-bold tracking-wide uppercase">
                                <span class="w-2 h-2 rounded-full {{ $isPassed ? 'bg-emerald-300' : 'bg-rose-300' }} et-pulse-dot"></span>
                                Nilai akhir • {{ $isPassed ? 'Lulus' : 'Belum lulus' }}
                            </span>

                            <h2 class="mt-3 text-3xl sm:text-5xl font-extrabold tracking-tight leading-none">
                                @if($scoreValue >= 85)
                                    Luar Biasa! 🏆
                                @elseif($scoreValue >= 70)
                                    Kerja Bagus! 🎉
                                @else
                                    Ayo Bisa Lagi! 💪
                                @endif
                            </h2>
                            <p class="mt-2 text-sm text-white/80 max-w-lg mx-auto sm:mx-0">
                                {{ $isPassed
                                    ? 'Hebat, kamu menaklukkan kuis ini! Pertahankan semangatmu di kuis berikutnya. 🚀'
                                    : 'Jangan menyerah! Pelajari soal yang salah di bawah, lalu coba lagi — kamu pasti bisa. 💪' }}
                            </p>

                            <div class="mt-4 et-progress bg-white/25 max-w-lg mx-auto sm:mx-0">
                                <div class="et-progress__bar et-progress--flat bg-white"
                                     style="width: {{ min(100, max(0, $scoreValue)) }}%; background-image: linear-gradient(135deg,#58cc02,#1cb0f6);"></div>
                            </div>

                            <div class="mt-4 flex flex-wrap justify-center sm:justify-start gap-2">
                                <span class="et-glass rounded-full px-3 py-1.5 text-[11px] font-bold uppercase tracking-widest">Lulus ≥ 70%</span>
                                <span class="et-glass rounded-full px-3 py-1.5 text-[11px] font-bold uppercase tracking-widest inline-block max-w-full truncate align-middle">🏆 {{ $attempt->quiz->title }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Correct / Wrong / Time --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8">
            <div class="et-stat et-reveal et-delay-2">
                <div class="flex items-center justify-between">
                    <div class="et-stat__icon bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="et-badge et-badge-success">Benar</span>
                </div>
                <p class="et-stat__value mt-3">{{ $attempt->total_correct }}</p>
                <p class="et-stat__label mt-0.5">Jawaban benar</p>
            </div>

            <div class="et-stat et-reveal et-delay-3">
                <div class="flex items-center justify-between">
                    <div class="et-stat__icon bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <span class="et-badge et-badge-danger">Salah</span>
                </div>
                <p class="et-stat__value mt-3">{{ $attempt->total_wrong }}</p>
                <p class="et-stat__label mt-0.5">Jawaban salah</p>
            </div>

            <div class="et-stat et-reveal et-delay-4">
                <div class="flex items-center justify-between">
                    <div class="et-stat__icon bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="et-badge et-badge-info">Waktu</span>
                </div>
                <p class="et-stat__value mt-3">{{ $durationMinutes !== null ? $durationMinutes : '—' }}<span class="text-base font-bold ml-1">menit</span></p>
                <p class="et-stat__label mt-0.5">Durasi pengerjaan</p>
            </div>
        </section>
    @else
        <section class="et-panel et-reveal et-delay-1 mb-6 sm:mb-8">
            <div class="et-empty py-10">
                <div class="et-empty__icon">🔒</div>
                <p class="et-empty__title">Nilai tidak ditampilkan</p>
                <p class="et-empty__text">Pengaturan kuis ini menyembunyikan skor. Hubungi HRD jika kamu ingin mengetahui hasil detailmu.</p>
            </div>
        </section>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        {{-- Review jawaban --}}
        <div class="lg:col-span-2">
            @if($canReview)
                <section class="et-panel et-reveal et-delay-5">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="et-section-title">📝 Review Jawaban</h2>
                            <p class="et-section-sub">Bandingkan jawabanmu dengan kunci jawaban</p>
                        </div>
                        @if($attempt->quiz->show_score)
                            <span class="et-badge {{ $isPassed ? 'et-badge-success' : 'et-badge-warning' }}">
                                {{ $attempt->total_correct }} benar • {{ $attempt->total_wrong }} salah
                            </span>
                        @endif
                    </div>

                    <div class="p-4 sm:p-5 space-y-3 sm:space-y-4">
                        @forelse($attempt->answers as $index => $answer)
                            <article class="rounded-2xl border p-4 sm:p-5 transition
                                {{ $answer->is_correct
                                    ? 'bg-emerald-50/70 border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-500/30'
                                    : 'bg-rose-50/70 border-rose-200 dark:bg-rose-500/10 dark:border-rose-500/30' }}">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2.5">
                                    <h3 class="flex-1 min-w-0 text-[14.5px] font-bold leading-snug text-slate-900 dark:text-slate-100 break-words">
                                        <span class="text-sky-600 dark:text-sky-400 font-extrabold mr-1.5">{{ $index + 1 }}.</span>
                                        {{ $answer->question->question_text }}
                                    </h3>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="et-badge {{ $answer->is_correct ? 'et-badge-success' : 'et-badge-danger' }}">
                                            {{ $answer->is_correct ? '✓ Benar' : '✗ Salah' }}
                                        </span>
                                        <span class="et-badge et-badge-neutral">{{ $answer->points_earned }}/{{ $answer->question->points }} poin</span>
                                    </div>
                                </div>

                                <div class="mt-3 space-y-2">
                                    @if($attempt->quiz->show_correct_answer)
                                        <div class="flex items-start gap-2.5 rounded-xl bg-white dark:bg-slate-900/70 border border-emerald-200/80 dark:border-emerald-500/25 px-3 py-2.5">
                                            <span class="shrink-0 w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-[12px] font-extrabold">✓</span>
                                            <p class="text-[13.5px] leading-snug text-emerald-700 dark:text-emerald-300 break-words">
                                                <strong>Kunci jawaban:</strong> {{ $answer->question->correct_answer }}
                                            </p>
                                        </div>
                                    @endif

                                    @if($attempt->quiz->show_wrong_answer && !$answer->is_correct)
                                        <div class="flex items-start gap-2.5 rounded-xl bg-white dark:bg-slate-900/70 border border-rose-200/80 dark:border-rose-500/25 px-3 py-2.5">
                                            <span class="shrink-0 w-5 h-5 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-[12px] font-extrabold">✗</span>
                                            <p class="text-[13.5px] leading-snug text-rose-700 dark:text-rose-300 break-words">
                                                <strong>Jawabanmu:</strong> {{ $answer->answer }}
                                            </p>
                                        </div>
                                    @endif

                                    @if($attempt->quiz->show_explanation && $answer->question->explanation)
                                        <div class="flex items-start gap-2.5 rounded-xl bg-sky-50/80 dark:bg-sky-500/10 border border-sky-200/80 dark:border-sky-500/25 px-3 py-2.5">
                                            <span class="shrink-0 text-[13px]">💡</span>
                                            <p class="text-[13.5px] leading-snug text-sky-800 dark:text-sky-200 break-words">
                                                <strong>Pembahasan:</strong> {{ $answer->question->explanation }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="et-empty py-8">
                                <div class="et-empty__icon" style="width:64px;height:64px;">🗂️</div>
                                <p class="et-empty__title text-[15px]">Belum ada jawaban tersimpan</p>
                                <p class="et-empty__text text-[12.5px]">Tidak ada data jawaban untuk percobaan ini.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            @else
                <section class="et-panel et-reveal et-delay-5">
                    <div class="et-empty py-10">
                        <div class="et-empty__icon">🙈</div>
                        <p class="et-empty__title">Review jawaban disembunyikan</p>
                        <p class="et-empty__text">Kuis ini tidak menampilkan kunci jawaban. Kamu tetap bisa melihat riwayat nilai lainnya.</p>
                    </div>
                </section>
            @endif
        </div>

        {{-- Sidebar aksi --}}
        <aside class="space-y-6">
            <div class="et-panel et-reveal et-delay-6">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800">
                    <h2 class="et-section-title">🚀 Langkah Berikutnya</h2>
                    <p class="et-section-sub">Teruskan progres belajarmu</p>
                </div>
                <div class="p-5 sm:p-6">
                    <a href="{{ route('employee.dashboard') }}"
                       class="et-btn et-btn-primary et-btn-lg et-btn-block et-press et-wiggle">
                        🏠 Kembali ke Dashboard
                    </a>
                    <a href="{{ route('employee.quizzes.history') }}"
                       class="et-btn et-btn-outline et-btn-block et-press mt-3">🕒 Lihat Riwayat Kuis</a>

                    <div class="mt-5 p-4 rounded-2xl border border-sky-200/70 dark:border-sky-500/25 bg-sky-50/70 dark:bg-sky-500/10">
                        <p class="text-[13px] leading-snug text-slate-700 dark:text-slate-200">
                            💬 Tip: periksa soal yang salah, catat polanya, lalu coba kuis berikutnya — konsistensi mengalahkan kecerdasan sesaat.
                        </p>
                    </div>
                </div>
            </div>

            <div class="et-panel et-reveal et-delay-7">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800">
                    <h2 class="et-section-title">📋 Info Percobaan</h2>
                </div>
                <div class="p-5 sm:p-6 space-y-3">
                    <div class="flex items-center justify-between gap-3 text-[13px]">
                        <span class="text-slate-500 dark:text-slate-400">Status</span>
                        <span class="et-badge {{ $attempt->status === 'completed' ? 'et-badge-success' : 'et-badge-warning' }}">
                            {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-[13px]">
                        <span class="text-slate-500 dark:text-slate-400">Mulai</span>
                        <span class="font-bold text-slate-800 dark:text-slate-100">{{ $attempt->started_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-[13px]">
                        <span class="text-slate-500 dark:text-slate-400">Selesai</span>
                        <span class="font-bold text-slate-800 dark:text-slate-100">{{ $attempt->completed_at?->format('d M Y, H:i') ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-[13px]">
                        <span class="text-slate-500 dark:text-slate-400">Total soal</span>
                        <span class="font-bold text-slate-800 dark:text-slate-100">{{ $attempt->answers->count() }}</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
