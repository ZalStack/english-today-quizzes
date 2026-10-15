<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#58cc02">
    <title>{{ config('app.name', 'ET-Quizzes') }} - @yield('title', 'English Today Quizzes')</title>

    {{-- Apply saved theme before paint (prevents flash) --}}
    <script>
        (function () {
            try {
                var t = localStorage.getItem('et-theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=baloo-2:500,600,700,800|nunito:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        /* Top loading bar on page load */
        .et-loadbar {
            position: fixed;
            top: 0; left: 0;
            height: 3px;
            width: 0;
            z-index: 90;
            background-image: linear-gradient(90deg, #58cc02, #1cb0f6, #ffc800);
            animation: etLoadbar 1.1s cubic-bezier(.2,.7,.3,1) forwards;
            box-shadow: 0 0 12px rgba(88, 204, 2, .7);
            pointer-events: none;
        }
        @keyframes etLoadbar {
            0% { width: 0; opacity: 1; }
            60% { width: 72%; opacity: 1; }
            100% { width: 100%; opacity: 0; }
        }

        /* Keep sticky offsets consistent for in-page sticky headers (quiz timer etc.) */
        .et-sticky-offset { top: 77px; }
        @media (max-width: 1023px) { .et-sticky-offset { top: 69px; } }
    </style>
</head>

<body class="et-playful font-sans antialiased bg-white dark:bg-[#131f26] text-[#3c3c3c] dark:text-[#f4f7f8] selection:bg-lime-100 dark:selection:bg-lime-500/30">
    <div class="et-loadbar" aria-hidden="true"></div>

    <div class="min-h-screen flex flex-col">
        @include('layouts.partials.employee-navbar')

        {{-- Flash messages as modern toasts --}}
        @php
            $etToasts = array_values(array_filter([
                session('success') ? ['type' => 'success', 'message' => session('success')] : null,
                session('error') ? ['type' => 'error', 'message' => session('error')] : null,
                session('info') ? ['type' => 'info', 'message' => session('info')] : null,
            ]));
        @endphp

        @if(count($etToasts))
            <div x-data="etToasts()" x-init="boot(@json($etToasts))"
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
        @endif

        {{-- Page content --}}
        <main class="flex-1 w-full">
            @yield('content')
        </main>

        {{-- Footer (hidden on focus-mode pages like quiz taking) --}}
        @hasSection('hide_footer')
        @else
            @include('layouts.partials.employee-footer')
        @endif
    </div>

    {{-- Navbar scroll state --}}
    <script>
        (function () {
            var onScroll = function () {
                var bar = document.querySelector('.et-navbar');
                if (bar) bar.classList.toggle('is-scrolled', window.scrollY > 8);
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        })();
    </script>

    @stack('scripts')

    <script>
        function etToasts() {
            return {
                items: [],
                seq: 0,
                boot(list) {
                    (list || []).forEach(t => this.push(t));
                },
                push(toast) {
                    const id = ++this.seq;
                    this.items.push(Object.assign({ id }, toast));
                    setTimeout(() => this.dismiss(id), 6000);
                },
                dismiss(id) {
                    this.items = this.items.filter(t => t.id !== id);
                },
                icon(type) {
                    if (type === 'success') return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
                    if (type === 'error') return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                    return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                }
            };
        }
    </script>
</body>
</html>
