<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ET-Quizzes') }} - @yield('title', 'HR Management')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#f6f8fc]"
      x-data="{
          sidebarCollapsed: localStorage.getItem('hr-sidebar-collapsed') === '1',
          sidebarMobile: false
      }"
      @toggle-sidebar-mobile.window="sidebarMobile = !sidebarMobile"
      @toggle-sidebar-collapse.window="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('hr-sidebar-collapsed', sidebarCollapsed ? '1' : '0')"
      @keydown.escape.window="sidebarMobile = false">

    <div class="hr-layout" :class="{ 'hr-layout--collapsed': sidebarCollapsed }">

        {{-- Mobile Overlay --}}
        <div class="hr-overlay" x-show="sidebarMobile" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="sidebarMobile = false">
        </div>

        {{-- Sidebar --}}
        <div class="hr-layout__sidebar-wrapper"
             :class="{ 'hr-layout__sidebar-wrapper--mobile-open': sidebarMobile }">
            @include('layouts.partials.hr-sidebar')
        </div>

        {{-- Main Content --}}
        <div class="hr-layout__main">
            @include('layouts.partials.hr-header')

            <main class="hr-layout__content page-enter">

                @if(session('success'))
                    <div class="hr-alert hr-alert--success" x-data="{ show: true }" x-show="show" x-cloak x-transition.opacity.duration.400ms>
                        <div class="hr-alert__icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="hr-alert__body">
                            <p class="hr-alert__title">Berhasil</p>
                            <p class="hr-alert__text">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="hr-alert__close" @click="show = false" aria-label="Tutup">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="hr-alert hr-alert--error" x-data="{ show: true }" x-show="show" x-cloak x-transition.opacity.duration.400ms>
                        <div class="hr-alert__icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="hr-alert__body">
                            <p class="hr-alert__title">Terjadi Kesalahan</p>
                            <p class="hr-alert__text">{{ session('error') }}</p>
                        </div>
                        <button type="button" class="hr-alert__close" @click="show = false" aria-label="Tutup">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="hr-alert hr-alert--info" x-data="{ show: true }" x-show="show" x-cloak x-transition.opacity.duration.400ms>
                        <div class="hr-alert__icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="hr-alert__body">
                            <p class="hr-alert__title">Informasi</p>
                            <p class="hr-alert__text">{{ session('info') }}</p>
                        </div>
                        <button type="button" class="hr-alert__close" @click="show = false" aria-label="Tutup">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
