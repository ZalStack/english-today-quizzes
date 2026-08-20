<header class="hr-header" x-data="{ userDropdown: false }" @click.outside="userDropdown = false; $dispatch('close-notification-dropdown')">
    <div class="hr-header__inner">
        {{-- Left: Mobile menu toggle + Page title --}}
        <div class="hr-header__left">
            <button @click="$dispatch('toggle-sidebar')" class="hr-header__menu-btn lg:hidden" aria-label="Toggle sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <button @click="$dispatch('toggle-sidebar')" class="hr-header__menu-btn hidden lg:flex" aria-label="Toggle sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="hr-header__title">
                <h1 class="hr-header__title-text">@yield('header-title', 'Dashboard')</h1>
                @hasSection('header-subtitle')
                    <p class="hr-header__subtitle">@yield('header-subtitle')</p>
                @endif
            </div>
        </div>

        {{-- Right: Actions --}}
        <div class="hr-header__right">
            {{-- Notifications --}}
            @include('layouts.partials.notification-dropdown')

            {{-- User Dropdown --}}
            <div class="hr-header__user" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="hr-header__user-btn">
                    <div class="hr-header__avatar">
                        {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hr-header__user-info">
                        <span class="hr-header__user-name">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                        <span class="hr-header__user-role">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                    <svg class="hr-header__chevron" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="hr-header__dropdown">
                    <div class="hr-header__dropdown-header">
                        <p class="hr-header__dropdown-name">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
                        <p class="hr-header__dropdown-email">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('hr.profile.edit') }}" class="hr-header__dropdown-item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile Settings
                    </a>
                    <div class="hr-header__dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hr-header__dropdown-item hr-header__dropdown-item--danger">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
