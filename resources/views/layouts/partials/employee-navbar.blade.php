{{-- Employee navigation: modern glass navbar (desktop pills + mobile sheet) --}}
@php
    $empNav = [
        ['label' => 'Dashboard', 'route' => 'employee.dashboard', 'active' => request()->routeIs('employee.dashboard'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/>'],
        ['label' => 'Join Quiz', 'route' => 'employee.quizzes.join', 'active' => request()->routeIs('employee.quizzes.join') || request()->routeIs('employee.quizzes.preview') || request()->routeIs('employee.quizzes.take') || request()->routeIs('employee.quizzes.result'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>'],
        ['label' => 'History', 'route' => 'employee.quizzes.history', 'active' => request()->routeIs('employee.quizzes.history'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        ['label' => 'Video Challenge', 'route' => 'employee.video-challenges.index', 'active' => request()->routeIs('employee.video-challenges.*'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>'],
        ['label' => 'Materi', 'route' => 'employee.materi.index', 'active' => request()->routeIs('employee.materi.*'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
        ['label' => '3D Pet', 'route' => 'employee.pet.index', 'active' => request()->routeIs('employee.pet.*'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 12.75c0 1.4.7 2.6 1.8 3.3l1.2.8c.9.6 2 1 3.1 1h2.6c1.1 0 2.2-.4 3.1-1l1.2-.8c1.1-.7 1.8-1.9 1.8-3.3 0-2.5-2.2-4.5-5-4.5-.7 0-1.4.1-2 .4-.6-.3-1.3-.4-2-.4-2.8 0-5 2-5 4.5z"/>'],
    ];

    $user = auth()->user();
    $initial = strtoupper(substr($user->full_name ?? $user->name ?? 'U', 0, 1));
@endphp

<script>
    window.etToggleTheme = function () {
        const isDark = document.documentElement.classList.toggle('dark');
        try { localStorage.setItem('et-theme', isDark ? 'dark' : 'light'); } catch (e) {}
    };
</script>

<header x-data="{ mobileOpen: false, userOpen: false }"
        @keydown.escape.window="mobileOpen = false; userOpen = false"
        class="sticky top-0 z-50 et-navbar">
    <div class="et-navbar__bar">
        <div class="et-navbar__inner">
            {{-- Brand --}}
            <a href="{{ route('employee.dashboard') }}" class="et-brand et-focus-ring" aria-label="English Today - Dashboard">
                <span class="et-brand__mark et-press" title="English Today">
                    <span class="text-[22px] leading-none" aria-hidden="true">🦉</span>
                </span>
                <span class="et-brand__text">
                    English <span class="et-gradient-text">Today</span>
                    <small>Quizzes</small>
                </span>
            </a>

            {{-- Desktop nav --}}
            <nav class="et-navbar__nav" aria-label="Menu utama">
                @foreach($empNav as $item)
                    <a href="{{ route($item['route']) }}"
                       @if($item['active']) aria-current="page" @endif
                       class="et-nav-link {{ $item['active'] ? 'et-nav-link--active' : '' }} et-focus-ring et-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- Right actions --}}
            <div class="et-navbar__actions">
                {{-- Points balance chip → Toko --}}
                <a href="{{ route('employee.shop.index') }}"
                   class="et-press et-focus-ring inline-flex items-center gap-1.5 h-9 sm:h-10 whitespace-nowrap rounded-full border border-amber-300 dark:border-amber-400/40 bg-amber-100 dark:bg-amber-500/15 px-2.5 sm:px-3.5 shadow-[0_3px_0_rgba(217,119,6,.35)] transition hover:bg-amber-200 dark:hover:bg-amber-500/25"
                   title="Saldo poin kamu — buka Toko 🪙"
                   aria-label="Saldo poin {{ number_format((int) ($user->points ?? 0), 0, ',', '.') }} — buka Toko">
                    <span class="text-[15px] leading-none" aria-hidden="true">🪙</span>
                    <span id="et-points-balance" class="text-[13px] font-extrabold leading-none text-amber-700 dark:text-amber-300">{{ number_format((int) ($user->points ?? 0), 0, ',', '.') }}</span>
                    <span class="hidden sm:inline text-[11px] font-bold leading-none text-amber-600/80 dark:text-amber-400/80">poin</span>
                </a>

                {{-- Theme toggle --}}
                <button type="button" onclick="etToggleTheme()" class="et-icon-btn et-focus-ring"
                        title="Ganti tema terang/gelap" aria-label="Ganti tema">
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                {{-- Notifications --}}
                @include('layouts.partials.notification-dropdown')

                {{-- User dropdown (desktop) --}}
                <div class="hidden sm:block relative" @click.outside="userOpen = false">
                    <button type="button" @click="userOpen = !userOpen" class="et-user-chip et-focus-ring" :aria-expanded="userOpen">
                        <span class="et-user-chip__avatar">{{ $initial }}</span>
                        <span class="et-user-chip__meta">
                            <span class="et-user-chip__name">{{ $user->full_name ?? $user->name }}</span>
                            <span class="et-user-chip__role">{{ ucfirst($user->role) }}</span>
                        </span>
                        <svg class="w-4 h-4 et-user-chip__chevron" :class="{ 'rotate-180': userOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="userOpen" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="et-dropdown">
                        <div class="et-dropdown__head">
                            <p class="et-dropdown__name">{{ $user->full_name ?? $user->name }}</p>
                            <p class="et-dropdown__email">{{ $user->email }}</p>
                            @if($user->division)
                                <span class="et-badge et-badge-info mt-2">{{ $user->division->name }}</span>
                            @endif
                        </div>
                        <a href="{{ route('employee.profile.edit') }}" class="et-dropdown__item {{ request()->routeIs('employee.profile.*') ? 'et-dropdown__item--active' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Saya
                        </a>
                        <a href="{{ route('notifications.index') }}" class="et-dropdown__item {{ request()->routeIs('notifications.*') ? 'et-dropdown__item--active' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            Notifikasi
                        </a>
                        <div class="et-dropdown__divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="et-dropdown__item et-dropdown__item--danger">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Mobile hamburger --}}
                <button type="button" class="et-icon-btn et-navbar__burger et-focus-ring"
                        @click="mobileOpen = !mobileOpen"
                        :aria-expanded="mobileOpen" aria-label="Buka menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': mobileOpen, 'block': !mobileOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{ 'block': mobileOpen, 'hidden': !mobileOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Progress shimmer line at very bottom of navbar --}}
        <div class="et-navbar__glow"></div>
    </div>

    {{-- Mobile sheet --}}
    <div x-show="mobileOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0 -translate-y-3"
         class="et-mobile-sheet">
        <div class="et-mobile-sheet__inner">
            <div class="grid grid-cols-2 gap-2.5">
                @foreach($empNav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="et-mobile-card {{ $item['active'] ? 'et-mobile-card--active' : '' }} et-press">
                        <span class="et-mobile-card__icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                        </span>
                        <span class="et-mobile-card__label">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-800 space-y-1">
                <a href="{{ route('employee.profile.edit') }}" class="et-mobile-row {{ request()->routeIs('employee.profile.*') ? 'et-mobile-row--active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil & Keamanan
                </a>
                <a href="{{ route('notifications.index') }}" class="et-mobile-row {{ request()->routeIs('notifications.*') ? 'et-mobile-row--active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifikasi
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="et-mobile-row et-mobile-row--danger w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar dari akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
