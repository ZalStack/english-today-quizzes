@php
    $sidebarItems = [
        ['route' => 'hr.dashboard', 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
        ['route' => 'hr.employees.index', 'active' => 'hr.employees.*', 'label' => 'Employees', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>'],
        ['route' => 'hr.divisions.index', 'active' => 'hr.divisions.*', 'label' => 'Divisions', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
        ['route' => 'hr.categories.index', 'active' => 'hr.categories.*', 'label' => 'Categories', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>'],
        ['route' => 'hr.quizzes.index', 'active' => 'hr.quizzes.*', 'label' => 'Quizzes', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'],
        ['route' => 'hr.reports.index', 'active' => 'hr.reports.*', 'label' => 'Reports', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
        ['route' => 'hr.video-challenges.index', 'active' => 'hr.video-challenges.*', 'label' => 'Video Challenges', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>'],
    ];
@endphp

<aside class="hr-sidebar" :class="{ 'hr-sidebar--collapsed': sidebarCollapsed }" x-data="{ open: false }">
    <div class="hr-sidebar__inner">
        {{-- Logo --}}
        <div class="hr-sidebar__logo">
            <a href="{{ route('hr.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                <div class="hr-sidebar__logo-icon">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="hr-sidebar__logo-text">ET-Quizzes</span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="hr-sidebar__nav">
            <div class="hr-sidebar__nav-label">Main Menu</div>
            @foreach($sidebarItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="hr-sidebar__link {{ request()->routeIs($item['active'] ?? $item['route']) ? 'hr-sidebar__link--active' : '' }}"
                   title="{{ $item['label'] }}">
                    <svg class="hr-sidebar__link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                    <span class="hr-sidebar__link-text">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Bottom Section --}}
        <div class="hr-sidebar__bottom">
            <div class="hr-sidebar__divider"></div>
            <a href="{{ route('hr.profile.edit') }}"
               class="hr-sidebar__link {{ request()->routeIs('hr.profile.*') ? 'hr-sidebar__link--active' : '' }}"
               title="Profile Settings">
                <svg class="hr-sidebar__link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="hr-sidebar__link-text">Settings</span>
            </a>

            <div class="hr-sidebar__user">
                <div class="hr-sidebar__user-avatar">
                    {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->name, 0, 1)) }}
                </div>
                <div class="hr-sidebar__user-info">
                    <p class="hr-sidebar__user-name">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
                    <p class="hr-sidebar__user-role">HR Administrator</p>
                </div>
            </div>
        </div>
    </div>
</aside>
