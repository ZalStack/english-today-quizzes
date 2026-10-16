{{-- resources/views/employee/pet/index.blade.php --}}
@extends('layouts.app')

@section('title', '3D Virtual Pet Sanctuary')

@section('content')
@php
    $allPets = \App\Models\Pet::all();
    $logs = \App\Models\PetLog::where('user_id', auth()->id())->latest()->take(5)->get();

    // Meta tier telur generasi saat ini (Perunggu → Perak → Emas → Kristal)
    $eggTierMeta = \App\Services\PetService::tierMeta($userPet->egg_tier);
    $tiersJson = collect(\App\Services\PetService::TIER_META)
        ->map(fn (array $meta, string $key) => [
            'key' => $key,
            'label' => $meta['label'],
            'icon' => $meta['icon'],
            'color' => $meta['color'],
            'min_generation' => $meta['min_generation'],
        ])
        ->values();

    // Inventaris alat & makanan (siap dipakai Alpine)
    $toolsJson = collect($tools)->map(fn ($t) => [
        'id' => (int) $t->shop_item_id,
        'name' => $t->shopItem?->name ?? 'Alat Pecah Telur',
        'icon' => $t->shopItem?->icon ?? '🔨',
        'power' => (int) ($t->shopItem?->power ?? 0),
        'quantity' => (int) $t->quantity,
    ])->values();

    $foodsJson = collect($foods)->map(fn ($f) => [
        'id' => (int) $f->shop_item_id,
        'name' => $f->shopItem?->name ?? 'Makanan',
        'icon' => $f->shopItem?->icon ?? '🍖',
        'quantity' => (int) $f->quantity,
    ])->values();

    // Level menuju tahap berikutnya (saat pet sudah menetas)
    $nextStageLevel = null;
    if ($userPet->isHatched() && $userPet->life_stage !== 'adult') {
        $nextStageLevel = \App\Services\PetService::STAGE_LEVELS[$userPet->life_stage] ?? null;
    }
@endphp

{{-- Halaman tematik "sanctuary" — mendukung mode terang & gelap --}}
<div class="relative bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-white overflow-x-hidden min-h-[calc(100vh-65px)]"
     x-data="petSanctuaryPage()"
     x-init="initPage()">

    {{-- Ambient glow latar sanctuary --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-32 -left-24 w-[340px] h-[340px] rounded-full bg-sky-500/10 dark:bg-sky-500/20 blur-3xl"></div>
        <div class="absolute top-[10%] left-[28%] w-[260px] h-[260px] rounded-full bg-lime-500/10 dark:bg-lime-500/15 blur-3xl"></div>
        <div class="absolute top-[32%] -right-28 w-[320px] h-[320px] rounded-full bg-cyan-500/5 dark:bg-cyan-500/10 blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/3 w-[380px] h-[260px] rounded-full bg-amber-500/5 dark:bg-amber-500/10 blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 lg:pt-10 pb-14 sm:pb-16">

        {{-- ================= HEADER ================= --}}
        <header class="et-reveal">
            {{-- Breadcrumb --}}
            <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[12px] font-semibold" aria-label="Breadcrumb">
                <a href="{{ route('employee.dashboard') }}"
                   class="et-press inline-flex items-center gap-1.5 rounded-md text-sky-600 dark:text-sky-300 transition hover:text-sky-500 dark:hover:text-sky-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Dashboard
                </a>
                <span class="text-slate-500" aria-hidden="true">/</span>
                <span class="text-slate-600 dark:text-slate-300">Pet Sanctuary 3D</span>
            </nav>

            <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <div class="flex items-center gap-3">
                        <span class="et-mascot et-mascot--bounce w-12 h-12 sm:w-14 sm:h-14 shrink-0 text-2xl sm:text-3xl bg-white dark:bg-slate-800 border-sky-400 dark:border-sky-500"
                              aria-hidden="true">🐾</span>
                        <span class="inline-flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-[0.14em] text-amber-600 dark:text-amber-300/90">
                            🐾 Ruang Gamifikasi
                        </span>
                    </div>
                    <h1 class="mt-1.5 text-[27px] leading-[1.12] sm:text-4xl lg:text-[42px] font-extrabold tracking-tight break-words bg-gradient-to-r from-amber-500 via-yellow-500 to-cyan-600 dark:from-amber-300 dark:via-yellow-200 dark:to-cyan-300 bg-clip-text text-transparent">
                        3D Virtual Pet Sanctuary
                    </h1>
                    <p class="mt-2.5 max-w-2xl text-[13.5px] leading-relaxed text-slate-600 dark:text-slate-400 sm:text-[15px]">
                        Kumpulkan poin dari kuis & video challenge, beli alat di 🛒 Toko, pecahkan telur, rawat bayimu hingga dewasa — lalu biakkan generasi berikutnya! 🥚✨
                    </p>
                </div>

                {{-- Badge status & kontrol audio --}}
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <span class="inline-flex items-center gap-2 rounded-full et-glass px-3.5 py-2 text-[11.5px] font-bold text-emerald-700 dark:text-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 et-pulse-dot"></span>
                        WebGL 3D Active
                    </span>
                    <button type="button"
                            @click="toggleSound()"
                            :aria-pressed="soundOn"
                            aria-label="Nyalakan atau matikan efek suara"
                            class="et-press inline-flex items-center gap-1.5 rounded-full et-glass px-3.5 py-2 text-[11.5px] font-bold text-slate-700 dark:text-white transition hover:bg-white/25 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 dark:focus-visible:ring-white/60">
                        <span x-text="soundOn ? '🔊' : '🔇'">🔊</span>
                        <span x-text="soundOn ? 'Audio ON' : 'Audio OFF'">Audio ON</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- ================= TIMELINE TAHAP ================= --}}
        <section class="mt-6 rounded-3xl border-2 border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/70 p-3.5 shadow-xl shadow-slate-200/60 dark:shadow-black/30 sm:p-4 et-reveal et-delay-1"
                 aria-label="Perjalanan tahap peliharaan">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <span class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-lime-600 dark:text-lime-300/90">
                    🗺️ Perjalanan Siklus Pet
                </span>
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-[11.5px] font-extrabold text-amber-700 dark:text-amber-300">
                    ✨ Generasi <span x-text="petState.generation">{{ $userPet->generation }}</span>
                </span>
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border px-3 py-1 text-[11.5px] font-extrabold text-slate-700 transition-colors dark:text-slate-200"
                      :style="`border-color: ${petState.egg_tier_color}66; background: ${petState.egg_tier_color}1F;`">
                    <span x-text="petState.egg_tier_icon">🥉</span>
                    <span class="hidden sm:inline">Telur</span>
                    <span x-text="petState.egg_tier_label">Perunggu</span>
                </span>
            </div>

            <ol class="flex gap-2 overflow-x-auto pb-1 sm:gap-1.5" style="scrollbar-width: thin;">
                <template x-for="(step, i) in timelineSteps" :key="step.label">
                    <li class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                        <span class="inline-flex items-center gap-2 rounded-2xl border-2 px-2.5 py-2 transition-all duration-300"
                              :class="stepClass(i)">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-xl text-sm"
                                  :class="stepState(i) === 'todo' ? 'bg-black/5 dark:bg-white/10' : 'bg-white/70 dark:bg-black/25'"
                                  x-text="stepState(i) === 'done' ? '✅' : step.icon">🥚</span>
                            <span class="whitespace-nowrap text-[12.5px] font-extrabold" x-text="step.label"></span>
                        </span>
                        <span x-show="i < timelineSteps.length - 1"
                              class="hidden text-xs font-black text-slate-400 dark:text-slate-500 sm:inline"
                              aria-hidden="true">➜</span>
                    </li>
                </template>
            </ol>
        </section>

        {{-- ================= PANGGUNG 3D + PANEL STATS ================= --}}
        <div class="mt-5 grid grid-cols-1 gap-5 sm:mt-6 sm:gap-6 lg:grid-cols-12">

            {{-- ===== KIRI: Panggung Interaktif 3D (8 kolom) ===== --}}
            <section class="min-w-0 lg:col-span-8 et-reveal et-delay-1">
                <div class="relative overflow-hidden rounded-3xl border-2 border-sky-500/30 bg-gradient-to-b from-white via-white to-sky-50 dark:from-sky-950/70 dark:via-slate-900/90 dark:to-slate-950 shadow-2xl shadow-slate-200/60 dark:shadow-black/50">

                    {{-- Glow ambient --}}
                    <div class="pointer-events-none absolute -top-28 -left-24 h-72 w-72 rounded-full bg-cyan-500/10 dark:bg-cyan-500/15 blur-3xl" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute -bottom-28 -right-24 h-72 w-72 rounded-full bg-amber-500/10 dark:bg-amber-500/15 blur-3xl" aria-hidden="true"></div>

                    <div class="relative z-10 p-4 sm:p-6 lg:p-7">

                        {{-- Header panggung --}}
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-tr from-amber-400 to-yellow-300 text-2xl shadow-lg shadow-amber-500/30">
                                    <span x-text="petState.status === 'hatched' ? '🐾' : '🥚'">🥚</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="min-w-0 text-lg font-extrabold tracking-tight text-slate-800 dark:text-white break-words sm:text-xl"
                                            x-text="petState.status === 'hatched' ? (petState.nickname || 'Virtual Pet') : 'Telur Pengetahuan 3D'">
                                            Telur Pengetahuan 3D
                                        </h2>
                                        <template x-if="petState.status === 'hatched' && petState.pet">
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-extrabold uppercase leading-none tracking-wider"
                                                  :class="{
                                                      'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-500/15 dark:text-emerald-300 dark:border-emerald-400/40': petState.pet.rarity === 'common',
                                                      'bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-500/15 dark:text-blue-300 dark:border-blue-400/40': petState.pet.rarity === 'rare',
                                                      'bg-purple-100 text-purple-700 border-purple-300 dark:bg-purple-500/15 dark:text-purple-300 dark:border-purple-400/40': petState.pet.rarity === 'epic',
                                                      'bg-amber-100 text-amber-700 border-amber-300 dark:bg-amber-500/15 dark:text-amber-300 dark:border-amber-400/40 animate-pulse shadow-lg shadow-amber-500/20': petState.pet.rarity === 'legendary'
                                                  }"
                                                  x-text="petState.pet.rarity">
                                            </span>
                                        </template>
                                    </div>
                                    <p class="mt-1 text-xs leading-snug text-sky-700 dark:text-sky-200/70"
                                       x-text="petState.status === 'hatched' ? (petState.pet?.species + ' • Level ' + petState.level) : 'Pukul telur dengan alat sampai 100% retak!'">
                                        Pukul telur dengan alat sampai 100% retak!
                                    </p>
                                </div>
                            </div>

                            {{-- Badge siap menetas --}}
                            <template x-if="eggReady">
                                <span class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-full bg-gradient-to-r from-amber-400 to-yellow-300 px-3.5 py-1.5 text-[11px] font-extrabold tracking-wide text-slate-950 shadow-lg shadow-amber-500/40 animate-bounce sm:self-auto">
                                    ⚡ SIAP MENETAS!
                                </span>
                            </template>
                        </div>

                        {{-- Badge tahap hidup + generasi (setelah menetas) --}}
                        <template x-if="petState.status === 'hatched'">
                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-2 rounded-2xl border-2 border-sky-500/40 bg-sky-500/10 px-3.5 py-2 text-[13.5px] font-extrabold text-sky-700 dark:text-sky-200 dark:border-sky-400/40">
                                    <span class="text-lg" x-text="petState.life_stage_icon">👶</span>
                                    <span x-text="petState.life_stage_label">Bayi</span>
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1.5 text-[11.5px] font-extrabold text-amber-700 dark:text-amber-300">
                                    ✨ Gen <span x-text="petState.generation">1</span>
                                </span>
                                <span x-show="petState.is_adult"
                                      class="et-wiggle inline-flex items-center gap-1.5 rounded-full border border-lime-500/40 bg-lime-500/10 px-3 py-1.5 text-[11.5px] font-extrabold text-lime-700 dark:text-lime-300">
                                    🥚 Siap bertelur!
                                </span>
                            </div>
                        </template>

                        {{-- Panggung WebGL 3D --}}
                        <div class="relative mt-4 flex h-72 w-full cursor-grab items-center justify-center overflow-hidden rounded-2xl border-2 border-sky-500/30 bg-[radial-gradient(circle_at_50%_35%,rgba(14,165,233,0.30),rgba(2,6,23,0.96))] shadow-inner active:cursor-grabbing sm:mt-5 sm:h-96">
                            <div id="sanctuary-3d-canvas" class="h-full w-full"></div>

                            {{-- Layer efek DOM (hati saat bertelur) --}}
                            <div id="stage-fx" class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true"></div>

                            {{-- Petunjuk interaksi --}}
                            <div class="pointer-events-none absolute inset-x-2 bottom-2 flex justify-center sm:inset-x-3 sm:bottom-3 sm:justify-start">
                                <span class="rounded-xl border border-white/10 bg-black/60 px-3 py-1.5 text-center text-[10.5px] leading-snug text-sky-200/90 backdrop-blur-md sm:text-[11px]">
                                    👆 Geser untuk putar 360° • Klik objek untuk interaksi
                                </span>
                            </div>
                        </div>

                        {{-- ============ TOOLBAR AKSI ============ --}}
                        <div class="relative z-10 mt-4 sm:mt-5">

                            {{-- ===== Fase telur: pilih alat, pukul, ketuk ===== --}}
                            <template x-if="petState.status !== 'hatched'">
                                <div class="flex flex-col gap-3">

                                    {{-- Pemilih alat --}}
                                    <div class="rounded-2xl border border-dashed border-amber-400/50 bg-amber-500/5 p-3 dark:bg-amber-500/10">
                                        <div class="flex items-center justify-between gap-2 text-[11.5px] font-extrabold text-amber-700 dark:text-amber-300">
                                            <span>🧰 Pilih Alat Pecah Telur</span>
                                            <span x-show="tools.length">Sisa stok: <b x-text="totalToolStock()">0</b></span>
                                        </div>

                                        <template x-if="tools.length">
                                            <div class="mt-2 flex gap-2 overflow-x-auto pb-1 -mx-1 px-1 sm:flex-wrap sm:overflow-visible">
                                                <template x-for="tool in tools" :key="tool.id">
                                                    <button type="button"
                                                            @click="selectedToolId = tool.id"
                                                            :aria-pressed="selectedToolId === tool.id"
                                                            class="et-press flex shrink-0 items-center gap-2 rounded-2xl border-2 px-3 py-2 text-left transition-all sm:flex-none"
                                                            :class="selectedToolId === tool.id
                                                                ? 'border-sky-500 bg-sky-100 shadow-md shadow-sky-500/20 dark:bg-sky-500/20'
                                                                : 'border-slate-200 bg-white hover:border-sky-300 dark:border-white/10 dark:bg-white/5 dark:hover:border-sky-400/40'">
                                                        <span class="text-xl" x-text="tool.icon">🔨</span>
                                                        <span class="flex flex-col leading-tight">
                                                            <span class="text-[12.5px] font-extrabold text-slate-800 dark:text-white" x-text="tool.name">Palu Kayu</span>
                                                            <span class="text-[10.5px] font-bold"
                                                                  :class="selectedToolId === tool.id ? 'text-sky-700 dark:text-sky-200' : 'text-slate-500 dark:text-slate-400'"
                                                                  x-text="'+' + tool.power + ' retak · stok ' + tool.quantity"></span>
                                                        </span>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- Empty state: belum ada alat --}}
                                        <template x-if="!tools.length">
                                            <div class="mt-2 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white/80 px-3 py-2.5 dark:border-white/10 dark:bg-black/30">
                                                <span class="text-[12.5px] font-bold text-slate-600 dark:text-slate-300">
                                                    🧰 Belum ada alat — beli di 🛒 Toko dulu ya!
                                                </span>
                                                <a href="{{ route('employee.shop.index') }}"
                                                   class="et-press inline-flex items-center gap-1 rounded-lg bg-sky-500 px-3 py-1.5 text-[11.5px] font-extrabold text-white shadow-md shadow-sky-600/30 transition hover:bg-sky-400">
                                                    🛒 ke Toko
                                                </a>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Tombol pukul / pecahkan + ketuk tangan --}}
                                    <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-3 sm:gap-3">
                                        <template x-if="eggReady">
                                            <button type="button"
                                                    @click="triggerHatch()"
                                                    :disabled="isProcessing"
                                                    class="et-press et-wiggle flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-500 px-4 py-4 text-sm font-black tracking-wide text-slate-950 shadow-xl shadow-amber-500/30 transition-all hover:from-amber-300 hover:to-yellow-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-300 disabled:cursor-not-allowed disabled:opacity-60 sm:col-span-2">
                                                <span class="text-xl">🎉</span>
                                                <span x-text="isProcessing ? 'MEMECAHKAN TELUR...' : 'PECAHKAN TELUR!'">PECAHKAN TELUR!</span>
                                            </button>
                                        </template>

                                        <template x-if="!eggReady">
                                            <button type="button"
                                                    @click="strikeEgg()"
                                                    :disabled="isProcessing || !selectedToolId"
                                                    class="et-press et-wiggle flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-lime-400 via-lime-500 to-emerald-500 px-4 py-4 text-sm font-black tracking-wide text-slate-950 shadow-xl shadow-lime-600/30 transition-all hover:from-lime-300 hover:to-emerald-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lime-300 disabled:cursor-not-allowed disabled:opacity-60 sm:col-span-2">
                                                <span class="text-xl">🔨</span>
                                                <span x-text="isProcessing ? 'MENGHANTAM...' : 'PUKUL TELUR!'">PUKUL TELUR!</span>
                                            </button>
                                        </template>

                                        <template x-if="!eggReady">
                                            <button type="button"
                                                    @click="tapEgg()"
                                                    :disabled="isProcessing"
                                                    class="et-press flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-slate-200 bg-white px-4 py-3.5 text-[13px] font-extrabold text-slate-700 transition-all hover:border-sky-400/40 hover:bg-sky-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/60 disabled:cursor-not-allowed disabled:opacity-60 dark:border-white/15 dark:bg-white/10 dark:text-white dark:hover:bg-white/15">
                                                <span>👋</span>
                                                <span>Ketuk Tangan <span class="opacity-80">(+1)</span></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            {{-- ===== Fase pet: beri makan, elus, latih, bertelur ===== --}}
                            <template x-if="petState.status === 'hatched'">
                                <div class="flex flex-col gap-3">
                                    <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-4 sm:gap-3">
                                        <button type="button"
                                                @click="feedPet()"
                                                :disabled="isProcessing"
                                                class="et-press et-wiggle flex min-h-[74px] flex-col items-center justify-center gap-1.5 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-500 px-2 py-3 text-[13px] font-extrabold text-white shadow-lg shadow-emerald-900/40 transition-all hover:from-emerald-500 hover:to-teal-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 disabled:cursor-not-allowed disabled:opacity-60 sm:flex-row sm:text-sm">
                                            <span class="text-lg sm:text-base">🍼</span>
                                            <span>Beri Makan</span>
                                        </button>

                                        <button type="button"
                                                @click="petCreature()"
                                                :disabled="isProcessing"
                                                class="et-press flex min-h-[74px] flex-col items-center justify-center gap-1.5 rounded-2xl bg-gradient-to-br from-pink-600 to-rose-500 px-2 py-3 text-[13px] font-extrabold text-white shadow-lg shadow-pink-900/40 transition-all hover:from-pink-500 hover:to-rose-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-pink-300 disabled:cursor-not-allowed disabled:opacity-60 sm:flex-row sm:text-sm">
                                            <span class="text-lg sm:text-base">💕</span>
                                            <span>Elus</span>
                                        </button>

                                        <a href="{{ route('employee.quizzes.join') }}"
                                           class="et-press flex min-h-[74px] flex-col items-center justify-center gap-1.5 rounded-2xl bg-gradient-to-br from-sky-600 to-blue-600 px-2 py-3 text-center text-[13px] font-extrabold text-white shadow-lg shadow-blue-900/40 transition-all hover:from-sky-500 hover:to-blue-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300 sm:flex-row sm:text-sm">
                                            <span class="text-lg sm:text-base">📚</span>
                                            <span>Latih (Kuis)</span>
                                        </a>

                                        {{-- Dewasa: bertelur ke generasi berikutnya --}}
                                        <template x-if="petState.is_adult">
                                            <button type="button"
                                                    @click="breedPet()"
                                                    :disabled="isProcessing"
                                                    class="et-press et-wiggle et-glow-pulse flex min-h-[74px] flex-col items-center justify-center gap-1.5 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 px-2 py-3 text-[13px] font-black text-slate-950 shadow-lg shadow-amber-500/40 transition-all hover:from-amber-300 hover:to-orange-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-200 disabled:cursor-not-allowed disabled:opacity-60 sm:flex-row sm:text-sm">
                                                <span class="text-lg sm:text-base">🥚</span>
                                                <span>Bertelur!</span>
                                            </button>
                                        </template>

                                        <template x-if="!petState.is_adult">
                                            <button type="button"
                                                    @click="resetEgg()"
                                                    class="et-press flex min-h-[74px] flex-col items-center justify-center gap-1.5 rounded-2xl border-2 border-slate-200 bg-white text-slate-500 px-2 py-3 text-[13px] font-extrabold transition-all hover:border-rose-400/40 hover:bg-rose-50 hover:text-rose-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-400/60 sm:flex-row sm:text-sm dark:border-white/15 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-rose-500/10 dark:hover:text-rose-200">
                                                <span class="text-lg sm:text-base">🔄</span>
                                                <span>Telur Baru</span>
                                            </button>
                                        </template>
                                    </div>

                                    <p x-show="!foods.length"
                                       class="text-[12px] font-bold text-rose-600 dark:text-rose-300">
                                        🍽️ Stok makanan habis —
                                        <a href="{{ route('employee.shop.index') }}" class="underline decoration-2 underline-offset-2 hover:text-rose-500">beli di 🛒 Toko</a>
                                        agar peliharaanmu terus tumbuh!
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ===== KANAN: Status, Atribut & Riwayat (4 kolom) ===== --}}
            <aside class="space-y-5 sm:space-y-6 lg:col-span-4">

                {{-- Panel Status & Atribut --}}
                <section class="rounded-3xl border-2 border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/70 p-4 shadow-xl shadow-slate-200/60 dark:shadow-black/30 backdrop-blur-sm sm:p-5 et-reveal et-delay-2">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="flex items-center gap-2 text-[15px] font-extrabold text-slate-800 dark:text-white sm:text-base">
                            <span>📊</span> Status &amp; Atribut Pet
                        </h3>
                        <span class="shrink-0 rounded-full border border-cyan-500/30 bg-cyan-500/10 px-2.5 py-1 text-[11px] font-bold text-cyan-700 dark:text-cyan-300"
                              x-text="petState.status === 'hatched' ? ('Lv. ' + petState.level) : (eggReady ? 'Siap Netas' : 'Telur')">Telur</span>
                    </div>

                    <div class="mt-4 space-y-4">
                        {{-- Meteran keretakan telur --}}
                        <template x-if="petState.status !== 'hatched'">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2 text-xs font-semibold">
                                    <span class="text-slate-600 dark:text-slate-400">🔨 Keretakan Telur</span>
                                    <span class="font-mono font-bold"
                                          :class="petState.crack_percentage >= 75 ? 'text-amber-500 dark:text-amber-300 animate-pulse' : 'text-amber-600 dark:text-amber-300'"
                                          x-text="petState.crack_percentage + '%'">0%</span>
                                </div>
                                <div class="et-progress"
                                     role="progressbar" aria-label="Keretakan telur"
                                     aria-valuemin="0" aria-valuemax="100"
                                     :aria-valuenow="petState.crack_percentage">
                                    <div class="et-progress__bar"
                                         :style="`width: ${Math.min(100, petState.crack_percentage)}%; background-image: linear-gradient(90deg, ${petState.crack_percentage >= 75 ? '#f59e0b, #fbbf24, #fde68a' : '#f59e0b, #fbbf24, #fef3c7'});`"></div>
                                </div>
                                <p class="text-[11px] leading-relaxed text-slate-600 dark:text-slate-400">
                                    Pukul telur pakai alat 🧰 dari 🛒 Toko — kumpulkan poinnya dari kuis & video challenge!
                                    <b x-show="petState.crack_percentage >= 75" class="text-amber-600 dark:text-amber-300"> Hampir pecah, lanjutkan! 🔥</b>
                                </p>
                            </div>
                        </template>

                        {{-- Level, EXP & atribut (setelah menetas) --}}
                        <template x-if="petState.status === 'hatched' && petState.pet">
                            <div class="space-y-4">
                                {{-- Bar EXP Tahap --}}
                                <div>
                                    <div class="mb-1.5 flex items-center justify-between gap-2 text-xs font-semibold">
                                        <span class="text-slate-700 dark:text-slate-300">EXP Tahap • Lv. <span x-text="petState.level">1</span></span>
                                        <span class="font-mono text-[11px] text-cyan-700 dark:text-cyan-300"
                                              x-text="petState.experience + ' / ' + petState.maxExperience + ' XP'">0 / 100 XP</span>
                                    </div>
                                    <div class="et-progress"
                                         role="progressbar" aria-label="Progress EXP tahap"
                                         aria-valuemin="0" aria-valuemax="100"
                                         :aria-valuenow="petState.expPercentage">
                                        <div class="et-progress__bar"
                                             :style="`width: ${Math.min(100, petState.expPercentage)}%`"></div>
                                    </div>
                                    <p x-show="petState.next_stage_level"
                                       class="mt-1.5 text-[11.5px] font-bold text-lime-700 dark:text-lime-300">
                                        🎯 Naik ke tahap berikut di Level <span x-text="petState.next_stage_level">3</span>
                                    </p>
                                </div>

                                {{-- Meter atribut --}}
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-black/30 p-3">
                                        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 dark:text-slate-400">❤️ Kebahagiaan</span>
                                        <span class="mt-1 block text-sm font-black text-pink-600 dark:text-pink-300" x-text="petState.happiness + '%'">0%</span>
                                        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                                            <div class="h-full rounded-full bg-gradient-to-r from-pink-500 to-rose-400 transition-all duration-500"
                                                 :style="`width: ${Math.min(100, petState.happiness)}%`"></div>
                                        </div>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-black/30 p-3">
                                        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 dark:text-slate-400">⚡ Energi</span>
                                        <span class="mt-1 block text-sm font-black text-yellow-600 dark:text-yellow-300" x-text="petState.energy + '%'">0%</span>
                                        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                                            <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-yellow-300 transition-all duration-500"
                                                 :style="`width: ${Math.min(100, petState.energy)}%`"></div>
                                        </div>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-black/30 p-3">
                                        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 dark:text-slate-400">⚔️ Base Power</span>
                                        <span class="mt-1 block text-sm font-black text-amber-600 dark:text-amber-300"
                                              x-text="(petState.pet?.base_power || 10) + ' Power'">10 Power</span>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-black/30 p-3">
                                        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 dark:text-slate-400">✨ Elemen</span>
                                        <span class="mt-1 block break-words text-sm font-black capitalize text-cyan-600 dark:text-cyan-300"
                                              x-text="(petState.pet?.element_icon || '✨') + ' ' + (petState.pet?.element || 'Nature')">✨ Nature</span>
                                    </div>
                                </div>

                                {{-- Deskripsi pet --}}
                                <div class="rounded-2xl border border-sky-500/30 bg-sky-500/10 p-3.5">
                                    <p class="mb-1.5 text-[10.5px] font-extrabold uppercase tracking-[0.12em] text-sky-700 dark:text-sky-300/80">Tentang Pet</p>
                                    <p class="text-[12.5px] italic leading-relaxed text-slate-600 dark:text-slate-300" x-text="petState.pet?.description"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Alat tes cepat (dev) --}}
                    <div x-show="petState.status === 'hatched'"
                         class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-slate-200 dark:border-slate-800 pt-4 text-[11.5px] text-slate-600 dark:text-slate-400">
                        <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-slate-500">⚡ Tes Cepat (Dev)</span>
                        <div class="flex items-center gap-1.5">
                            <button type="button"
                                    @click="addDevPoints(25)"
                                    class="et-press rounded-lg border border-slate-200 bg-white text-sky-700 dark:border-white/10 dark:bg-white/10 dark:text-sky-200 px-2.5 py-1.5 font-mono text-[11px] font-bold transition hover:bg-slate-100 dark:hover:bg-white/20">
                                +25 EXP
                            </button>
                            <button type="button"
                                    @click="addDevPoints(100)"
                                    class="et-press rounded-lg border border-amber-500/30 bg-amber-500/15 px-2.5 py-1.5 font-mono text-[11px] font-bold text-amber-700 dark:text-amber-300 transition hover:bg-amber-500/25">
                                ⚡ Instant 100%
                            </button>
                        </div>
                    </div>
                </section>

                {{-- Panel Dompet Poin --}}
                <section class="rounded-3xl border-2 border-amber-400/40 bg-gradient-to-br from-amber-50 via-white to-yellow-50 p-4 shadow-xl shadow-amber-500/10 sm:p-5 et-reveal et-delay-2 dark:border-amber-500/30 dark:from-amber-950/40 dark:via-slate-900/80 dark:to-slate-950">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="flex items-center gap-2 text-[15px] font-extrabold text-slate-800 dark:text-white sm:text-base">
                            <span>💰</span> Dompet Poin
                        </h3>
                        <span class="shrink-0 rounded-full border border-amber-500/30 bg-amber-500/15 px-2.5 py-1 text-[11px] font-bold text-amber-700 dark:text-amber-300">
                            Poin kamu
                        </span>
                    </div>

                    <p class="mt-3 text-3xl font-extrabold tracking-tight text-amber-600 dark:text-amber-300">
                        <span x-text="fmt(balance)">{{ number_format((int) $balance, 0, ',', '.') }}</span>
                        <span class="text-base font-bold text-slate-500 dark:text-slate-400">poin</span>
                    </p>

                    <p class="mt-1.5 text-[12px] leading-relaxed text-slate-600 dark:text-slate-400">
                        Kumpulkan poin dari kuis &amp; video challenge, lalu beli alat untuk memecahkan telur! 🛒
                    </p>

                    <div class="mt-3.5 flex flex-wrap items-center gap-2">
                        <a href="{{ route('employee.shop.index') }}"
                           class="et-press inline-flex flex-1 items-center justify-center gap-1.5 rounded-2xl bg-gradient-to-r from-amber-400 to-yellow-400 px-4 py-3 text-[13px] font-extrabold text-slate-950 shadow-lg shadow-amber-500/30 transition hover:from-amber-300 hover:to-yellow-300">
                            🛒 ke Toko
                        </a>
                        <a href="{{ route('employee.points.history') }}"
                           class="et-press inline-flex items-center justify-center gap-1.5 rounded-2xl border-2 border-slate-200 bg-white px-3.5 py-3 text-[12.5px] font-extrabold text-slate-600 transition hover:border-amber-400/50 hover:text-amber-700 dark:border-white/15 dark:bg-white/5 dark:text-slate-300 dark:hover:text-amber-300">
                            📜 Riwayat
                        </a>
                    </div>
                </section>

                {{-- Panel Riwayat Aktivitas --}}
                <section class="rounded-3xl border-2 border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/70 p-4 shadow-xl shadow-slate-200/60 dark:shadow-black/30 backdrop-blur-sm sm:p-5 et-reveal et-delay-3">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="flex items-center gap-2 text-[15px] font-extrabold text-slate-800 dark:text-white sm:text-base">
                            <span>📜</span> Riwayat Aktivitas &amp; EXP
                        </h3>
                        <span class="shrink-0 rounded-full border border-sky-500/30 bg-sky-500/15 px-2.5 py-1 text-[11px] font-bold text-sky-700 dark:text-sky-200">
                            {{ $logs->count() }} log
                        </span>
                    </div>

                    <div class="mt-3.5 space-y-2.5">
                        @forelse($logs as $log)
                            <div class="et-lift flex items-start gap-2.5 rounded-2xl border border-slate-200 bg-slate-50 dark:border-white/5 dark:bg-black/30 p-3 transition hover:border-emerald-500/30">
                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-400" aria-hidden="true"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[13px] font-medium leading-snug text-slate-800 dark:text-slate-100 break-words line-clamp-2">{{ $log->description }}</p>
                                    <p class="mt-1 text-[11px] text-slate-500">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="shrink-0 rounded-lg border border-emerald-500/30 bg-emerald-500/15 px-2 py-1 font-mono text-[11px] font-extrabold text-emerald-700 dark:text-emerald-300">
                                    +{{ $log->growth_gained }} EXP
                                </span>
                            </div>
                        @empty
                            <div class="et-empty" style="padding: 28px 12px;">
                                <div class="et-empty__icon" style="width:60px;height:60px;font-size:26px;">📭</div>
                                <p class="et-empty__title" style="color:var(--et-ink);font-size:15px;">Belum ada riwayat aktivitas</p>
                                <p class="et-empty__text" style="color:var(--et-ink-soft);font-size:12.5px;">
                                    Ikuti kuis atau selesaikan video challenge untuk mengumpulkan poin pertamamu!
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>

        {{-- ================= BESTIARY ================= --}}
        <section class="mt-10 border-t border-slate-200 dark:border-slate-800/80 pt-8 sm:mt-14 sm:pt-10 et-reveal et-delay-4">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3 sm:mb-6">
                <div class="min-w-0">
                    <span class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-lime-600 dark:text-lime-300/90">Koleksi Spesies</span>
                    <h2 class="mt-1 text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2 sm:text-2xl">
                        <span>📖</span> Ensiklopedia Hewan (Bestiary)
                    </h2>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-400 sm:text-sm">
                        Daftar seluruh spesies hewan legendaris yang bisa kamu dapatkan melalui penetasan telur.
                    </p>
                </div>
                <span class="shrink-0 rounded-full border border-sky-500/30 bg-sky-500/15 px-3 py-1.5 text-[11.5px] font-bold text-sky-700 dark:text-sky-200">
                    {{ $allPets->count() }} spesies
                </span>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
                @foreach($allPets as $pet)
                    <div class="flex et-reveal et-delay-{{ min(6, $loop->index + 1) }}">
                        <article class="et-lift group relative flex flex-1 flex-col overflow-hidden rounded-3xl border-2 border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/70 p-4 hover:border-sky-500/40 hover:shadow-xl hover:shadow-sky-950/60">
                            {{-- Glow rarity saat hover --}}
                            <div class="pointer-events-none absolute -right-10 -top-10 h-24 w-24 rounded-full opacity-40 blur-2xl transition duration-300 group-hover:opacity-80
                                @if($pet->rarity === 'legendary') bg-amber-400
                                @elseif($pet->rarity === 'epic') bg-purple-400
                                @elseif($pet->rarity === 'rare') bg-blue-400
                                @else bg-slate-400 @endif" aria-hidden="true"></div>

                            <div class="relative flex items-start justify-between gap-2">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider
                                    @if($pet->rarity === 'legendary') bg-amber-100 text-amber-700 border-amber-300 dark:bg-amber-500/20 dark:text-amber-300 dark:border-amber-500/30
                                    @elseif($pet->rarity === 'epic') bg-purple-100 text-purple-700 border-purple-300 dark:bg-purple-500/20 dark:text-purple-300 dark:border-purple-500/30
                                    @elseif($pet->rarity === 'rare') bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-500/20 dark:text-blue-300 dark:border-blue-500/30
                                    @else bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-500/20 dark:text-slate-300 dark:border-slate-500/30 @endif">
                                    {{ $pet->rarity }}
                                </span>
                                <span class="shrink-0 text-xs font-bold text-slate-600 dark:text-slate-400">
                                    {{ $pet->element_icon }} {{ ucfirst($pet->element) }}
                                </span>
                            </div>

                            <h3 class="relative mt-3 text-base font-bold leading-snug text-slate-800 dark:text-white break-words">{{ $pet->species }}</h3>
                            <p class="relative mt-0.5 text-xs font-medium text-sky-700 dark:text-sky-300/80 break-words">{{ $pet->name }}</p>
                            <p class="relative mt-2 text-[11.5px] leading-relaxed text-slate-600 dark:text-slate-400 line-clamp-2">{{ $pet->description }}</p>

                            <div class="relative mt-3 flex items-center justify-between gap-2 border-t border-dashed border-slate-200 dark:border-slate-800 pt-2.5 text-[11px] text-slate-600 dark:text-slate-400">
                                <span>Power: <b class="text-amber-600 dark:text-amber-300">{{ $pet->base_power }}</b></span>
                                <span class="truncate font-mono capitalize text-sky-600 dark:text-sky-400">{{ $pet->model_preset }} 3D</span>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ================= KOLEKSI / HALL OF FAME ================= --}}
        <section class="mt-10 border-t border-slate-200 dark:border-slate-800/80 pt-8 sm:mt-14 sm:pt-10 et-reveal et-delay-4">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3 sm:mb-6">
                <div class="min-w-0">
                    <span class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-amber-600 dark:text-amber-300/90">Pet Pensiun</span>
                    <h2 class="mt-1 text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2 sm:text-2xl">
                        <span>🏆</span> Koleksi (Hall of Fame)
                    </h2>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-400 sm:text-sm">
                        Peliharaan yang sudah pensiun setelah bertelur — jasa mereka tercatat selamanya!
                    </p>
                </div>
                <span class="shrink-0 rounded-full border border-amber-500/30 bg-amber-500/15 px-3 py-1.5 text-[11.5px] font-bold text-amber-700 dark:text-amber-300">
                    <span x-text="archiveCount">{{ $archive->count() }}</span> koleksi
                </span>
            </div>

            @if($archive->isEmpty())
                <div class="et-empty">
                    <div class="et-empty__icon">🦉</div>
                    <p class="et-empty__title">Belum ada pet yang pensiun</p>
                    <p class="et-empty__text">Rawat peliharaanmu hingga dewasa lalu biakkan — Generasi berikutnya akan lahir! 🥚✨</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
                    {{-- Kartu baru hasil bertelur (muncul langsung tanpa reload) --}}
                    <template x-for="(entry, i) in extraArchive" :key="'extra-' + i">
                        <div class="flex et-reveal-pop">
                            <article class="et-lift group relative flex flex-1 flex-col overflow-hidden rounded-3xl border-2 border-amber-400/50 bg-gradient-to-br from-white to-amber-50 p-4 dark:border-amber-500/30 dark:from-slate-900 dark:to-amber-950/40">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider"
                                          :class="{
                                              'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-500/20 dark:text-emerald-300 dark:border-emerald-500/30': entry.rarity === 'common',
                                              'bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-500/20 dark:text-blue-300 dark:border-blue-500/30': entry.rarity === 'rare',
                                              'bg-purple-100 text-purple-700 border-purple-300 dark:bg-purple-500/20 dark:text-purple-300 dark:border-purple-500/30': entry.rarity === 'epic',
                                              'bg-amber-100 text-amber-700 border-amber-300 dark:bg-amber-500/20 dark:text-amber-300 dark:border-amber-500/30': entry.rarity === 'legendary'
                                          }"
                                          x-text="entry.rarity"></span>
                                    <span class="shrink-0 text-[11px] font-extrabold text-amber-600 dark:text-amber-300" x-text="'Gen ' + entry.generation"></span>
                                </div>

                                <div class="mt-3 flex items-center gap-2.5">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-xl shadow-inner"
                                          :style="`background: ${entry.color_primary || '#FDBA74'}22; border: 2px solid ${entry.color_primary || '#FDBA74'};`">🐾</span>
                                    <div class="min-w-0">
                                        <p class="truncate text-[15px] font-extrabold text-slate-800 dark:text-white" x-text="entry.nickname || entry.species"></p>
                                        <p class="truncate text-xs font-bold text-sky-700 dark:text-sky-300/80" x-text="entry.species"></p>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between gap-2 border-t border-dashed border-amber-300/60 pt-2.5 text-[11.5px] font-bold text-slate-600 dark:border-amber-500/20 dark:text-slate-400">
                                    <span x-text="'Gen ' + entry.generation + ' · Lv ' + entry.final_level"></span>
                                    <span class="opacity-70" x-text="formatDate(entry.hatched_at)"></span>
                                </div>
                            </article>
                        </div>
                    </template>

                    @foreach($archive as $entry)
                        <div class="flex et-reveal et-delay-{{ min(4, $loop->index + 1) }}">
                            <article class="et-lift group relative flex flex-1 flex-col overflow-hidden rounded-3xl border-2 bg-gradient-to-br from-white to-slate-50 p-4
                                @if($entry->rarity === 'legendary') border-amber-400/60 to-amber-50 dark:border-amber-500/40 dark:from-slate-900 dark:to-amber-950/30
                                @elseif($entry->rarity === 'epic') border-purple-400/60 to-purple-50 dark:border-purple-500/40 dark:from-slate-900 dark:to-purple-950/30
                                @elseif($entry->rarity === 'rare') border-sky-400/60 to-sky-50 dark:border-sky-500/40 dark:from-slate-900 dark:to-sky-950/30
                                @else border-slate-300 bg-white dark:border-slate-700 dark:from-slate-900 dark:to-slate-900 @endif">

                                <div class="flex items-start justify-between gap-2">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider
                                        @if($entry->rarity === 'legendary') bg-amber-100 text-amber-700 border-amber-300 dark:bg-amber-500/20 dark:text-amber-300 dark:border-amber-500/30
                                        @elseif($entry->rarity === 'epic') bg-purple-100 text-purple-700 border-purple-300 dark:bg-purple-500/20 dark:text-purple-300 dark:border-purple-500/30
                                        @elseif($entry->rarity === 'rare') bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-500/20 dark:text-blue-300 dark:border-blue-500/30
                                        @else bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-500/20 dark:text-slate-300 dark:border-slate-500/30 @endif">
                                        {{ $entry->rarity }}
                                    </span>
                                    <span class="shrink-0 text-[11px] font-extrabold text-amber-600 dark:text-amber-300">Gen {{ $entry->generation }}</span>
                                </div>

                                <div class="mt-3 flex items-center gap-2.5">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-xl shadow-inner"
                                          style="background: {{ $entry->color_primary ?? '#FDBA74' }}22; border: 2px solid {{ $entry->color_primary ?? '#FDBA74' }};">🐾</span>
                                    <div class="min-w-0">
                                        <p class="truncate text-[15px] font-extrabold text-slate-800 dark:text-white">{{ $entry->nickname ?: $entry->species }}</p>
                                        <p class="truncate text-xs font-bold text-sky-700 dark:text-sky-300/80">{{ $entry->species }}</p>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between gap-2 border-t border-dashed border-slate-200 pt-2.5 text-[11.5px] font-bold text-slate-600 dark:border-slate-700 dark:text-slate-400">
                                    <span>Gen {{ $entry->generation }} · Lv {{ $entry->final_level }}</span>
                                    <span class="opacity-70">{{ $entry->hatched_at?->format('d M Y') ?? '—' }}</span>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ================= MODAL KONFIRMASI BERTELUR ================= --}}
        <div x-show="breedModal"
             x-transition.opacity
             @click.outside="breedModal = false"
             class="fixed inset-0 z-[85] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm"
             role="dialog" aria-modal="true" aria-label="Konfirmasi bertelur">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 class="w-full max-w-md overflow-hidden rounded-[28px] border-4 border-pink-300 bg-gradient-to-br from-pink-100 via-rose-50 to-amber-100 text-center shadow-2xl dark:border-pink-400/60 dark:from-pink-300 dark:via-rose-200 dark:to-amber-200">
                <div class="px-6 py-7">
                    <p class="text-[12px] font-black uppercase tracking-[0.24em] text-pink-600">💕 Waktunya Bertelur</p>
                    <div class="mt-3 flex items-center justify-center gap-3 text-4xl" aria-hidden="true">
                        <span>🧑‍🍼</span>
                        <span class="et-heart-pop text-xl font-black text-pink-500/80">❤️</span>
                        <span>🥚</span>
                    </div>
                    <p class="mt-3 text-lg font-extrabold text-slate-900">
                        <span x-text="petState.nickname || 'Peliharaanmu'">Peliharaanmu</span> akan pensiun ke 🏆 Koleksi
                    </p>
                    <p class="mt-1.5 text-[13px] font-bold leading-relaxed text-slate-700">
                        Sebagai gantinya, telur Generasi
                        <b x-text="(petState.generation + 1)">{{ (int) $userPet->generation + 1 }}</b>
                        ber-tier <span x-text="nextTierMeta.icon + ' ' + nextTierMeta.label">🥉 Perunggu</span>
                        akan diletakkan di sarangmu — tier lebih tinggi = peluang hewan langka lebih besar! ✨
                    </p>
                </div>
                <div class="grid grid-cols-1 gap-2 border-t-2 border-pink-200/70 bg-white/50 p-4 sm:grid-cols-2 dark:border-pink-400/30 dark:bg-slate-900/40">
                    <button type="button"
                            @click="confirmBreed()"
                            :disabled="isProcessing"
                            class="et-press et-wiggle flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-amber-400 to-orange-500 px-4 py-3.5 text-[13px] font-black text-slate-950 shadow-lg shadow-amber-500/40 transition hover:from-amber-300 hover:to-orange-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-300 disabled:cursor-not-allowed disabled:opacity-60">
                        🥚 Bertelur Sekarang!
                    </button>
                    <button type="button"
                            @click="breedModal = false"
                            :disabled="isProcessing"
                            class="et-press rounded-2xl border-2 border-slate-200 bg-white px-4 py-3.5 text-[13px] font-extrabold text-slate-600 transition hover:border-slate-300 hover:text-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 disabled:opacity-60 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-white">
                        Nanti Dulu
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= TOAST GLOBAL ================= --}}
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-6 scale-95"
             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0 translate-x-6"
             class="fixed right-3 sm:right-5 top-[78px] z-[70] w-[calc(100vw-24px)] max-w-[380px] sm:w-auto"
             role="status" aria-live="polite">
            <div class="et-toast pointer-events-auto"
                 :class="toast.type === 'error' ? 'border-l-4 border-l-rose-500' : 'border-l-4 border-l-lime-500'">
                <span class="et-toast__icon"
                      :class="toast.type === 'error'
                          ? 'bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400'
                          : 'bg-lime-100 text-lime-600 dark:bg-lime-500/15 dark:text-lime-400'"
                      x-text="toast.type === 'error' ? '⚠️' : '✅'">✅</span>
                <div class="min-w-0 flex-1">
                    <p class="text-[13px] font-extrabold text-slate-800 dark:text-slate-100"
                       x-text="toast.type === 'error' ? 'Yah, gagal' : 'Mantap!'">Mantap!</p>
                    <p class="mt-0.5 text-[13px] leading-snug text-slate-600 dark:text-slate-300" x-text="toast.message"></p>
                </div>
                <button type="button" @click="toast.show = false"
                        class="et-press text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200"
                        aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- ================= BANNER NAIK TAHAP ================= --}}
        <div x-show="stageUp"
             x-transition.opacity
             class="pointer-events-none fixed inset-0 z-[80] flex items-center justify-center p-5 bg-slate-950/70 backdrop-blur-sm"
             role="alert" aria-live="assertive">
            <div class="et-stageup w-full max-w-sm rounded-[28px] border-4 border-amber-400 bg-gradient-to-br from-amber-200 via-yellow-100 to-lime-200 px-6 py-7 text-center shadow-2xl dark:border-amber-300 dark:from-amber-300 dark:via-yellow-200 dark:to-lime-300">
                <p class="text-[13px] font-black uppercase tracking-[0.24em] text-amber-700">✨ NAIK TAHAP! ✨</p>
                <p class="mt-3 text-5xl" x-text="stageUp ? stageUp.to_icon : ''">👶</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900" x-text="stageUp ? stageUp.to_label : ''">Remaja</p>
                <p class="mt-1 text-[13px] font-bold text-slate-700"
                   x-text="stageUp ? stageUp.from_label + ' ➜ ' + stageUp.to_label : ''"></p>
                <p class="mt-3 text-[12.5px] font-semibold text-slate-600">Terus beri makan & ikuti kuis biar cepat dewasa! 💪</p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script src="{{ asset('js/pet-3d-game.js') }}"></script>
<script>
    function petSanctuaryPage() {
        return {
            game: null,
            soundOn: true,
            isProcessing: false,
            selectedToolId: {{ $toolsJson->count() ? (int) $toolsJson->first()['id'] : 'null' }},
            tools: @json($toolsJson),
            foods: @json($foodsJson),
            balance: {{ (int) $balance }},
            archiveCount: {{ $archive->count() }},
            extraArchive: [],
            stageUp: null,
            breedModal: false,
            tiers: @json($tiersJson),
            toast: { show: false, message: '', type: 'success' },
            _toastTimer: null,
            _stageTimer: null,
            petState: {
                status: @json($userPet->status),
                life_stage: @json($userPet->life_stage),
                life_stage_label: @json($userPet->life_stage_label),
                life_stage_icon: @json($userPet->life_stage_icon),
                generation: {{ (int) $userPet->generation }},
                egg_tier: @json($userPet->egg_tier ?: 'bronze'),
                egg_tier_label: @json($eggTierMeta['label']),
                egg_tier_icon: @json($eggTierMeta['icon']),
                egg_tier_color: @json($eggTierMeta['color']),
                next_egg_tier: @json(\App\Services\PetService::tierForGeneration((int) $userPet->generation + 1)),
                is_adult: {{ $userPet->isAdult() ? 'true' : 'false' }},
                next_stage_level: @json($nextStageLevel),
                crack_points: {{ (int) $userPet->crack_points }},
                crack_percentage: {{ (int) $userPet->crack_percentage }},
                crack_stage: {{ $userPet->crack_stage }},
                growthPercentage: {{ $userPet->growth_percentage }},
                growthPoints: {{ $userPet->growth_points }},
                maxGrowthPoints: {{ $userPet->max_growth_points }},
                level: {{ $userPet->level }},
                experience: {{ $userPet->experience }},
                maxExperience: {{ $userPet->max_experience }},
                expPercentage: {{ $userPet->exp_percentage }},
                happiness: {{ $userPet->happiness }},
                energy: {{ $userPet->energy }},
                nickname: @json($userPet->nickname ?? 'My Egg'),
                pet: @json($userPet->pet),
            },

            timelineSteps: [
                { icon: '🥚', label: 'Telur',           active: 'border-amber-400 bg-amber-50 text-amber-700 dark:border-amber-400/50 dark:bg-amber-500/15 dark:text-amber-200' },
                { icon: '👶', label: 'Bayi',            active: 'border-lime-400 bg-lime-50 text-lime-700 dark:border-lime-400/50 dark:bg-lime-500/15 dark:text-lime-200' },
                { icon: '🧒', label: 'Remaja',          active: 'border-sky-400 bg-sky-50 text-sky-700 dark:border-sky-400/50 dark:bg-sky-500/15 dark:text-sky-200' },
                { icon: '🧑', label: 'Dewasa',          active: 'border-amber-400 bg-amber-50 text-amber-700 dark:border-amber-400/50 dark:bg-amber-500/15 dark:text-amber-200' },
                { icon: '🥚', label: 'Gen Berikutnya',  active: 'border-lime-400 bg-lime-50 text-lime-700 dark:border-lime-400/50 dark:bg-lime-500/15 dark:text-lime-200' },
            ],

            /* ============ Lifecycle ============ */
            initPage() {
                this.game = new Pet3DGame('sanctuary-3d-canvas', {
                    apiUrl: '{{ route("employee.pet.index") }}',
                    csrfToken: '{{ csrf_token() }}',
                    soundEnabled: this.soundOn,
                    onStateChange: (state) => {
                        this.petState = Object.assign({}, this.petState, state);
                    },
                    onHatched: (data) => {
                        this.isProcessing = false;
                        this.showToast('🐣 ' + (data?.message || 'Telur berhasil menetas!'), 'success');
                        // Sinkron penuh setelah animasi hatch selesai
                        setTimeout(() => this.syncStatus(), 800);
                    }
                });
                window.PetGame = this.game;
            },

            /* ============ Timeline tahap ============ */
            get stageIndex() {
                if (this.petState.status !== 'hatched') return 0;
                switch (this.petState.life_stage) {
                    case 'baby': return 1;
                    case 'juvenile': return 2;
                    case 'adult': return 3;
                    default: return 1;
                }
            },

            get eggReady() {
                return this.petState.status !== 'hatched' &&
                    (this.petState.status === 'ready_to_hatch' || this.petState.crack_percentage >= 100);
            },

            stepState(i) {
                if (i < this.stageIndex) return 'done';
                if (i === this.stageIndex) return 'active';
                if (i === 4 && this.stageIndex === 3) return 'goal';
                return 'todo';
            },

            stepClass(i) {
                const s = this.stepState(i);
                if (s === 'done') return 'border-emerald-400/70 bg-emerald-50 text-emerald-700 dark:border-emerald-400/40 dark:bg-emerald-500/15 dark:text-emerald-200';
                if (s === 'active') return this.timelineSteps[i].active;
                if (s === 'goal') return this.timelineSteps[i].active + ' et-wiggle';
                return 'border-slate-200 bg-white text-slate-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-400';
            },

            /* ============ Util ============ */
            fmt(n) {
                return new Intl.NumberFormat('id-ID').format(n || 0);
            },

            formatDate(d) {
                if (!d) return '—';
                try {
                    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                } catch (e) {
                    return '—';
                }
            },

            totalToolStock() {
                return this.tools.reduce((sum, t) => sum + (t.quantity || 0), 0);
            },

            showToast(message, type = 'success') {
                this.toast = { show: true, message: message || '', type };
                if (this._toastTimer) clearTimeout(this._toastTimer);
                this._toastTimer = setTimeout(() => { this.toast.show = false; }, 4500);
            },

            /** Panggil method efek 3D secara aman (null-safe). */
            gameFx(method, arg) {
                const g = window.PetGame;
                if (g && typeof g[method] === 'function') {
                    try { g[method](arg); } catch (e) { console.warn('PetGame fx error:', e); }
                }
            },

            async api(url, body = null) {
                return fetch(url, {
                    method: body === null ? 'GET' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: body === null ? undefined : JSON.stringify(body),
                });
            },

            /* ============ Sinkronisasi status ============ */
            applyStatus(d) {
                this.petState.status = d.status;
                this.petState.life_stage = d.life_stage;
                this.petState.life_stage_label = d.life_stage_label;
                this.petState.life_stage_icon = d.life_stage_icon;
                this.petState.is_adult = d.is_adult;
                this.petState.generation = d.generation;
                this.petState.egg_tier = d.egg_tier;
                this.petState.egg_tier_label = d.egg_tier_label;
                this.petState.egg_tier_icon = d.egg_tier_icon;
                this.petState.egg_tier_color = d.egg_tier_color;
                this.petState.next_egg_tier = d.next_egg_tier;
                this.petState.next_stage_level = d.next_stage_level;
                this.petState.crack_points = d.crack_points;
                this.petState.crack_percentage = d.crack_percentage;
                this.petState.crack_stage = d.crack_stage;
                this.petState.growthPoints = d.growth_points;
                this.petState.maxGrowthPoints = d.max_growth_points;
                this.petState.growthPercentage = d.growth_percentage;
                this.petState.level = d.level;
                this.petState.experience = d.experience;
                this.petState.maxExperience = d.max_experience;
                this.petState.expPercentage = d.exp_percentage;
                this.petState.happiness = d.happiness;
                this.petState.energy = d.energy;
                this.petState.nickname = d.nickname;
                this.petState.pet = d.pet;

                this.balance = d.points;
                this.archiveCount = d.archive_count;
                this.tools = (d.tools || []).map(t => Object.assign({}, t));
                this.foods = (d.foods || []).map(f => Object.assign({}, f));

                const stillThere = this.tools.find(t => t.id === this.selectedToolId);
                if (!stillThere) this.selectedToolId = this.tools.length ? this.tools[0].id : null;
            },

            async syncStatus() {
                try {
                    const res = await this.api('{{ route("employee.pet.status") }}');
                    const data = await res.json();
                    if (!data.success) return;
                    if (this.game && typeof this.game.updateState === 'function') {
                        this.game.updateState(data); // render ulang egg/pet (crack_stage & skala tahap)
                    }
                    this.applyStatus(data);
                } catch (e) {
                    console.warn('Status sync error:', e);
                }
            },

            /* ============ Fase telur ============ */
            async strikeEgg() {
                if (this.isProcessing || !this.selectedToolId || this.eggReady) return;
                this.isProcessing = true;
                try {
                    const res = await this.api('{{ route("employee.pet.use-tool") }}', { item_id: this.selectedToolId });
                    const data = await res.json();
                    if (!data.success) {
                        this.showToast(data.message || 'Pukulan gagal, coba lagi ya!', 'error');
                        if (data.message && data.message.includes('habis')) await this.syncStatus();
                        return;
                    }

                    this.petState.crack_points = data.crack_points;
                    this.petState.crack_percentage = data.crack_percentage;
                    this.petState.status = data.status;

                    this.gameFx('useToolStrike');
                    this.showToast(data.message, 'success');

                    await this.syncStatus(); // gambar ulang garis retak + refresh stok alat

                    if (data.ready_to_hatch) {
                        this.showToast('🎉 Telur 100% retak — pecahkan sekarang!', 'success');
                        this.gameFx('playSound', 'fanfare');
                    }
                } catch (e) {
                    console.error('Use tool error:', e);
                    this.showToast('Ups, ada gangguan. Coba lagi ya! 🙏', 'error');
                } finally {
                    this.isProcessing = false;
                }
            },

            async tapEgg() {
                if (this.isProcessing || this.eggReady) return;
                this.isProcessing = true;
                try {
                    const res = await this.api('{{ route("employee.pet.crack") }}', {});
                    const data = await res.json();
                    if (!data.success) {
                        this.showToast(data.message || 'Gagal mengetuk telur', 'error');
                        return;
                    }

                    this.petState.crack_points = data.crack_points;
                    this.petState.crack_percentage = data.crack_percentage;
                    this.petState.status = data.status;

                    this.gameFx('triggerWobble');
                    this.showToast(data.message, 'success');

                    await this.syncStatus();

                    if (data.ready_to_hatch) {
                        this.showToast('🎉 Telur 100% retak — pecahkan sekarang!', 'success');
                    }
                } catch (e) {
                    console.error('Tap crack error:', e);
                    this.showToast('Ups, ada gangguan. Coba lagi ya! 🙏', 'error');
                } finally {
                    this.isProcessing = false;
                }
            },

            triggerHatch() {
                if (this.game && !this.isProcessing) {
                    this.isProcessing = true;
                    this.game.triggerHatchSequence();
                    setTimeout(() => { this.isProcessing = false; }, 2500);
                }
            },

            /* ============ Fase pet ============ */
            async feedPet() {
                if (this.isProcessing) return;
                this.isProcessing = true;
                try {
                    const res = await this.api('{{ route("employee.pet.feed") }}', {});
                    const data = await res.json();
                    if (!data.success) {
                        this.showToast(data.message || 'Gagal memberi makan', 'error');
                        return;
                    }

                    this.petState.level = data.level;
                    this.petState.experience = data.experience;
                    this.petState.maxExperience = data.max_experience;
                    this.petState.expPercentage = Math.min(100, Math.round((data.experience / Math.max(1, data.max_experience)) * 100));
                    this.petState.happiness = data.happiness;
                    this.petState.energy = data.energy;
                    this.petState.life_stage = data.life_stage;

                    this.gameFx('feedAnimation');
                    this.showToast(data.message, 'success');

                    await this.syncStatus();

                    if (data.stage_up) this.showStageUp(data.stage_up);
                } catch (e) {
                    console.error('Feed error:', e);
                    this.showToast('Ups, ada gangguan. Coba lagi ya! 🙏', 'error');
                } finally {
                    this.isProcessing = false;
                }
            },

            async petCreature() {
                if (this.isProcessing) return;
                this.gameFx('petJumpAnimation');
                this.isProcessing = true;
                try {
                    const res = await this.api('{{ route("employee.pet.pet") }}', {});
                    const data = await res.json();
                    if (data.success) {
                        if (data.user_pet) {
                            this.petState.happiness = data.user_pet.happiness;
                            this.petState.energy = data.user_pet.energy;
                        }
                        this.showToast(data.message, 'success');
                    } else {
                        this.showToast(data.message || 'Gagal mengelus pet', 'error');
                    }
                } catch (e) {
                    console.error('Pet error:', e);
                    this.showToast('Ups, ada gangguan. Coba lagi ya! 🙏', 'error');
                } finally {
                    this.isProcessing = false;
                }
            },

            /** Tier telur untuk generasi tertentu (mirror PetService::tierForGeneration). */
            tierForGeneration(gen) {
                let key = 'bronze';
                this.tiers.forEach(t => { if (gen >= t.min_generation) key = t.key; });
                return key;
            },

            /** Meta tier telur generasi berikutnya (untuk modal bertelur). */
            get nextTierMeta() {
                return this.tiers.find(t => t.key === this.petState.next_egg_tier) || this.tiers[0];
            },

            async breedPet() {
                if (this.isProcessing || !this.petState.is_adult) return;
                this.breedModal = true;
            },

            async confirmBreed() {
                if (this.isProcessing) return;
                this.breedModal = false;
                this.isProcessing = true;
                try {
                    const res = await this.api('{{ route("employee.pet.breed") }}', {});
                    const data = await res.json();
                    if (!data.success) {
                        this.showToast(data.message || 'Gagal bertelur', 'error');
                        return;
                    }

                    // Animasi hati di panggung + konfeti
                    this.spawnStageHearts();
                    this.gameFx('heartBurst', 10);
                    this.gameFx('playSound', 'fanfare');
                    if (typeof confetti === 'function') {
                        confetti({
                            particleCount: 130,
                            spread: 90,
                            origin: { y: 0.6 },
                            colors: ['#FF4081', '#FF80AB', '#FFD700', '#A3E635', '#38BDF8'],
                        });
                    }

                    if (data.archived) {
                        this.extraArchive.unshift(data.archived);
                        this.archiveCount += 1;
                    }

                    // Reset state lokal ke fase telur generasi baru
                    this.petState.generation = data.generation;
                    this.petState.egg_tier = data.egg_tier;
                    this.petState.egg_tier_label = data.egg_tier_label;
                    this.petState.egg_tier_icon = data.egg_tier_icon;
                    this.petState.egg_tier_color = data.egg_tier_color;
                    this.petState.next_egg_tier = this.tierForGeneration(data.generation + 1);
                    this.petState.status = 'egg';
                    this.petState.life_stage = 'egg';
                    this.petState.life_stage_label = 'Telur';
                    this.petState.life_stage_icon = '🥚';
                    this.petState.is_adult = false;
                    this.petState.next_stage_level = null;
                    this.petState.crack_points = 0;
                    this.petState.crack_percentage = 0;
                    this.petState.crack_stage = 0;
                    this.petState.pet = null;
                    this.petState.nickname = null;
                    this.petState.level = 1;
                    this.petState.experience = 0;
                    this.petState.maxExperience = 100;
                    this.petState.expPercentage = 0;

                    this.showToast(data.message, 'success');
                    await this.syncStatus();
                } catch (e) {
                    console.error('Breed error:', e);
                    this.showToast('Ups, ada gangguan. Coba lagi ya! 🙏', 'error');
                } finally {
                    this.isProcessing = false;
                }
            },

            showStageUp(stageUp) {
                this.stageUp = stageUp;
                this.gameFx('playSound', 'levelup');
                if (typeof confetti === 'function') {
                    confetti({
                        particleCount: 140,
                        spread: 95,
                        origin: { y: 0.55 },
                        colors: ['#A3E635', '#38BDF8', '#FBBF24', '#FF4081', '#FFFFFF'],
                    });
                }
                if (this._stageTimer) clearTimeout(this._stageTimer);
                this._stageTimer = setTimeout(() => { this.stageUp = null; }, 3500);
            },

            /** Beberapa elemen ❤️ dengan class et-heart-pop di area panggung. */
            spawnStageHearts() {
                const stage = document.getElementById('stage-fx');
                if (!stage) return;
                stage.innerHTML = '';
                for (let i = 0; i < 7; i++) {
                    const el = document.createElement('span');
                    el.className = 'et-heart-pop absolute text-2xl sm:text-3xl pointer-events-none';
                    el.textContent = '❤️';
                    el.style.left = (18 + Math.random() * 64) + '%';
                    el.style.top = (32 + Math.random() * 42) + '%';
                    el.style.animationDelay = (Math.random() * 0.45).toFixed(2) + 's';
                    stage.appendChild(el);
                    setTimeout(() => el.remove(), 2300);
                }
            },

            /* ============ Kontrol & dev ============ */
            toggleSound() {
                this.soundOn = !this.soundOn;
                if (this.game) {
                    this.game.soundEnabled = this.soundOn;
                }
            },

            triggerWobble() {
                if (this.game) this.game.triggerWobble();
            },

            async addDevPoints(amount) {
                if (this.game) {
                    await this.game.addPoints(amount);
                    await this.syncStatus();
                }
            },

            async resetEgg() {
                if (confirm('Apakah kamu ingin menetaskan telur baru? (Pet saat ini akan diganti dengan telur baru)')) {
                    if (this.game) {
                        await this.game.resetNewEgg();
                        await this.syncStatus();
                    }
                }
            }
        };
    }
</script>
@endpush
@endsection
