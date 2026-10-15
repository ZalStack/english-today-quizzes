{{-- resources/views/employee/shop/index.blade.php — 🛒 Toko Peralatan (alat pecah telur & makanan) --}}
@extends('layouts.app')

@section('title', 'Toko Peralatan')

@section('content')
@php
    // Katalog toko → JSON siap pakai Alpine
    $toJson = fn ($item) => [
        'id' => (int) $item->id,
        'name' => $item->name,
        'slug' => $item->slug,
        'category' => $item->category,
        'description' => $item->description,
        'icon' => $item->icon,
        'price' => (int) $item->price,
        'power' => (int) $item->power,
        'uses' => (int) $item->uses,
        'effect_summary' => $item->effect_summary,
    ];

    $toolsJson = collect($tools)->map($toJson)->values();
    $foodsJson = collect($foods)->map($toJson)->values();

    // Inventaris milik user → JSON siap pakai Alpine
    $inventoryJson = collect($inventory)->map(fn ($row) => [
        'id' => (int) $row->shop_item_id,
        'name' => $row->shopItem?->name ?? 'Item',
        'icon' => $row->shopItem?->icon ?? '🎁',
        'category' => $row->shopItem?->category ?? 'food',
        'description' => $row->shopItem?->description ?? '',
        'effect_summary' => $row->shopItem?->effect_summary ?? '',
        'uses' => (int) ($row->shopItem?->uses ?? 1),
        'quantity' => (int) $row->quantity,
    ])->values();

    $petStatus = $userPet?->status;
@endphp

{{-- ================= HALAMAN TOKO ================= --}}
<div x-data="shopPage()"
     class="relative min-h-[calc(100vh-65px)] overflow-x-hidden bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-white"
     @keydown.escape.window="historyOpen = false">

    {{-- Glow ambient latar toko --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-32 -left-24 h-[340px] w-[340px] rounded-full bg-lime-500/10 blur-3xl dark:bg-lime-500/15"></div>
        <div class="absolute top-[14%] left-[30%] h-[260px] w-[260px] rounded-full bg-amber-500/10 blur-3xl dark:bg-amber-500/15"></div>
        <div class="absolute top-[38%] -right-28 h-[320px] w-[320px] rounded-full bg-sky-500/5 blur-3xl dark:bg-sky-500/10"></div>
        <div class="absolute -bottom-24 left-1/3 h-[260px] w-[380px] rounded-full bg-purple-500/5 blur-3xl dark:bg-purple-500/10"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 pb-14 pt-6 sm:px-6 sm:pt-8 lg:px-8 lg:pt-10">

        {{-- ================= HEADER ================= --}}
        <header class="et-reveal">
            <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[12px] font-semibold" aria-label="Breadcrumb">
                <a href="{{ route('employee.dashboard') }}"
                   class="et-press inline-flex items-center gap-1.5 rounded-md text-sky-600 transition hover:text-sky-500 dark:text-sky-300 dark:hover:text-sky-200">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Dashboard
                </a>
                <span class="text-slate-500" aria-hidden="true">/</span>
                <span class="text-slate-600 dark:text-slate-300">🛒 Toko Peralatan</span>
            </nav>

            <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex min-w-0 items-start gap-3">
                    <span class="et-mascot et-mascot--bounce h-12 w-12 shrink-0 border-lime-400 bg-white text-2xl dark:border-lime-500 dark:bg-slate-800 sm:h-14 sm:w-14 sm:text-3xl"
                          aria-hidden="true">🛒</span>
                    <div class="min-w-0">
                        <span class="inline-flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-[0.14em] text-amber-600 dark:text-amber-300/90">
                            🛒 Pasar Perlengkapan
                        </span>
                        <h1 class="mt-1.5 break-words bg-gradient-to-r from-lime-500 via-emerald-500 to-sky-600 bg-clip-text text-[27px] font-extrabold leading-[1.12] text-transparent sm:text-4xl lg:text-[42px] dark:from-lime-300 dark:via-emerald-300 dark:to-sky-300">
                            Toko Peralatan
                        </h1>
                        <p class="mt-2.5 max-w-2xl text-[13.5px] leading-relaxed text-slate-600 sm:text-[15px] dark:text-slate-400">
                            Kumpulkan poin dari kuis &amp; video challenge → beli alat untuk memecahkan telur → rawat peliharaanmu! ✨
                        </p>
                    </div>
                </div>

                {{-- Panel Saldo (menonjol) --}}
                <div class="et-reveal-pop w-full shrink-0 rounded-3xl border-2 border-amber-400/50 bg-gradient-to-br from-amber-50 via-white to-yellow-50 p-4 shadow-xl shadow-amber-500/10 sm:p-5 lg:w-[300px] dark:border-amber-500/30 dark:from-amber-950/40 dark:via-slate-900/80 dark:to-slate-950">
                    <div class="flex items-center gap-3">
                        <span id="et-shop-coin"
                              class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-tr from-amber-400 to-yellow-300 text-2xl shadow-lg shadow-amber-500/30 sm:h-14 sm:w-14 sm:text-3xl"
                              aria-hidden="true">🪙</span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-amber-700 dark:text-amber-300/90">Saldo kamu</p>
                            <p class="mt-0.5 leading-none text-amber-600 dark:text-amber-300">
                                <span id="et-shop-balance" class="text-[26px] font-extrabold tracking-tight sm:text-3xl">{{ number_format((int) $balance, 0, ',', '.') }}</span>
                                <span class="text-[13px] font-bold text-slate-500 dark:text-slate-400">poin</span>
                            </p>
                        </div>
                    </div>

                    <button type="button"
                            @click="openHistory()"
                            class="et-press mt-3.5 flex w-full items-center justify-center gap-1.5 rounded-2xl border-2 border-slate-200 bg-white px-3.5 py-2.5 text-[12.5px] font-extrabold text-slate-600 transition hover:border-amber-400/60 hover:text-amber-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400/60 dark:border-white/15 dark:bg-white/5 dark:text-slate-300 dark:hover:text-amber-300">
                        📜 Riwayat Poin
                    </button>
                </div>
            </div>
        </header>

        {{-- ================= TAB ================= --}}
        <div class="et-reveal et-delay-1 mt-6 flex items-center gap-2 overflow-x-auto pb-1.5 et-no-scrollbar"
             role="tablist" aria-label="Kategori toko">
            <button type="button"
                    role="tab"
                    :aria-selected="tab === 'tool'"
                    @click="tab = 'tool'"
                    class="et-press inline-flex shrink-0 items-center gap-1.5 rounded-full border-2 px-3.5 py-2 text-[13px] font-extrabold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/60"
                    :class="tab === 'tool'
                        ? 'border-lime-600 bg-lime-500 text-white shadow-[0_3px_0_#46a302]'
                        : 'border-slate-200 bg-white text-slate-600 hover:border-lime-300 hover:text-lime-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-lime-500/50 dark:hover:text-lime-300'">
                <span>🔨 Alat Pecah Telur</span>
                <span class="rounded-full px-1.5 py-0.5 text-[11px] font-black"
                      :class="tab === 'tool' ? 'bg-black/20' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400'"
                      x-text="tools.length">{{ $tools->count() }}</span>
            </button>

            <button type="button"
                    role="tab"
                    :aria-selected="tab === 'food'"
                    @click="tab = 'food'"
                    class="et-press inline-flex shrink-0 items-center gap-1.5 rounded-full border-2 px-3.5 py-2 text-[13px] font-extrabold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/60"
                    :class="tab === 'food'
                        ? 'border-lime-600 bg-lime-500 text-white shadow-[0_3px_0_#46a302]'
                        : 'border-slate-200 bg-white text-slate-600 hover:border-lime-300 hover:text-lime-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-lime-500/50 dark:hover:text-lime-300'">
                <span>🍼 Makanan</span>
                <span class="rounded-full px-1.5 py-0.5 text-[11px] font-black"
                      :class="tab === 'food' ? 'bg-black/20' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400'"
                      x-text="foods.length">{{ $foods->count() }}</span>
            </button>

            <button type="button"
                    role="tab"
                    :aria-selected="tab === 'inventory'"
                    @click="tab = 'inventory'"
                    class="et-press inline-flex shrink-0 items-center gap-1.5 rounded-full border-2 px-3.5 py-2 text-[13px] font-extrabold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/60"
                    :class="tab === 'inventory'
                        ? 'border-lime-600 bg-lime-500 text-white shadow-[0_3px_0_#46a302]'
                        : 'border-slate-200 bg-white text-slate-600 hover:border-lime-300 hover:text-lime-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-lime-500/50 dark:hover:text-lime-300'">
                <span>🎒 Inventaris</span>
                <span class="rounded-full px-1.5 py-0.5 text-[11px] font-black"
                      :class="tab === 'inventory' ? 'bg-black/20' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400'"
                      x-text="inventory.length">{{ $inventory->count() }}</span>
            </button>
        </div>

        {{-- ================= PANEL ================= --}}
        <section class="mt-3 sm:mt-4" aria-live="polite">

            {{-- ===== Katalog (Alat / Makanan) ===== --}}
            <div x-show="tab !== 'inventory'">
                <template x-if="currentItems.length === 0">
                    <div class="et-empty">
                        <div class="et-empty__icon">🦉</div>
                        <p class="et-empty__title">Belum ada barang di kategori ini</p>
                        <p class="et-empty__text">Coba cek tab lainnya ya — siapa tahu ada yang cocok untuk peliharaanmu!</p>
                    </div>
                </template>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                    <template x-for="item in currentItems" :key="'cat-' + item.id">
                        <article class="et-lift group relative flex h-full flex-col rounded-3xl border-2 border-slate-200 bg-white p-3 shadow-[0_3px_0_rgba(60,60,60,.08)] transition-all hover:border-lime-400/60 hover:shadow-xl sm:p-4 dark:border-slate-800 dark:bg-slate-900/70 dark:shadow-black/30 dark:hover:border-lime-500/40"
                                 :class="shakeId === item.id ? 'et-shake' : ''">

                            {{-- Papan ikon item --}}
                            <div class="flex h-16 w-full items-center justify-center rounded-2xl border-2 sm:h-20"
                                 :class="item.category === 'tool'
                                     ? 'border-sky-200 bg-gradient-to-br from-sky-100 to-cyan-100 dark:border-sky-400/30 dark:from-sky-500/25 dark:to-cyan-500/10'
                                     : 'border-amber-200 bg-gradient-to-br from-amber-100 to-orange-100 dark:border-amber-400/30 dark:from-amber-500/25 dark:to-orange-500/10'">
                                <span class="text-3xl leading-none transition-transform duration-300 group-hover:-rotate-6 group-hover:scale-110 sm:text-4xl"
                                      x-text="item.icon" aria-hidden="true">🔨</span>
                            </div>

                            <h3 class="mt-3 break-words text-[14px] font-extrabold leading-snug text-slate-800 sm:text-[15px] dark:text-white"
                                x-text="item.name">Nama Item</h3>
                            <p class="mt-1 line-clamp-2 text-[11.5px] leading-relaxed text-slate-500 dark:text-slate-400"
                               x-text="item.description">Deskripsi singkat item.</p>

                            {{-- Badge efek --}}
                            <span class="mt-2 self-start rounded-full border px-2 py-1 text-[10px] font-extrabold leading-tight"
                                  :class="item.category === 'tool'
                                      ? 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-400/40 dark:bg-sky-500/15 dark:text-sky-300'
                                      : 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-400/40 dark:bg-emerald-500/15 dark:text-emerald-300'"
                                  x-text="item.effect_summary">+5 retak · 4 kali pakai</span>

                            {{-- Harga + tombol beli --}}
                            <div class="mt-auto flex flex-col gap-2 pt-3">
                                <p class="text-[15px] font-extrabold leading-none text-amber-600 dark:text-amber-300">
                                    🪙 <span x-text="fmt(item.price)">0</span>
                                </p>
                                <button type="button"
                                        @click="buy(item, $event.currentTarget)"
                                        :disabled="buyingId === item.id"
                                        class="et-press w-full rounded-2xl border-2 px-3 py-2.5 text-[13px] font-extrabold text-white transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lime-300 disabled:cursor-not-allowed disabled:opacity-60"
                                        :class="boughtFlash[item.id]
                                            ? 'border-emerald-600 bg-emerald-500 shadow-[0_3px_0_#059669]'
                                            : 'border-lime-600 bg-lime-500 shadow-[0_3px_0_#46a302] hover:bg-lime-400 active:translate-y-[2px] active:shadow-[0_1px_0_#46a302]'">
                                    <span x-text="buyLabel(item)">Beli</span>
                                </button>
                            </div>
                        </article>
                    </template>
                </div>
            </div>

            {{-- ===== Inventaris ===== --}}
            <div x-show="tab === 'inventory'">
                <template x-if="inventory.length === 0">
                    <div class="et-empty">
                        <div class="et-empty__icon">🦉</div>
                        <p class="et-empty__title">Belum ada stok — beli alat &amp; makanan dulu ya!</p>
                        <p class="et-empty__text">Kumpulkan poin dari kuis &amp; video challenge, lalu belanja alat pecah telur 🪓 atau makanan 🍼 di sini.</p>
                        <button type="button"
                                @click="tab = 'tool'"
                                class="et-press mt-4 inline-flex items-center gap-1.5 rounded-2xl border-2 border-lime-600 bg-lime-500 px-4 py-2.5 text-[13px] font-extrabold text-white shadow-[0_3px_0_#46a302] transition hover:bg-lime-400 active:translate-y-[2px]">
                            🛒 Mulai Belanja
                        </button>
                    </div>
                </template>

                <template x-if="inventory.length > 0">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3">
                        <template x-for="inv in inventory" :key="'inv-' + inv.id">
                            <article class="et-lift flex items-start gap-3 rounded-3xl border-2 border-slate-200 bg-white p-3.5 shadow-[0_3px_0_rgba(60,60,60,.08)] transition-all hover:border-sky-400/50 sm:p-4 dark:border-slate-800 dark:bg-slate-900/70 dark:shadow-black/30 dark:hover:border-sky-500/40"
                                     :class="shakeId === inv.id ? 'et-shake' : ''">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border-2 text-2xl"
                                      :class="inv.category === 'tool'
                                          ? 'border-sky-200 bg-gradient-to-br from-sky-100 to-cyan-100 dark:border-sky-400/30 dark:from-sky-500/25 dark:to-cyan-500/10'
                                          : 'border-amber-200 bg-gradient-to-br from-amber-100 to-orange-100 dark:border-amber-400/30 dark:from-amber-500/25 dark:to-orange-500/10'"
                                      x-text="inv.icon" aria-hidden="true">🎁</span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[14.5px] font-extrabold text-slate-800 dark:text-white" x-text="inv.name">Nama Item</p>
                                    <p class="mt-0.5 text-[11.5px] leading-relaxed text-slate-500 dark:text-slate-400"
                                       x-text="inv.effect_summary">+30 EXP</p>

                                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                        <span class="rounded-full border border-slate-200 bg-slate-50 px-2 py-1 text-[10.5px] font-extrabold text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300">
                                            Stok: <b x-text="inv.quantity">1</b>
                                        </span>
                                        <template x-if="inv.category === 'tool'">
                                            <span class="rounded-full border border-sky-200 bg-sky-50 px-2 py-1 text-[10.5px] font-extrabold text-sky-700 dark:border-sky-400/40 dark:bg-sky-500/15 dark:text-sky-300">
                                                Dipakai di 🥚 Sanctuary
                                            </span>
                                        </template>
                                    </div>

                                    <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                        <template x-if="inv.category === 'food'">
                                            <button type="button"
                                                    @click="useFood(inv, $event.currentTarget)"
                                                    :disabled="usingId === inv.id"
                                                    class="et-press rounded-2xl border-2 px-4 py-2 text-[12.5px] font-extrabold text-white transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lime-300 disabled:cursor-not-allowed disabled:opacity-60"
                                                    :class="usedFlash === inv.id
                                                        ? 'border-emerald-600 bg-emerald-500 shadow-[0_3px_0_#059669]'
                                                        : 'border-lime-600 bg-lime-500 shadow-[0_3px_0_#46a302] hover:bg-lime-400 active:translate-y-[2px] active:shadow-[0_1px_0_#46a302]'">
                                                <span x-text="useLabel(inv)">🍼 Pakai</span>
                                            </button>
                                        </template>

                                        <template x-if="inv.category === 'tool'">
                                            <a href="{{ route('employee.pet.index') }}"
                                               class="et-press inline-flex items-center gap-1.5 rounded-2xl border-2 border-slate-200 bg-white px-3.5 py-2 text-[12.5px] font-extrabold text-slate-600 transition hover:border-sky-400/60 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/60 dark:border-white/15 dark:bg-white/5 dark:text-slate-300 dark:hover:text-sky-300">
                                                🥚 Buka Sanctuary
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </div>
                </template>
            </div>
        </section>

        {{-- ================= FOOTER CTA ================= --}}
        <section class="et-reveal et-delay-2 mt-10 sm:mt-12">
            <div class="relative overflow-hidden rounded-3xl border-2 border-dashed border-lime-400/60 bg-gradient-to-r from-lime-50 via-white to-emerald-50 p-5 sm:p-6 dark:border-lime-500/40 dark:from-lime-950/40 dark:via-slate-900 dark:to-emerald-950/40">
                <div class="et-confetti" aria-hidden="true">
                    @foreach([12, 27, 44, 58, 71, 88] as $i)
                        <i style="left: {{ $i }}%; background: {{ ['#58cc02', '#ffc800', '#1cb0f6', '#ff9600'][$loop->index % 4] }};
                                   animation-duration: {{ 3.6 + ($i % 5) * .6 }}s;
                                   animation-delay: {{ ($i % 7) * .5 }}s;"></i>
                    @endforeach
                </div>

                <div class="relative z-10 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-[17px] font-extrabold leading-snug text-slate-800 sm:text-xl dark:text-white">
                            🥚 Sudah punya telur? Ayo pecahkan di Pet Sanctuary!
                        </p>
                        <p class="mt-1 text-[13px] leading-relaxed text-slate-600 dark:text-slate-400">
                            @if($petStatus && $petStatus !== 'hatched')
                                Telurmu masih menunggu untuk dipecahkan — pakai alat yang baru kamu beli biar makin cepat netas! 🔨
                            @elseif($petStatus === 'hatched')
                                Peliharaanmu butuh perhatian — beri makanan favoritnya supaya makin bahagia &amp; cepat dewasa! 💪
                            @else
                                Kunjungi Pet Sanctuary untuk mendapatkan telur pertamamu, lalu rawat sampai dewasa! ✨
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('employee.pet.index') }}"
                       class="et-press et-wiggle inline-flex shrink-0 items-center gap-2 rounded-2xl border-2 border-lime-600 bg-lime-500 px-5 py-3 text-[14px] font-extrabold text-white shadow-[0_4px_0_#46a302] transition hover:bg-lime-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lime-300 active:translate-y-[2px] active:shadow-[0_2px_0_#46a302]">
                        🥚 Buka Pet Sanctuary
                    </a>
                </div>
            </div>
        </section>
    </div>

    {{-- ================= TOAST DINAMIS (mekanisme layout: event `et-toast`) ================= --}}
    <div id="et-shop-toasts"
         x-data="etToasts()"
         @et-toast.window="push($event.detail)"
         class="pointer-events-none fixed left-3 right-3 top-[78px] z-[70] space-y-2.5 sm:left-auto sm:right-5 sm:w-[380px]"
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
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="icon(toast.type)"></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-[13px] font-bold capitalize text-slate-800 dark:text-slate-100"
                       x-text="toast.type === 'success' ? 'Berhasil' : (toast.type === 'error' ? 'Gagal' : 'Informasi')"></p>
                    <p class="mt-0.5 text-[13px] leading-snug text-slate-600 dark:text-slate-300" x-text="toast.message"></p>
                </div>
                <button type="button" @click="dismiss(toast.id)"
                        class="et-press transition hover:text-slate-600 dark:hover:text-slate-200"
                        aria-label="Tutup notifikasi">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    {{-- ================= MODAL RIWAYAT POIN ================= --}}
    <div x-show="historyOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[75] flex items-center justify-center p-4"
         role="dialog" aria-modal="true" aria-labelledby="etShopHistoryTitle">
        <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm" @click="historyOpen = false" aria-hidden="true"></div>

        <div class="et-reveal-pop relative z-10 flex max-h-[82vh] w-full max-w-lg flex-col overflow-hidden rounded-3xl border-2 border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-3 border-b-2 border-slate-100 bg-gradient-to-r from-amber-50 to-yellow-50 px-5 py-4 dark:border-slate-800 dark:from-amber-950/40 dark:to-slate-900">
                <div class="min-w-0">
                    <h3 id="etShopHistoryTitle" class="text-[16px] font-extrabold text-slate-800 dark:text-white">📜 Riwayat Poin</h3>
                    <p class="mt-0.5 text-[12px] text-slate-500 dark:text-slate-400">
                        Saldo saat ini:
                        <b class="text-amber-600 dark:text-amber-300">🪙 <span x-text="fmt(historyBalance)">{{ number_format((int) $balance, 0, ',', '.') }}</span></b>
                    </p>
                </div>
                <button type="button"
                        @click="historyOpen = false"
                        class="et-press flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-white hover:text-slate-600 dark:hover:bg-white/10 dark:hover:text-slate-200"
                        aria-label="Tutup riwayat poin">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="et-scroll-y max-h-[60vh] overflow-y-auto p-4">
                {{-- Loading --}}
                <template x-if="historyLoading">
                    <div class="py-10 text-center">
                        <div class="flex justify-center">
                            <span class="et-mascot et-mascot--bounce h-14 w-14 text-2xl" aria-hidden="true">🦉</span>
                        </div>
                        <p class="mt-3 text-[13px] font-bold text-slate-500 dark:text-slate-400">Memuat riwayat poin...</p>
                    </div>
                </template>

                {{-- Kosong --}}
                <template x-if="!historyLoading && transactions.length === 0">
                    <div class="et-empty" style="padding: 30px 12px;">
                        <div class="et-empty__icon" style="width:64px;height:64px;font-size:30px;">📭</div>
                        <p class="et-empty__title" style="font-size:16px;">Belum ada transaksi poin</p>
                        <p class="et-empty__text" style="font-size:12.5px;">Ikuti kuis atau selesaikan video challenge untuk mulai mengumpulkan poin! 🪙</p>
                    </div>
                </template>

                {{-- Daftar transaksi --}}
                <template x-if="!historyLoading && transactions.length > 0">
                    <div class="space-y-2.5">
                        <template x-for="(tx, i) in transactions" :key="'tx-' + i">
                            <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-black/30">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-lg dark:border-white/10 dark:bg-white/10"
                                      x-text="tx.type_icon" aria-hidden="true">🪙</span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="truncate text-[13px] font-extrabold text-slate-800 dark:text-slate-100" x-text="tx.type_label">Label</p>
                                        <span class="shrink-0 text-[13.5px] font-extrabold"
                                              :class="tx.amount >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                                              x-text="amountLabel(tx)">+50</span>
                                    </div>
                                    <p class="mt-0.5 break-words text-[12px] leading-snug text-slate-600 dark:text-slate-400" x-text="tx.description">Deskripsi transaksi</p>
                                    <div class="mt-1.5 flex flex-wrap items-center justify-between gap-x-2 gap-y-1 text-[10.5px] font-semibold text-slate-500 dark:text-slate-500">
                                        <span x-text="tx.created_at">baru saja</span>
                                        <span>Saldo: <b class="text-slate-600 dark:text-slate-300" x-text="fmt(tx.balance_after)">0</b></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="border-t-2 border-slate-100 px-5 py-3.5 text-right dark:border-slate-800">
                <button type="button"
                        @click="historyOpen = false"
                        class="et-press rounded-2xl border-2 border-slate-200 bg-white px-4 py-2 text-[13px] font-extrabold text-slate-600 transition hover:border-sky-400/60 hover:text-sky-700 dark:border-white/15 dark:bg-white/5 dark:text-slate-300 dark:hover:text-sky-300">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ================= BANNER NAIK TAHAP ================= --}}
    <div x-show="stageUp"
         x-cloak
         x-transition.opacity
         class="pointer-events-none fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/70 p-5 backdrop-blur-sm"
         role="alert" aria-live="assertive">
        <div class="et-stageup w-full max-w-sm rounded-[28px] border-4 border-amber-400 bg-gradient-to-br from-amber-200 via-yellow-100 to-lime-200 px-6 py-7 text-center shadow-2xl dark:border-amber-300 dark:from-amber-300 dark:via-yellow-200 dark:to-lime-300">
            <p class="text-[13px] font-black uppercase tracking-[0.24em] text-amber-700">✨ NAIK TAHAP! ✨</p>
            <p class="mt-3 text-5xl" x-text="stageUp ? stageUp.to_icon : ''">🧒</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-900" x-text="stageUp ? stageUp.to_label : ''">Remaja</p>
            <p class="mt-1 text-[13px] font-bold text-slate-700"
               x-text="stageUp ? stageUp.from_label + ' ➜ ' + stageUp.to_label : ''"></p>
            <p class="mt-3 text-[12.5px] font-semibold text-slate-600">Terus beri makan &amp; ikuti kuis biar cepat dewasa! 💪</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
    // ==== Dispatcher toast (mekanisme sama dengan dashboard → event `et-toast`) ====
    window.etToast = function (type, message) {
        var mine  = document.getElementById('et-shop-toasts');
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

    // ==== Halaman Toko (Alpine) ====
    function shopPage() {
        return {
            tab: 'tool',
            balance: {{ (int) $balance }},
            historyBalance: {{ (int) $balance }},
            tools: @json($toolsJson),
            foods: @json($foodsJson),
            inventory: @json($inventoryJson),

            buyingId: null,
            boughtFlash: {},
            usingId: null,
            usedFlash: null,
            shakeId: null,

            stageUp: null,

            historyOpen: false,
            historyLoading: false,
            transactions: [],

            _shakeTimer: null,
            _stageTimer: null,

            /* ============ Util ============ */
            get currentItems() {
                return this.tab === 'food' ? this.foods : this.tools;
            },

            fmt(n) {
                return new Intl.NumberFormat('id-ID').format(Number(n) || 0);
            },

            buyLabel(item) {
                if (this.buyingId === item.id) return '...';
                if (this.boughtFlash[item.id]) return '✓';
                return 'Beli';
            },

            useLabel(item) {
                if (this.usingId === item.id) return '...';
                if (this.usedFlash === item.id) return '✓';
                return '🍼 Pakai';
            },

            amountLabel(tx) {
                const amount = Number(tx.amount) || 0;
                return (amount >= 0 ? '+' : '-') + this.fmt(Math.abs(amount));
            },

            notify(type, message) {
                if (typeof window.etToast === 'function') {
                    window.etToast(type, message);
                } else {
                    window.dispatchEvent(new CustomEvent('et-toast', { detail: { type: type, message: message } }));
                }
            },

            async api(url, body = null) {
                const meta = document.querySelector('meta[name="csrf-token"]');
                return fetch(url, {
                    method: body === null ? 'GET' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': meta ? meta.content : '',
                    },
                    body: body === null ? undefined : JSON.stringify(body),
                });
            },

            confettiBurst(anchor) {
                if (!anchor) return;
                const fire = function () {
                    if (typeof window.confetti !== 'function') return;
                    const r = anchor.getBoundingClientRect();
                    window.confetti({
                        particleCount: 45,
                        spread: 60,
                        startVelocity: 28,
                        scalar: 0.8,
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
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js';
                s.onload = fire;
                document.head.appendChild(s);
            },

            /* ============ Saldo ============ */
            setBalance(value) {
                const next = Number(value) || 0;
                const prev = this.balance;
                this.balance = next;

                const nav  = document.getElementById('et-points-balance');
                const el   = document.getElementById('et-shop-balance');
                const coin = document.getElementById('et-shop-coin');

                if (nav) nav.textContent = this.fmt(next);

                if (coin) {
                    coin.classList.remove('et-coin-fly');
                    void coin.offsetWidth; // restart animasi
                    coin.classList.add('et-coin-fly');
                    setTimeout(() => coin.classList.remove('et-coin-fly'), 950);
                }

                if (!el) return;

                const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                if (prev === next || reduced) {
                    el.textContent = this.fmt(next);
                    return;
                }

                const start = performance.now();
                const duration = 700;
                const tick = (now) => {
                    const p = Math.min(1, (now - start) / duration);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = this.fmt(Math.round(prev + (next - prev) * eased));
                    if (p < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            },

            /* ============ Stok inventaris lokal ============ */
            applyStock(itemId, quantityLeft, fallbackDelta = 0) {
                let qty = Number(quantityLeft);
                if (isNaN(qty) || quantityLeft === undefined || quantityLeft === null) qty = -1;
                if (qty < 0) {
                    const found = this.inventory.find(i => i.id === itemId);
                    qty = (found ? found.quantity : 0) + fallbackDelta;
                }
                qty = Math.max(0, qty);

                const idx = this.inventory.findIndex(i => i.id === itemId);

                if (qty <= 0) {
                    if (idx >= 0) this.inventory.splice(idx, 1);
                    return;
                }

                if (idx >= 0) {
                    this.inventory[idx].quantity = qty;
                    return;
                }

                const src = this.tools.find(t => t.id === itemId) || this.foods.find(f => f.id === itemId) || {};
                this.inventory.push({
                    id: itemId,
                    name: src.name || 'Item',
                    icon: src.icon || '🎁',
                    category: src.category || 'food',
                    description: src.description || '',
                    effect_summary: src.effect_summary || '',
                    uses: src.uses || 1,
                    quantity: qty,
                });
            },

            shake(itemId) {
                this.shakeId = itemId;
                clearTimeout(this._shakeTimer);
                this._shakeTimer = setTimeout(() => { this.shakeId = null; }, 600);
            },

            /* ============ Beli item ============ */
            async buy(item, anchor) {
                if (this.buyingId) return;
                const button = anchor || null;
                this.buyingId = item.id;

                try {
                    const res = await this.api('{{ route("employee.shop.buy") }}', { item_id: item.id });
                    const data = await res.json();

                    if (!data.success) {
                        this.shake(item.id);
                        this.notify('error', data.message || 'Poin tidak cukup! Kumpulkan poin lagi dari kuis ya 🪙');
                        return;
                    }

                    this.setBalance(data.balance);
                    this.applyStock(item.id, data.quantity_left, item.uses || 1);

                    this.boughtFlash = Object.assign({}, this.boughtFlash, { [item.id]: true });
                    setTimeout(() => {
                        this.boughtFlash = Object.assign({}, this.boughtFlash, { [item.id]: false });
                    }, 1500);

                    this.confettiBurst(button);
                    this.notify('success', data.message || ('Berhasil membeli ' + item.name + '!'));
                } catch (e) {
                    console.error('Shop buy error:', e);
                    this.notify('error', 'Ups, ada gangguan. Coba lagi ya! 🙏');
                } finally {
                    this.buyingId = null;
                }
            },

            /* ============ Pakai makanan ============ */
            async useFood(item, anchor) {
                if (this.usingId) return;
                const button = anchor || null;
                this.usingId = item.id;

                try {
                    const res = await this.api('{{ route("employee.shop.use-food") }}', { item_id: item.id });
                    const data = await res.json();

                    if (!data.success) {
                        this.shake(item.id);
                        this.notify('error', data.message || 'Gagal memakai makanan. Coba lagi ya! 🙏');
                        return;
                    }

                    this.applyStock(item.id, data.quantity_left, -1);

                    this.usedFlash = item.id;
                    setTimeout(() => { this.usedFlash = null; }, 1500);

                    this.notify('success', data.message || (item.name + ' berhasil dipakai! +'+ (data.exp_gained || 0) + ' EXP'));

                    if (data.stage_up) {
                        this.showStageUp(data.stage_up);
                        this.confettiBurst(button);
                    }
                } catch (e) {
                    console.error('Shop use food error:', e);
                    this.notify('error', 'Ups, ada gangguan. Coba lagi ya! 🙏');
                } finally {
                    this.usingId = null;
                }
            },

            showStageUp(stage) {
                this.stageUp = stage;
                clearTimeout(this._stageTimer);
                this._stageTimer = setTimeout(() => { this.stageUp = null; }, 4000);
            },

            /* ============ Riwayat poin ============ */
            async openHistory() {
                this.historyOpen = true;
                this.historyLoading = true;

                try {
                    const res = await this.api('{{ route("employee.points.history") }}');
                    const data = await res.json();

                    if (!data.success) {
                        this.notify('error', 'Gagal memuat riwayat poin. Coba lagi ya! 🙏');
                        return;
                    }

                    this.transactions = Array.isArray(data.transactions) ? data.transactions : [];
                    this.historyBalance = Number(data.balance) || 0;
                    if (this.historyBalance !== this.balance) this.setBalance(this.historyBalance);
                } catch (e) {
                    console.error('Points history error:', e);
                    this.notify('error', 'Ups, ada gangguan saat memuat riwayat 🙏');
                } finally {
                    this.historyLoading = false;
                }
            },
        };
    }
</script>
@endpush
