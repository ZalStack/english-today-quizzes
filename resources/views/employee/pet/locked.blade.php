{{-- resources/views/employee/pet/locked.blade.php --}}
@extends('layouts.app')

@section('title', 'Sanctuary Terkunci — Selesaikan Misi Dulu')

@section('content')
<div class="relative bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-white overflow-x-hidden min-h-[calc(100vh-65px)]"
     x-data="sanctuaryGate()"
     x-init="init()">

    {{-- Ambient glow latar --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-32 -left-24 w-[340px] h-[340px] rounded-full bg-amber-500/10 dark:bg-amber-500/15 blur-3xl"></div>
        <div class="absolute top-[28%] -right-28 w-[320px] h-[320px] rounded-full bg-sky-500/10 dark:bg-sky-500/15 blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/3 w-[380px] h-[260px] rounded-full bg-lime-500/5 dark:bg-lime-500/10 blur-3xl"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 pt-8 sm:pt-12 pb-16">

        {{-- Banner info: datang dari link Toko yang terkunci --}}
        <div x-show="showLockedNotice"
             x-transition
             class="mb-5 flex items-start gap-3 rounded-2xl border-2 border-amber-400/50 bg-amber-50 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
            <span class="text-xl" aria-hidden="true">🔒</span>
            <p class="text-[13px] leading-relaxed font-bold text-amber-800 dark:text-amber-200">
                Toko &amp; Sanctuary Pet masih terkunci. Selesaikan dua misi pembuka di bawah ini
                untuk membuka seluruh sistem poin, alat pecah telur, dan peliharaanmu! 📚🎬
            </p>
        </div>

        {{-- ================= HERO: TELUR TERKUNCI ================= --}}
        <header class="text-center et-reveal">
            <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full border-4 border-dashed border-amber-400/70 bg-gradient-to-br from-amber-100 via-yellow-50 to-sky-100 text-6xl shadow-2xl shadow-amber-500/20 dark:from-amber-500/20 dark:via-slate-900 dark:to-sky-500/10"
                 :class="unlocked ? 'et-stageup' : 'et-egg-lock'">
                <span x-text="unlocked ? '🎉' : '🔒'" aria-hidden="true">🔒</span>
            </div>

            <span class="mt-5 inline-flex items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-3.5 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.14em] text-amber-700 dark:text-amber-300">
                🐾 Ruang Gamifikasi
            </span>

            <h1 class="mt-3 text-[27px] leading-[1.12] sm:text-4xl lg:text-[42px] font-extrabold tracking-tight bg-gradient-to-r from-amber-500 via-yellow-500 to-cyan-600 dark:from-amber-300 dark:via-yellow-200 dark:to-cyan-300 bg-clip-text text-transparent">
                Sanctuary-mu Terkunci
            </h1>

            <p class="mx-auto mt-3 max-w-2xl text-[13.5px] leading-relaxed text-slate-600 dark:text-slate-400 sm:text-[15px]">
                Di English Today, semua hadiah harus kamu <b class="text-slate-800 dark:text-white">raih dengan usaha</b>.
                Selesaikan <b class="text-sky-600 dark:text-sky-300">1 kuis</b> dan kumpulkan
                <b class="text-pink-600 dark:text-pink-300">1 video challenge</b> untuk membuka
                Sanctuary Pet, Toko alat, dan seluruh ekonomi poinmu! 💪
            </p>

            {{-- Status live --}}
            <p class="mt-3 inline-flex items-center gap-2 text-[12px] font-bold text-slate-500 dark:text-slate-400">
                <span class="w-2 h-2 rounded-full bg-sky-400 et-pulse-dot" aria-hidden="true"></span>
                <span x-text="unlocked ? 'Semua misi selesai!' : (checking ? 'Memeriksa misi...' : 'Cek otomatis setiap beberapa detik')">Memeriksa misi...</span>
            </p>
        </header>

        {{-- ================= CHECKLIST MISI ================= --}}
        <section class="mt-8 grid grid-cols-1 gap-4 sm:mt-10 sm:grid-cols-2" aria-label="Syarat membuka sanctuary">
            {{-- Misi 1: Kuis --}}
            <article class="et-reveal et-delay-1 relative overflow-hidden rounded-3xl border-2 p-5 transition-all duration-300 sm:p-6"
                     :class="doneQuiz
                         ? 'border-emerald-400 bg-gradient-to-br from-emerald-50 to-lime-50 dark:border-emerald-400/50 dark:from-emerald-500/10 dark:to-slate-900'
                         : 'border-slate-200 bg-white hover:border-sky-400/60 dark:border-slate-800 dark:bg-slate-900/70'">
                <div class="flex items-start justify-between gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-2xl shadow-lg"
                          :class="doneQuiz ? 'bg-emerald-100 dark:bg-emerald-500/20' : 'bg-sky-100 dark:bg-sky-500/20'">📚</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-wider transition-all"
                          :class="doneQuiz
                              ? 'bg-emerald-500 text-white et-pop'
                              : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400'"
                          x-text="doneQuiz ? '✅ Selesai' : '⏳ Belum'">⏳ Belum</span>
                </div>

                <h2 class="mt-4 text-lg font-extrabold text-slate-800 dark:text-white">Kerjakan 1 Kuis</h2>
                <p class="mt-1.5 text-[13px] leading-relaxed text-slate-600 dark:text-slate-400">
                    Pilih kuis aktif, kerjakan sampai selesai, dan raih poin pertamamu.
                    Skor minimal berapa pun tetap dihitung — yang penting selesai! 🎯
                </p>

                <a href="{{ route('employee.quizzes.join') }}"
                   class="et-press mt-4 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-sky-500 to-blue-600 px-4 py-3 text-[13px] font-extrabold text-white shadow-lg shadow-sky-600/30 transition hover:from-sky-400 hover:to-blue-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300 sm:w-auto">
                    <span aria-hidden="true">🚀</span>
                    <span x-text="doneQuiz ? 'Kuis Lagi (Opsional)' : 'Mulai Kuis'">Mulai Kuis</span>
                </a>
            </article>

            {{-- Misi 2: Video Challenge --}}
            <article class="et-reveal et-delay-2 relative overflow-hidden rounded-3xl border-2 p-5 transition-all duration-300 sm:p-6"
                     :class="doneVideo
                         ? 'border-emerald-400 bg-gradient-to-br from-emerald-50 to-lime-50 dark:border-emerald-400/50 dark:from-emerald-500/10 dark:to-slate-900'
                         : 'border-slate-200 bg-white hover:border-pink-400/60 dark:border-slate-800 dark:bg-slate-900/70'">
                <div class="flex items-start justify-between gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-2xl shadow-lg"
                          :class="doneVideo ? 'bg-emerald-100 dark:bg-emerald-500/20' : 'bg-pink-100 dark:bg-pink-500/20'">🎬</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-wider transition-all"
                          :class="doneVideo
                              ? 'bg-emerald-500 text-white et-pop'
                              : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400'"
                          x-text="doneVideo ? '✅ Selesai' : '⏳ Belum'">⏳ Belum</span>
                </div>

                <h2 class="mt-4 text-lg font-extrabold text-slate-800 dark:text-white">Kumpulkan 1 Video Challenge</h2>
                <p class="mt-1.5 text-[13px] leading-relaxed text-slate-600 dark:text-slate-400">
                    Rekam video speaking-mu sesuai challenge aktif, lalu tempel link-nya.
                    Sekali submit, misi terbuka seumur hidup. 🎬
                </p>

                <a href="{{ route('employee.video-challenges.index') }}"
                   class="et-press mt-4 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-pink-500 to-rose-500 px-4 py-3 text-[13px] font-extrabold text-white shadow-lg shadow-pink-600/30 transition hover:from-pink-400 hover:to-rose-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-pink-300 sm:w-auto">
                    <span aria-hidden="true">🎥</span>
                    <span x-text="doneVideo ? 'Video Lagi (Opsional)' : 'Buka Video Challenge'">Buka Video Challenge</span>
                </a>
            </article>
        </section>

        {{-- ================= PETA JALAN SIKLUS ================= --}}
        <section class="mt-8 rounded-3xl border-2 border-slate-200 bg-white p-4 shadow-xl shadow-slate-200/60 dark:border-slate-800 dark:bg-slate-900/70 dark:shadow-black/30 sm:p-6 et-reveal et-delay-3"
                 aria-label="Peta jalan siklus peliharaan">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-[15px] font-extrabold text-slate-800 dark:text-white sm:text-base">🗺️ Setelah Terbuka, Ini Siklusnya</h2>
                <span class="rounded-full border border-lime-500/30 bg-lime-500/10 px-3 py-1 text-[11px] font-extrabold text-lime-700 dark:text-lime-300">Case Real</span>
            </div>

            <ol class="mt-4 flex gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible" style="scrollbar-width: thin;">
                @foreach ([
                    ['icon' => '📚', 'label' => 'Kerjakan Kuis'],
                    ['icon' => '🎬', 'label' => 'Upload Video'],
                    ['icon' => '🪙', 'label' => 'Kumpulkan Poin'],
                    ['icon' => '🛒', 'label' => 'Beli Alat'],
                    ['icon' => '🔨', 'label' => 'Pecahkan Telur'],
                    ['icon' => '🐣', 'label' => 'Telur Menetas'],
                    ['icon' => '🍼', 'label' => 'Rawat Bayi'],
                    ['icon' => '🧑', 'label' => 'Jadi Dewasa'],
                    ['icon' => '🥚', 'label' => 'Bertelur (Gen Naik!)'],
                ] as $i => $step)
                    <li class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                        <span class="inline-flex items-center gap-2 rounded-2xl border-2 border-slate-200 bg-white px-2.5 py-2 text-[12.5px] font-extrabold text-slate-600 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-400">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-black/5 text-sm dark:bg-white/10">{{ $step['icon'] }}</span>
                            <span class="whitespace-nowrap">{{ $step['label'] }}</span>
                        </span>
                        @unless($loop->last)
                            <span class="hidden text-xs font-black text-slate-400 dark:text-slate-500 sm:inline" aria-hidden="true">➜</span>
                        @endunless
                    </li>
                @endforeach
            </ol>

            <p class="mt-4 text-[12.5px] leading-relaxed text-slate-600 dark:text-slate-400">
                Telur generasi lanjutan berubah tier — <b class="text-amber-600 dark:text-amber-300">🥉 Perunggu → 🥈 Perak → 🥇 Emas → 💎 Kristal</b> —
                dengan peluang mendapatkan hewan <b>langka yang lebih besar</b>. Rawat, kawinkan, ulangi! ✨
            </p>
        </section>

        {{-- ================= NOTIFIKASI UNLOCK ================= --}}
        <div x-show="unlocked"
             x-transition.opacity
             class="pointer-events-none fixed inset-0 z-[85] flex items-center justify-center p-5 bg-slate-950/70 backdrop-blur-sm"
             role="alert" aria-live="assertive">
            <div class="et-stageup w-full max-w-sm rounded-[28px] border-4 border-lime-400 bg-gradient-to-br from-lime-200 via-yellow-100 to-sky-200 px-6 py-7 text-center shadow-2xl dark:border-lime-300 dark:from-lime-300 dark:via-yellow-200 dark:to-sky-300">
                <p class="text-[13px] font-black uppercase tracking-[0.24em] text-lime-700">🎉 SANCTUARY TERBUKA!</p>
                <p class="mt-3 text-5xl">🔓🥚</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">Kerja bagus!</p>
                <p class="mt-1 text-[13px] font-bold text-slate-700">Mengalihkanmu ke Sanctuary Pet...</p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
    function sanctuaryGate() {
        return {
            doneQuiz: {{ $done_quiz ? 'true' : 'false' }},
            doneVideo: {{ $done_video ? 'true' : 'false' }},
            unlocked: {{ $unlocked ? 'true' : 'false' }},
            checking: false,
            showLockedNotice: {{ session('sanctuary_locked') ? 'true' : 'false' }},
            _timer: null,

            init() {
                if (this.unlocked) {
                    this.celebrate();
                    return;
                }

                this.check();
                this._timer = setInterval(() => this.check(), 6000);
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden && !this.unlocked) this.check();
                });
                window.addEventListener('focus', () => {
                    if (!this.unlocked) this.check();
                });
            },

            async check() {
                if (this.unlocked || this.checking) return;
                this.checking = true;
                try {
                    const res = await fetch('{{ route("employee.pet.unlock-status") }}', {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await res.json();
                    this.doneQuiz = !!data.done_quiz;
                    this.doneVideo = !!data.done_video;

                    if (data.unlocked) {
                        this.unlocked = true;
                        if (this._timer) clearInterval(this._timer);
                        this.celebrate();
                    }
                } catch (e) {
                    console.warn('Unlock status error:', e);
                } finally {
                    this.checking = false;
                }
            },

            celebrate() {
                if (typeof confetti === 'function') {
                    confetti({ particleCount: 160, spread: 100, origin: { y: 0.55 }, colors: ['#A3E635', '#FBBF24', '#38BDF8', '#FF4081', '#FFFFFF'] });
                    setTimeout(() => confetti({ particleCount: 90, spread: 120, origin: { y: 0.35 }, colors: ['#58CC02', '#FFC800', '#1CB0F6'] }), 350);
                }
                setTimeout(() => { window.location = '{{ route("employee.pet.index") }}'; }, 1900);
            },
        };
    }
</script>
@endpush
@endsection
