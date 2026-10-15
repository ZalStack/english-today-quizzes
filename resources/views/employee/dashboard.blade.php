@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $hour = (int) now()->format('G');
    $greeting = match (true) {
        $hour < 5 => 'Selamat malam',
        $hour < 11 => 'Selamat pagi',
        $hour < 15 => 'Selamat siang',
        $hour < 19 => 'Selamat sore',
        default => 'Selamat malam',
    };
    $userName = auth()->user()->full_name ?? auth()->user()->name;
    $daysId = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $todayLabel = $daysId[(int) now()->format('w')] . ', ' . now()->format('d M Y');
    $avg = round($statistics['average_score'], 1);
    $ringCircumference = 2 * pi() * 26; // r = 26
    $ringOffset = $ringCircumference - ($ringCircumference * min(100, $avg) / 100);
@endphp

{{-- ================= HERO ================= --}}
<section class="relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8">
        <div class="et-hero et-reveal shadow-2xl shadow-sky-900/20">
            <div class="et-hero__grid"></div>

            {{-- Falling confetti (decorative) --}}
            <div class="et-confetti" aria-hidden="true">
                @foreach([8, 22, 35, 49, 61, 73, 86, 94] as $i)
                    <i style="left: {{ $i }}%; background: {{ ['#fff','#ffc800','#ff9600','#ce82ff'][$loop->index % 4] }};
                               animation-duration: {{ 3.4 + ($i % 5) * .6 }}s;
                               animation-delay: {{ ($i % 7) * .5 }}s;"></i>
                @endforeach
            </div>

            <div class="relative z-10 p-6 sm:p-8 lg:p-10">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-10">
                    {{-- Greeting --}}
                    <div class="flex-1 min-w-0">
                        <span class="inline-flex items-center gap-2 et-glass rounded-full px-3 py-1.5 text-[11px] font-bold tracking-wide uppercase">
                            <span class="w-2 h-2 rounded-full bg-lime-300 et-pulse-dot"></span>
                            Sistem aktif • {{ $todayLabel }}
                        </span>

                        <div class="mt-3 flex items-start gap-3">
                            <span class="et-mascot et-mascot--bounce w-14 h-14 sm:w-16 sm:h-16 text-3xl sm:text-4xl shrink-0" aria-hidden="true">🦉</span>
                            <div class="min-w-0">
                                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight">
                                    {{ $greeting }}, <span class="break-words">{{ $userName }}</span>! 👋
                                </h1>
                                <p class="mt-1.5 text-sm sm:text-base text-white/85 max-w-xl font-semibold">
                                    {{ $ongoingQuizzes->count() > 0
                                        ? 'Kamu punya kuis yang sedang berjalan — lanjutkan sebelum waktunya habis! 💪'
                                        : 'Ayo tingkatkan skill bahasa Inggrismu hari ini: kuis, video challenge, dan materi baru menunggumu! ✨' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-2.5">
                            @if($ongoingQuizzes->count() > 0)
                                <a href="{{ route('employee.quizzes.take', $ongoingQuizzes->first()->id) }}"
                                   class="et-btn et-btn-lg bg-white !text-[#3c3c3c] shadow-[0_5px_0_rgba(0,0,0,.18)] et-press">
                                    ▶️ Lanjutkan Kuis
                                </a>
                            @else
                                <a href="{{ route('employee.quizzes.join') }}"
                                   class="et-btn et-btn-lg bg-white !text-[#3c3c3c] shadow-[0_5px_0_rgba(0,0,0,.18)] et-press et-wiggle">
                                    🚀 Mulai Kuis Sekarang
                                </a>
                            @endif
                            <a href="{{ route('employee.materi.index') }}"
                               class="et-btn et-btn-lg et-glass text-white et-press">📚 Buka Materi</a>
                            <a href="https://sikekar.read1kpmseikhlasnya.com" target="_blank" rel="noopener noreferrer"
                               class="et-btn et-btn-lg et-glass text-white et-press">🚀 SIKEKAR</a>
                        </div>
                    </div>

                    {{-- Level & streak panel --}}
                    <div class="w-full lg:w-[320px] shrink-0">
                        <div class="et-glass rounded-3xl p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl shrink-0">⚡</div>
                                    <div class="min-w-0">
                                        <p class="text-[11px] uppercase tracking-widest font-bold text-white/70">Level Kamu</p>
                                        <p class="text-2xl font-extrabold leading-tight">Level {{ $level }}</p>
                                    </div>
                                </div>
                                <span class="text-right text-[11px] font-semibold text-white/80 leading-tight">
                                    {{ number_format($xpIntoLevel) }}<br>/ {{ number_format($levelSize) }} XP
                                </span>
                            </div>

                            <div class="mt-4 et-progress et-progress--flat" style="background: rgba(255,255,255,.28);">
                                <div class="et-progress__bar" style="width: {{ $levelProgress }}%; background-image: linear-gradient(135deg,#fbbf24,#f59e0b);"></div>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                                <div class="bg-white/15 rounded-2xl py-2.5">
                                    <p class="text-lg leading-none">🔥</p>
                                    <p class="text-base font-extrabold mt-1">{{ $streak }} hari</p>
                                    <p class="text-[10px] text-white/70 font-semibold">Streak aktif</p>
                                </div>
                                <div class="bg-white/15 rounded-2xl py-2.5">
                                    <p class="text-lg leading-none">🏆</p>
                                    <p class="text-base font-extrabold mt-1">{{ $bestStreak }} hari</p>
                                    <p class="text-[10px] text-white/70 font-semibold">Streak terbaik</p>
                                </div>
                                <div class="bg-white/15 rounded-2xl py-2.5">
                                    <p class="text-lg leading-none">🎖️</p>
                                    <p class="text-base font-extrabold mt-1">{{ $earnedCount }}/{{ count($achievements) }}</p>
                                    <p class="text-[10px] text-white/70 font-semibold">Badge</p>
                                </div>
                            </div>

                            @if($userPet && $userPet->status === 'hatched' && $userPet->pet)
                                <a href="{{ route('employee.pet.index') }}" class="mt-3 flex items-center justify-between bg-black/25 hover:bg-black/35 transition rounded-2xl px-3.5 py-2.5 text-white">
                                    <span class="text-xs font-semibold truncate">{{ $userPet->pet->element_icon ?? '✨' }} {{ $userPet->nickname ?? $userPet->pet->name }}</span>
                                    <span class="text-[11px] font-bold shrink-0">Lv. {{ $userPet->level }} →</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= CONTENT ================= --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-14 sm:pb-16">

    {{-- Stats --}}
    <section class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 -mt-8 sm:-mt-9 relative z-20 mb-8 sm:mb-10">
        <div class="et-stat et-reveal et-delay-1">
            <div class="flex items-center justify-between">
                <div class="et-stat__icon bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <span class="et-badge et-badge-info">Kuis</span>
            </div>
            <p class="et-stat__value mt-3"><span class="et-count" data-target="{{ $statistics['total_quizzes_taken'] }}">0</span></p>
            <p class="et-stat__label mt-0.5">Kuis diselesaikan</p>
        </div>

        <div class="et-stat et-reveal et-delay-2">
            <div class="flex items-center justify-between">
                <div class="et-stat__icon bg-lime-100 text-lime-600 dark:bg-lime-500/15 dark:text-lime-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <span class="et-badge et-badge-success">Rata-rata</span>
            </div>
            <p class="et-stat__value mt-3"><span class="et-count" data-target="{{ $avg }}" data-decimals="1">0</span>%</p>
            <p class="et-stat__label mt-0.5">Rata-rata nilai kamu</p>
        </div>

        <div class="et-stat et-reveal et-delay-3">
            <div class="flex items-center justify-between">
                <div class="et-stat__icon bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                <span class="et-badge et-badge-warning">Terbaik</span>
            </div>
            <p class="et-stat__value mt-3"><span class="et-count" data-target="{{ $statistics['highest_score'] }}">0</span>%</p>
            <p class="et-stat__label mt-0.5">Skor tertinggi</p>
        </div>

        <div class="et-stat et-reveal et-delay-4">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="et-stat__label">Tingkat kelulusan</p>
                    <p class="et-stat__value mt-1"><span class="et-count" data-target="{{ $statistics['pass_rate'] }}">0</span>%</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $statistics['passed'] }}/{{ $statistics['total_quizzes_taken'] }} kuis ≥ 70</p>
                </div>
                <div class="relative shrink-0">
                    <svg viewBox="0 0 64 64" class="w-16 h-16 -rotate-90">
                        <circle cx="32" cy="32" r="26" fill="none" stroke-width="7" class="stroke-slate-200 dark:stroke-slate-700"/>
                        <circle cx="32" cy="32" r="26" fill="none" stroke-width="7" stroke-linecap="round"
                                class="stroke-lime-500 transition-all duration-1000"
                                stroke-dasharray="{{ number_format($ringCircumference, 2) }}"
                                stroke-dashoffset="{{ number_format($ringCircumference - ($ringCircumference * min(100, $statistics['pass_rate']) / 100), 2) }}"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Poin (dompet) → Toko --}}
        <a href="{{ route('employee.shop.index') }}"
           class="et-stat et-reveal et-delay-4 col-span-2 lg:col-span-1 block et-focus-ring"
           title="Buka Toko — belanja alat & makanan">
            <div class="flex items-center justify-between">
                <div class="et-stat__icon bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400 text-2xl" aria-hidden="true">🪙</div>
                <span class="et-badge et-badge-warning">🛒 Toko</span>
            </div>
            <p class="et-stat__value mt-3"><span id="et-points-value" class="et-count" data-target="{{ (int) $points }}">0</span></p>
            <p class="et-stat__label mt-0.5">Poin dompet</p>
            <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 mt-0.5">Belanja alat & makanan →</p>
        </a>
    </section>

    {{-- ================= DAILY QUESTS ================= --}}
    @php
        $questBgs = [
            'kuis_hari_ini'      => 'bg-lime-100 dark:bg-lime-500/15 border-lime-200 dark:border-lime-500/30',
            'video_hari_ini'     => 'bg-sky-100 dark:bg-sky-500/15 border-sky-200 dark:border-sky-500/30',
            'rawat_pet_hari_ini' => 'bg-amber-100 dark:bg-amber-500/15 border-amber-200 dark:border-amber-500/30',
        ];
        $questDoneCount    = collect($dailyQuests)->where('done', true)->count();
        $questClaimedCount = collect($dailyQuests)->where('claimed', true)->count();
    @endphp
    <section class="et-panel et-reveal mb-8 sm:mb-10">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="et-section-title">🗺️ Misi Harian</h2>
                <p class="et-section-sub">Selesaikan misi, klaim hadiah poin 🪙 — reset tiap hari!</p>
            </div>
            <span class="et-badge {{ $questClaimedCount === count($dailyQuests) && count($dailyQuests) > 0 ? 'et-badge-success' : 'et-badge-info' }}">
                {{ $questDoneCount }}/{{ count($dailyQuests) }} selesai
            </span>
        </div>
        <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach($dailyQuests as $qi => $q)
                <div id="et-quest-{{ $q['key'] }}"
                     class="et-quest {{ $q['claimed'] ? 'ring-2 ring-lime-400/60' : ($q['done'] ? 'ring-2 ring-amber-400/60' : '') }} et-reveal et-delay-{{ $qi + 1 }}">
                    <span class="et-quest__icon border {{ $questBgs[$q['key']] ?? 'bg-slate-100 dark:bg-slate-500/15 border-slate-200 dark:border-slate-500/30' }}">{{ $q['icon'] }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-[13.5px] font-extrabold text-slate-800 dark:text-slate-100 leading-snug">{{ $q['title'] }}</p>
                            <span class="et-badge et-badge-warning !px-2 !py-0.5">+{{ (int) $q['reward'] }} 🪙</span>
                        </div>
                        <p class="mt-1 text-[12px] font-semibold text-slate-400 dark:text-slate-500 leading-snug">{{ $q['desc'] }}</p>

                        @if($q['claimed'])
                            <button type="button" disabled
                                    class="mt-3 w-full inline-flex items-center justify-center gap-1 rounded-xl px-3.5 py-2 text-[13px] font-extrabold bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-300 cursor-not-allowed">
                                ✓ Tercapai
                            </button>
                        @elseif($q['done'])
                            <button type="button" data-et-claim="{{ $q['key'] }}"
                                    class="mt-3 w-full et-btn et-btn-primary et-btn-sm et-press et-focus-ring">
                                Klaim +{{ (int) $q['reward'] }} 🪙
                            </button>
                        @else
                            <button type="button" disabled
                                    class="mt-3 w-full et-btn et-btn-ghost et-btn-sm">
                                Selesaikan dulu 💪
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        {{-- MAIN COLUMN --}}
        <div class="lg:col-span-2 space-y-6 sm:space-y-8">

            {{-- Ongoing quizzes --}}
            @if($ongoingQuizzes->count() > 0)
                <section class="et-panel et-reveal">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="et-section-title">⏳ Sedang Berjalan</h2>
                            <p class="et-section-sub">Selesaikan sebelum waktu habis</p>
                        </div>
                        <span class="et-badge et-badge-warning">{{ $ongoingQuizzes->count() }} aktif</span>
                    </div>
                    <div class="p-4 sm:p-5 space-y-3">
                        @foreach($ongoingQuizzes as $attempt)
                            <a href="{{ route('employee.quizzes.take', $attempt->id) }}"
                               class="et-lift flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-500/10 dark:to-orange-500/10 border border-amber-200/80 dark:border-amber-500/25">
                                <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-lg shrink-0">▶</div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-slate-900 dark:text-slate-100 text-sm sm:text-base truncate">{{ $attempt->quiz->title }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Mulai {{ $attempt->started_at->diffForHumans() }}</p>
                                </div>
                                <span class="et-btn et-btn-warning et-btn-sm self-start sm:self-auto">Lanjutkan →</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Available quizzes --}}
            <section class="et-panel et-reveal">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="et-section-title">🧩 Kuis Tersedia</h2>
                        <p class="et-section-sub">Masuk dengan kode enrollment dari HRD</p>
                    </div>
                    <a href="{{ route('employee.quizzes.join') }}" class="et-btn et-btn-soft et-btn-sm">
                        🔑 Join dengan Key
                    </a>
                </div>

                <div class="p-4 sm:p-5">
                    @if($availableQuizzes->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            @foreach($availableQuizzes as $i => $quiz)
                                <article class="et-card et-card-hover et-focus-ring et-reveal et-delay-{{ min(6, $i + 1) }} p-4 flex flex-col">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="w-11 h-11 rounded-2xl bg-lime-100 text-lime-600 dark:bg-lime-500/15 dark:text-lime-400 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <span class="et-badge et-badge-purple truncate max-w-[55%]">{{ $quiz->category->name }}</span>
                                    </div>

                                    <h3 class="mt-3 font-extrabold text-slate-900 dark:text-slate-100 text-[15px] leading-snug">{{ $quiz->title }}</h3>
                                    <p class="mt-1 text-[13px] text-slate-500 dark:text-slate-400 line-clamp-2">{{ Str::limit($quiz->description, 90) ?: 'Tanpa deskripsi' }}</p>

                                    <div class="mt-3 flex flex-wrap items-center gap-2 text-[12px] text-slate-500 dark:text-slate-400">
                                        <span class="et-chip">⏱️ {{ $quiz->duration }} menit</span>
                                        <span class="et-chip">❓ {{ $quiz->questions()->count() }} soal</span>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-dashed border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                                        @if($quiz->enroll_key)
                                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300">
                                                🔑 {{ $quiz->enroll_key }}
                                            </span>
                                        @else
                                            <span></span>
                                        @endif
                                        <a href="{{ route('employee.quizzes.preview', $quiz) }}" class="et-btn et-btn-primary et-btn-sm et-press">Ikuti →</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="et-empty">
                            <div class="et-empty__icon">🔑</div>
                            <p class="et-empty__title">Belum ada kuis baru</p>
                            <p class="et-empty__text">Masukkan kode enrollment dari HRD untuk membuka kuis yang belum tersedia di daftar ini.</p>
                            <a href="{{ route('employee.quizzes.join') }}" class="et-btn et-btn-primary mt-5 et-press">Join dengan Key</a>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Weekly activity --}}
            <section class="et-panel et-reveal">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="et-section-title">📈 Aktivitas 7 Hari Terakhir</h2>
                        <p class="et-section-sub">Jumlah kuis yang kamu selesaikan tiap hari</p>
                    </div>
                    @if($streak >= 2)
                        <span class="et-badge et-badge-warning">🔥 {{ $streak }} hari beruntun</span>
                    @endif
                </div>
                <div class="p-5 sm:p-6">
                    <div class="flex items-end justify-between gap-2 sm:gap-4 h-36">
                        @foreach($weeklyActivity as $day)
                            @php $h = $day['count'] > 0 ? max(10, round(($day['count'] / $weeklyMax) * 100)) : 4; @endphp
                            <div class="flex-1 flex flex-col items-center gap-2 min-w-0">
                                <span class="text-[11px] font-bold {{ $day['count'] > 0 ? 'text-lime-600 dark:text-lime-400' : 'text-slate-400' }}">{{ $day['count'] }}</span>
                                <div class="w-full flex-1 flex items-end">
                                    <div class="w-full rounded-t-xl rounded-b-md transition-all duration-700 {{ $day['count'] > 0 ? 'bg-gradient-to-t from-lime-500 to-sky-400' : 'bg-slate-200 dark:bg-slate-800' }}"
                                         style="height: {{ $h }}%"></div>
                                </div>
                                <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-6 sm:space-y-8">

            {{-- 3D Pet --}}
            <div class="et-reveal">
                <x-pet-3d-widget :compact="true" />
            </div>

            {{-- Recent scores --}}
            <section class="et-panel et-reveal">
                <div class="px-5 py-4 border-b border-slate-200/70 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="et-section-title">🕒 Nilai Terbaru</h2>
                    <a href="{{ route('employee.quizzes.history') }}" class="text-[12.5px] font-bold text-sky-600 dark:text-sky-400 hover:underline">Semua →</a>
                </div>
                <div class="p-4 sm:p-5">
                    @if($recentScores->count() > 0)
                        <div class="space-y-2.5">
                            @foreach($recentScores as $attempt)
                                @php $scoreColor = $attempt->score >= 85 ? 'et-badge-success' : ($attempt->score >= 70 ? 'et-badge-info' : 'et-badge-danger'); @endphp
                                <a href="{{ route('employee.quizzes.result', $attempt->id) }}"
                                   class="et-lift flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-transparent hover:border-sky-300 dark:hover:border-sky-500/40 transition">
                                    <div class="min-w-0">
                                        <p class="text-[13.5px] font-bold text-slate-800 dark:text-slate-100 truncate">{{ $attempt->quiz->title }}</p>
                                        <p class="text-[11.5px] text-slate-400">{{ $attempt->completed_at?->diffForHumans() }}</p>
                                    </div>
                                    <span class="et-badge {{ $scoreColor }} shrink-0">{{ $attempt->score }}%</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="et-empty py-8">
                            <div class="et-empty__icon" style="width:62px;height:62px;">🎯</div>
                            <p class="et-empty__title text-[15px]">Belum ada nilai</p>
                            <p class="et-empty__text text-[12.5px]">Selesaikan kuis pertamamu untuk melihat perkembanganmu di sini.</p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Achievements --}}
            <section class="et-panel et-reveal">
                <div class="px-5 py-4 border-b border-slate-200/70 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="et-section-title">🎖️ Pencapaian</h2>
                    <span class="et-badge et-badge-info">{{ $earnedCount }}/{{ count($achievements) }}</span>
                </div>
                <div class="p-4 sm:p-5 grid grid-cols-3 gap-2.5">
                    @foreach($achievements as $ach)
                        <div class="et-press text-center rounded-2xl p-3 border {{ $ach['earned'] ? 'bg-gradient-to-b from-lime-50 to-sky-50 dark:from-lime-500/15 dark:to-sky-500/10 border-lime-200 dark:border-lime-500/30' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/70 dark:border-slate-800 opacity-60 grayscale' }}"
                             title="{{ $ach['desc'] }}">
                            <p class="text-2xl leading-none">{{ $ach['earned'] ? $ach['icon'] : '🔒' }}</p>
                            <p class="mt-1.5 text-[11px] font-extrabold text-slate-700 dark:text-slate-200 leading-tight">{{ $ach['title'] }}</p>
                            <p class="text-[9.5px] text-slate-400 leading-tight mt-0.5">{{ $ach['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Leaderboard --}}
            <section class="et-panel et-reveal">
                <div class="px-5 py-4 border-b border-slate-200/70 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="et-section-title">🏆 Leaderboard</h2>
                    <span class="et-badge et-badge-warning">Top 5</span>
                </div>
                <div class="p-4 sm:p-5">
                    @if(isset($leaderboard) && $leaderboard->count() > 0)
                        {{-- Podium --}}
                        <div class="flex items-end justify-center gap-2 mb-4">
                            @php
                                $podium = $leaderboard->take(3)->values();
                                $order = [1, 0, 2]; // 2nd, 1st, 3rd
                                $heights = [ 'h-16', 'h-24', 'h-14' ];
                            @endphp
                            @foreach($order as $slot)
                                @if($podium->has($slot))
                                    @php
                                        $entry = $podium[$slot];
                                        $rank = $slot + 1;
                                        $medal = [1 => '🥇', 2 => '🥈', 3 => '🥉'][$rank];
                                        $isMe = $entry->id === auth()->id();
                                    @endphp
                                    <div class="flex-1 max-w-[92px] flex flex-col items-center gap-1.5">
                                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white font-extrabold text-sm shadow-lg {{ $isMe ? 'bg-gradient-to-br from-sky-500 to-lime-500 ring-2 ring-sky-300' : 'bg-gradient-to-br from-slate-500 to-slate-400' }}">
                                            {{ strtoupper(substr($entry->full_name, 0, 1)) }}
                                        </div>
                                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate max-w-full">{{ $entry->full_name }}</p>
                                        <div class="w-full {{ $heights[$slot] }} rounded-t-2xl flex flex-col items-center justify-start pt-1.5 {{ $rank === 1 ? 'bg-gradient-to-b from-amber-300 to-amber-400' : ($rank === 2 ? 'bg-gradient-to-b from-slate-300 to-slate-400' : 'bg-gradient-to-b from-orange-200 to-orange-300') }}">
                                            <span class="text-base leading-none">{{ $medal }}</span>
                                            <span class="text-[10px] font-extrabold text-slate-700 mt-1">{{ number_format($entry->total_score ?? 0) }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="space-y-2">
                            @foreach($leaderboard as $index => $entry)
                                @php
                                    $rank = $index + 1;
                                    $isMe = $entry->id === auth()->id();
                                @endphp
                                <div class="flex items-center justify-between gap-3 p-2.5 rounded-xl {{ $isMe ? 'bg-lime-50 dark:bg-lime-500/15 border border-lime-200 dark:border-lime-500/30' : 'bg-slate-50 dark:bg-slate-800/60' }}">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[11px] font-extrabold {{ $rank <= 3 ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' : 'bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-300' }}">{{ $rank }}</span>
                                        <div class="min-w-0">
                                            <p class="text-[13px] font-bold text-slate-800 dark:text-slate-100 truncate">
                                                {{ $entry->full_name }} @if($isMe)<span class="text-lime-600 dark:text-lime-400 font-semibold">(kamu)</span>@endif
                                            </p>
                                            <p class="text-[11px] text-slate-400">{{ $entry->completed_quizzes_count ?? 0 }} kuis</p>
                                        </div>
                                    </div>
                                    <span class="et-badge et-badge-info shrink-0">{{ number_format($entry->total_score ?? 0) }} pts</span>
                                </div>
                            @endforeach
                        </div>

                        @if(isset($currentUserRank) && $currentUserRank && $currentUserRank['rank'] > 5)
                            <div class="mt-3 pt-3 border-t border-dashed border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 p-2.5 rounded-xl bg-lime-50 dark:bg-lime-500/15">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-lime-600 text-white flex items-center justify-center text-[11px] font-extrabold">{{ $currentUserRank['rank'] }}</span>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-bold text-slate-800 dark:text-slate-100 truncate">Posisi kamu</p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Terus belajar untuk naik peringkat!</p>
                                    </div>
                                </div>
                                <span class="et-badge et-badge-info shrink-0">{{ number_format($currentUserRank['total_score'] ?? 0) }} pts</span>
                            </div>
                        @endif
                    @else
                        <div class="et-empty py-8">
                            <div class="et-empty__icon" style="width:62px;height:62px;">🏆</div>
                            <p class="et-empty__title text-[15px]">Belum ada data</p>
                            <p class="et-empty__text text-[12.5px]">Selesaikan kuis untuk masuk ke papan peringkat.</p>
                        </div>
                    @endif
                </div>
            </section>
        </aside>
    </div>

    {{-- Dynamic toast (klaim misi harian) --}}
    <div id="et-dash-toasts" x-data="etToasts()" @et-toast.window="push($event.detail)"
         class="fixed z-[70] top-[78px] right-3 sm:right-5 left-3 sm:left-auto sm:w-[380px] space-y-2.5 pointer-events-none"
         role="status" aria-live="polite">
        <template x-for="(toast, i) in items" :key="toast.id">
            <div class="et-toast pointer-events-auto"
                 :class="{
                     'border-l-4 border-l-lime-500': toast.type === 'success',
                     'border-l-4 border-l-rose-500': toast.type === 'error',
                     'border-l-4 border-l-sky-500': toast.type === 'info'
                 }"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-6 scale-95"
                 x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 translate-x-6">
                <span class="et-toast__icon"
                      :class="{
                          'bg-lime-100 text-lime-600 dark:bg-lime-500/15 dark:text-lime-400': toast.type === 'success',
                          'bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400': toast.type === 'error',
                          'bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400': toast.type === 'info'
                      }">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="icon(toast.type)"></svg>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-bold text-slate-800 dark:text-slate-100 capitalize" x-text="toast.type === 'success' ? 'Berhasil' : (toast.type === 'error' ? 'Gagal' : 'Informasi')"></p>
                    <p class="text-[13px] text-slate-600 dark:text-slate-300 leading-snug mt-0.5" x-text="toast.message"></p>
                </div>
                <button type="button" @click="dismiss(toast.id)" class="et-press text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition" aria-label="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Animated counters
    document.querySelectorAll('.et-count').forEach(function (el) {
        const target = parseFloat(el.dataset.target || '0');
        const decimals = parseInt(el.dataset.decimals || '0', 10);
        const duration = 900;
        const start = performance.now();

        function tick(now) {
            const p = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - p, 3);
            el.textContent = (target * eased).toLocaleString('id-ID', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            });
            if (p < 1) requestAnimationFrame(tick);
        }

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            el.textContent = target.toLocaleString('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        } else {
            requestAnimationFrame(tick);
        }
    });
</script>

<script>
    // ==== Poin & klaim misi harian (AJAX) ====
    (function () {
        var claimUrl  = @json(route('employee.quests.claim'));
        var csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        var fmt = function (n) { return Number(n || 0).toLocaleString('id-ID'); };

        // Toast dispatcher: pakai container toast layout (flash) bila ada,
        // selain itu pakai container milik dashboard — agar tidak tumpang tindih.
        window.etToast = function (type, message) {
            var mine  = document.getElementById('et-dash-toasts');
            var nodes = document.querySelectorAll('div[role="status"]');
            var flash = null;
            for (var i = 0; i < nodes.length; i++) {
                if (nodes[i] !== mine) { flash = nodes[i]; break; }
            }
            if (mine && flash) {
                var rect = flash.getBoundingClientRect();
                mine.style.top = rect.height > 4 ? Math.round(rect.bottom + 10) + 'px' : '';
            }
            window.dispatchEvent(new CustomEvent('et-toast', { detail: { type: type, message: message } }));
        };

        function setPointsBalance(balance) {
            var value = fmt(balance);
            var nav   = document.getElementById('et-points-balance');
            var dash  = document.getElementById('et-points-value');
            if (nav)  nav.textContent  = value;
            if (dash) dash.textContent = value;
        }

        function confettiBurst(anchor) {
            var fire = function () {
                if (typeof window.confetti !== 'function') return;
                var r = anchor.getBoundingClientRect();
                window.confetti({
                    particleCount: 55,
                    spread: 60,
                    startVelocity: 30,
                    scalar: 0.85,
                    ticks: 130,
                    disableForReducedMotion: true,
                    origin: {
                        x: (r.left + r.width / 2) / window.innerWidth,
                        y: (r.top + r.height / 2) / window.innerHeight
                    },
                    colors: ['#58cc02', '#ffc800', '#1cb0f6', '#ff9600']
                });
            };
            if (typeof window.confetti === 'function') { fire(); return; }
            // canvas-confetti belum termuat (widget pet memakainya juga) — muat sekali saja.
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js';
            s.onload = fire;
            document.head.appendChild(s);
        }

        document.querySelectorAll('[data-et-claim]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.disabled) return;
                var original = btn.innerHTML;
                btn.disabled = true;
                btn.textContent = 'Memproses…';

                fetch(claimUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ quest_key: btn.getAttribute('data-et-claim') })
                })
                    .then(function (res) {
                        return res.json().catch(function () { return {}; })
                            .then(function (data) { return { ok: res.ok, data: data || {} }; });
                    })
                    .then(function (r) {
                        if (r.ok && r.data.success) {
                            // Tombol → "✓ Tercapai" (abu, mati)
                            btn.className = 'mt-3 w-full inline-flex items-center justify-center gap-1 rounded-xl px-3.5 py-2 text-[13px] font-extrabold bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-300 cursor-not-allowed';
                            btn.textContent = '✓ Tercapai';
                            btn.disabled = true;

                            var card = btn.closest('.et-quest');
                            if (card) {
                                card.classList.add('ring-2', 'ring-lime-400/60');
                                card.classList.remove('ring-amber-400/60');
                            }

                            if (typeof r.data.balance === 'number') setPointsBalance(r.data.balance);
                            confettiBurst(btn);
                            window.etToast('success', r.data.message || 'Hadiah poin berhasil diklaim! 🪙');
                        } else {
                            btn.disabled = false;
                            btn.innerHTML = original;
                            window.etToast('error', r.data.message || 'Gagal klaim hadiah, coba lagi ya.');
                        }
                    })
                    .catch(function () {
                        btn.disabled = false;
                        btn.innerHTML = original;
                        window.etToast('error', 'Gagal terhubung ke server. Coba lagi ya! 🌐');
                    });
            });
        });
    })();
</script>
@endpush
