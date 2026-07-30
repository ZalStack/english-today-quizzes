<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>English Today — Portal Evaluasi Bahasa Inggris KPM Bogor</title>
    <meta name="description" content="Platform ujian Bahasa Inggris mingguan resmi Klinik Pendidikan MIPA (KPM) Bogor.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink: #161758;
            --blue: #27438D;
            --cyan: #00A2E9;
            --marker: #FCC626;
            --correct: #2E7D3E;
            --alert: #EC1D1D;
            --paper: #FBFBF9;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--paper);
            color: #232342;
        }
        .font-display {
            font-family: 'Space Grotesk', sans-serif;
        }

        /* Blob animations */
        @keyframes blob-float {
            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }
            25% {
                transform: translate(30px, -30px) scale(1.05);
            }
            50% {
                transform: translate(-20px, 20px) scale(0.95);
            }
            75% {
                transform: translate(-30px, -20px) scale(1.02);
            }
        }
        @keyframes blob-float-slow {
            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(-40px, 25px) scale(1.06);
            }
            66% {
                transform: translate(35px, -35px) scale(0.94);
            }
        }
        @keyframes float-subtle {
            0%,
            100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-12px);
            }
        }
        .animate-blob {
            animation: blob-float 8s ease-in-out infinite;
        }
        .animate-blob-slow {
            animation: blob-float-slow 12s ease-in-out infinite;
        }
        .animate-float-subtle {
            animation: float-subtle 6s ease-in-out infinite;
        }

        /* Card hover effect */
        .feature-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 48px -12px rgba(22, 23, 88, 0.18);
        }
        .feature-card:hover .feature-icon {
            transform: scale(1.1);
            box-shadow: 0 8px 24px rgba(0, 162, 233, 0.25);
        }
        .feature-icon {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Stamp */
        .stamp {
            position: absolute;
            right: -1.2rem;
            bottom: -1.8rem;
            width: 7.5rem;
            height: 7.5rem;
            border-radius: 999px;
            border: 3px double var(--alert);
            color: var(--alert);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transform: rotate(11deg);
            background: rgba(255, 255, 255, 0.7);
            mix-blend-mode: multiply;
            animation: stamp-in 0.7s cubic-bezier(0.2, 1.4, 0.4, 1) 0.5s both;
            pointer-events: none;
            user-select: none;
        }
        .stamp span:first-child {
            font-family: 'Inter', sans-serif;
            font-size: 0.6rem;
            letter-spacing: 0.2em;
            font-weight: 700;
            text-transform: uppercase;
        }
        .stamp span:last-child {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.7rem;
            font-weight: 700;
            line-height: 1;
        }
        @keyframes stamp-in {
            from {
                opacity: 0;
                transform: rotate(28deg) scale(0.7);
            }
            to {
                opacity: 1;
                transform: rotate(11deg) scale(1);
            }
        }

        /* Option styles */
        .quiz-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1.5px solid #E4E4EE;
            border-radius: 0.7rem;
            padding: 0.65rem 0.9rem;
            font-size: 0.875rem;
            color: #3a3a55;
            transition: all 0.2s;
        }
        .quiz-option.correct {
            border-color: var(--correct);
            background: rgba(46, 125, 62, 0.07);
            color: var(--correct);
            font-weight: 600;
        }

        /* Smooth scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        /* FAQ accordion */
        .faq-details summary::-webkit-details-marker {
            display: none;
        }
        .faq-details summary {
            list-style: none;
            cursor: pointer;
            user-select: none;
        }
        .faq-details[open] .faq-chevron {
            transform: rotate(180deg);
        }
        .faq-chevron {
            transition: transform 0.3s ease;
        }
        .faq-details[open] .faq-answer {
            animation: faq-slide 0.35s ease-out;
        }
        @keyframes faq-slide {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .stamp,
            .feature-card,
            .animate-blob,
            .animate-blob-slow,
            .animate-float-subtle,
            .faq-details[open] .faq-answer {
                animation: none !important;
                transition: none !important;
            }
        }
        :focus-visible {
            outline: 2.5px solid var(--cyan);
            outline-offset: 3px;
            border-radius: 4px;
        }
    </style>
</head>
<body class="antialiased selection:bg-[#FCC626]/30 selection:text-[#161758]">

    <!-- ==================== NAVIGATION ==================== -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-[#FBFBF9]/80 backdrop-blur-xl border-b border-transparent" aria-label="Main navigation">
        <div class="max-w-7xl mx-auto px-5 sm:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#161758] to-[#27438D] flex items-center justify-center shadow-lg shadow-[#161758]/20">
                        <span class="font-display font-bold text-white text-sm tracking-tight">ET</span>
                    </div>
                    <div class="hidden sm:block">
                        <span class="font-display font-bold text-lg text-[#161758] tracking-tight">English Today</span>
                        <span class="hidden lg:inline-block text-xs text-[#3a3a55]/50 ml-2 font-medium">— KPM Bogor</span>
                    </div>
                </div>

                {{-- <!-- Nav Links (hidden mobile) -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="#fitur" class="px-4 py-2 text-sm font-medium text-[#3a3a55]/70 hover:text-[#161758] rounded-lg hover:bg-[#161758]/5 transition-all duration-200">Fitur</a>
                    <a href="#cara-kerja" class="px-4 py-2 text-sm font-medium text-[#3a3a55]/70 hover:text-[#161758] rounded-lg hover:bg-[#161758]/5 transition-all duration-200">Cara Kerja</a>
                    <a href="#faq" class="px-4 py-2 text-sm font-medium text-[#3a3a55]/70 hover:text-[#161758] rounded-lg hover:bg-[#161758]/5 transition-all duration-200">FAQ</a>
                </div> --}}

                <!-- CTA -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}"
                    class="group relative inline-flex items-center gap-2 px-5 py-2.5 bg-[#161758] text-white rounded-xl hover:bg-[#27438D] transition-all duration-300 font-semibold text-sm shadow-md shadow-[#161758]/15 hover:shadow-lg hover:shadow-[#27438D]/25 hover:-translate-y-0.5">
                    <span>Masuk</span>
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ==================== HERO SECTION ==================== -->
<section class="relative pt-28 pb-16 sm:pt-36 sm:pb-20 px-5 sm:px-8 overflow-hidden">
    <!-- Decorative blobs -->
    <div class="absolute -top-32 -right-32 w-[500px] h-[500px] rounded-full bg-gradient-to-br from-[#00A2E9]/8 to-[#27438D]/5 blur-3xl animate-blob pointer-events-none" aria-hidden="true"></div>
    <div class="absolute -bottom-40 -left-40 w-[450px] h-[450px] rounded-full bg-gradient-to-tr from-[#FCC626]/10 to-[#2E7D3E]/5 blur-3xl animate-blob-slow pointer-events-none" aria-hidden="true"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-gradient-to-b from-[#161758]/3 to-transparent blur-3xl pointer-events-none" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <!-- Left Content -->
            <div class="text-center lg:text-left">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#00A2E9]/10 border border-[#00A2E9]/20 text-[#146a99] text-xs font-semibold tracking-wide mb-6">
                    <span class="w-2 h-2 rounded-full bg-[#00A2E9] animate-pulse"></span>
                    PORTAL EVALUASI BAHASA INGGRIS MINGGUAN
                </div>

                <h1 class="font-display font-bold text-[#161758] text-4xl sm:text-5xl lg:text-6xl xl:text-7xl leading-[1.06] tracking-tight">
                    Welcome to <br class="hidden sm:block" />
                    <span class="relative inline-block">
                        English Today
                        <svg class="absolute -bottom-2 left-0 w-full h-3 sm:h-4 text-[#FCC626]" viewBox="0 0 200 20" fill="none" aria-hidden="true">
                            <path d="M0 12 Q 50 0, 100 12 Q 150 24, 200 12" stroke="currentColor" stroke-width="8" stroke-linecap="round" fill="none" opacity="0.7"/>
                        </svg>
                    </span>
                </h1>

                <p class="text-lg sm:text-xl text-[#3a3a55]/75 font-display font-medium mt-5">
                    Portal Evaluasi Bahasa Inggris Pegawai KPM Bogor
                </p>

                <p class="text-base sm:text-lg text-[#3a3a55]/65 leading-relaxed max-w-xl mx-auto lg:mx-0 mt-4">
                    Tingkatkan kemampuan Bahasa Inggris secara konsisten! Platform resmi KPM untuk mengukur dan mengevaluasi pemahaman Bahasa Inggris pegawai setiap minggunya.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row flex-wrap gap-4 mt-8 justify-center lg:justify-start">
                    <a href="{{ route('login') }}"
                    class="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-[#161758] text-white rounded-2xl hover:bg-[#27438D] transition-all duration-300 font-semibold text-base shadow-xl shadow-[#161758]/20 hover:shadow-2xl hover:shadow-[#27438D]/25 hover:-translate-y-1">
                    <span>Mulai Ujian Minggu Ini</span>
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="#fitur"
                class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-[#161758] rounded-2xl hover:bg-gray-50 transition-all duration-300 font-semibold text-base border-2 border-[#E4E4EE] hover:border-[#161758]/30 hover:-translate-y-1">
                Jelajahi Fitur
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </a>
        </div>

        <!-- Stats mini -->
        <div class="flex flex-wrap gap-6 sm:gap-10 mt-10 justify-center lg:justify-start text-center sm:text-left">
            <div>
                <p class="font-display font-bold text-3xl sm:text-4xl text-[#161758]">50+</p>
                <p class="text-xs sm:text-sm text-[#3a3a55]/60 font-medium">Soal per Minggu</p>
            </div>
            <div class="w-px h-12 bg-[#E4E4EE] hidden sm:block"></div>
            <div>
                <p class="font-display font-bold text-3xl sm:text-4xl text-[#27438D]">24/7</p>
                <p class="text-xs sm:text-sm text-[#3a3a55]/60 font-medium">Akses Ujian</p>
            </div>
            <div class="w-px h-12 bg-[#E4E4EE] hidden sm:block"></div>
            <div>
                <p class="font-display font-bold text-3xl sm:text-4xl text-[#00A2E9]">Instan</p>
                <p class="text-xs sm:text-sm text-[#3a3a55]/60 font-medium">Hasil & Progress</p>
            </div>
        </div>
    </div>

    <!-- Right: Quiz Card Preview -->
    <div class="relative flex justify-center lg:justify-end">
        <!-- Card shadow glow -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#161758]/15 via-[#00A2E9]/10 to-[#FCC626]/10 blur-2xl rounded-3xl scale-90" aria-hidden="true"></div>

        <div class="relative w-full max-w-sm bg-white rounded-2xl p-6 border border-black/5 shadow-2xl shadow-black/8 animate-float-subtle">
            <!-- Card header -->
            <div class="flex items-center justify-between mb-5">
                <span class="text-[11px] font-bold tracking-widest text-[#3a3a55]/40 uppercase">Soal #1</span>
                <span class="inline-flex items-center gap-1.5 font-mono font-semibold text-xs tracking-wide bg-[#FCC626]/25 text-[#7a5b00] px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#EC1D1D] animate-pulse"></span>
                    14:32
                </span>
            </div>

            <p class="font-display font-semibold text-[#161758] mb-5 leading-snug">
                The word <em class="not-italic bg-[#FCC626]/30 px-1 rounded">"ubiquitous"</em> in the text is closest in meaning to …
            </p>

            <!-- Options -->
            <div class="space-y-2.5">
                <div class="quiz-option correct">
                    <span>Everywhere</span>
                    <span class="text-[11px] opacity-70 font-semibold">✓</span>
                </div>
                <div class="quiz-option"><span>Rare</span></div>
                <div class="quiz-option"><span>Hidden</span></div>
                <div class="quiz-option"><span>Powerful</span></div>
            </div>

            <!-- Progress dots -->
            <div class="flex items-center justify-center gap-1.5 mt-5">
                <span class="w-2 h-2 rounded-full bg-[#161758]"></span>
                <span class="w-2 h-2 rounded-full bg-[#E4E4EE]"></span>
                <span class="w-2 h-2 rounded-full bg-[#E4E4EE]"></span>
                <span class="w-2 h-2 rounded-full bg-[#E4E4EE]"></span>
            </div>

            <!-- Stamp -->
            <div class="stamp">
                <span>RESMI</span>
                <span>KPM</span>
            </div>
        </div>
    </div>
</div>
</div>
</section>

{{-- <!-- ==================== FEATURES SECTION ==================== -->
<section id="fitur" class="py-20 sm:py-28 px-5 sm:px-8 bg-gradient-to-b from-transparent via-[#161758]/2 to-transparent">
    <div class="max-w-6xl mx-auto">
        <!-- Section header -->
        <div class="text-center mb-14 sm:mb-18">
            <span class="inline-block text-xs font-bold tracking-widest uppercase text-[#00A2E9] mb-3">KENAPA ENGLISH TODAY?</span>
            <h2 class="font-display font-bold text-[#161758] text-3xl sm:text-4xl lg:text-5xl tracking-tight">
                Dirancang Khusus untuk<br class="hidden sm:block" /> Evaluasi Berkala
            </h2>
            <p class="text-[#3a3a55]/60 text-base sm:text-lg max-w-2xl mx-auto mt-4">
                Platform ujian mingguan yang memudahkan pegawai KPM mengukur pemahaman Bahasa Inggris secara terstruktur.
            </p>
        </div>

        <!-- Feature cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            <!-- Card 1 -->
            <div class="feature-card group bg-white rounded-2xl border border-black/5 p-7 sm:p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#27438D] to-[#00A2E9] scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left" aria-hidden="true"></div>
                <div class="feature-icon w-16 h-16 rounded-2xl bg-gradient-to-br from-[#27438D]/10 to-[#27438D]/5 flex items-center justify-center mx-auto mb-6 shadow-sm">
                    <svg class="w-8 h-8 text-[#27438D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-display font-bold text-[#161758] text-lg mb-3">Weekly Quiz &amp; Test</h3>
                <p class="text-[#3a3a55]/65 leading-relaxed text-sm">
                    Akses soal-soal ujian Bahasa Inggris mingguan yang dirancang khusus sesuai standar KPM.
                </p>
            </div>

            <!-- Card 2 -->
            <div class="feature-card group bg-white rounded-2xl border border-black/5 p-7 sm:p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#00A2E9] to-[#2E7D3E] scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left" aria-hidden="true"></div>
                <div class="feature-icon w-16 h-16 rounded-2xl bg-gradient-to-br from-[#00A2E9]/10 to-[#00A2E9]/5 flex items-center justify-center mx-auto mb-6 shadow-sm">
                    <svg class="w-8 h-8 text-[#00A2E9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="font-display font-bold text-[#161758] text-lg mb-3">Real-Time Result &amp; Progress</h3>
                <p class="text-[#3a3a55]/65 leading-relaxed text-sm">
                    Dapatkan hasil nilai langsung setelah selesai mengerjakan ujian dan pantau perkembangan setiap minggu.
                </p>
            </div>

            <!-- Card 3 -->
            <div class="feature-card group bg-white rounded-2xl border border-black/5 p-7 sm:p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#2E7D3E] to-[#FCC626] scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left" aria-hidden="true"></div>
                <div class="feature-icon w-16 h-16 rounded-2xl bg-gradient-to-br from-[#2E7D3E]/10 to-[#2E7D3E]/5 flex items-center justify-center mx-auto mb-6 shadow-sm">
                    <svg class="w-8 h-8 text-[#2E7D3E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="font-display font-bold text-[#161758] text-lg mb-3">Terintegrasi Sistem KPM</h3>
                <p class="text-[#3a3a55]/65 leading-relaxed text-sm">
                    Terhubung dengan basis data pegawai KPM untuk pencatatan nilai dan evaluasi berkala.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== HOW IT WORKS ==================== -->
<section id="cara-kerja" class="py-20 sm:py-28 px-5 sm:px-8">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-14 sm:mb-18">
            <span class="inline-block text-xs font-bold tracking-widest uppercase text-[#2E7D3E] mb-3">PROSES MUDAH</span>
            <h2 class="font-display font-bold text-[#161758] text-3xl sm:text-4xl lg:text-5xl tracking-tight">
                Bagaimana Cara Mengikuti Ujian?
            </h2>
            <p class="text-[#3a3a55]/60 text-base sm:text-lg max-w-2xl mx-auto mt-4">
                Hanya beberapa langkah sederhana untuk memulai evaluasi mingguan Anda.
            </p>
        </div>

        <!-- Steps -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 sm:gap-6">
            <!-- Step 1 -->
            <div class="relative text-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#161758] to-[#27438D] flex items-center justify-center mx-auto mb-5 shadow-lg shadow-[#161758]/20 relative z-10">
                    <span class="font-display font-bold text-white text-2xl">1</span>
                </div>
                <h4 class="font-display font-bold text-[#161758] text-lg mb-2">Login Akun KPM</h4>
                <p class="text-[#3a3a55]/60 text-sm leading-relaxed">Gunakan kredensial pegawai KPM Anda untuk masuk ke portal ujian.</p>
                <!-- Connector line desktop -->
                <div class="hidden sm:block absolute top-8 left-[calc(50%+2rem)] w-[calc(100%-4rem)] h-0.5 bg-gradient-to-r from-[#E4E4EE] to-[#E4E4EE]/30 -z-0" aria-hidden="true"></div>
            </div>

            <!-- Step 2 -->
            <div class="relative text-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#27438D] to-[#00A2E9] flex items-center justify-center mx-auto mb-5 shadow-lg shadow-[#27438D]/20 relative z-10">
                    <span class="font-display font-bold text-white text-2xl">2</span>
                </div>
                <h4 class="font-display font-bold text-[#161758] text-lg mb-2">Kerjakan Soal</h4>
                <p class="text-[#3a3a55]/60 text-sm leading-relaxed">Jawab soal-soal pilihan ganda dalam batas waktu yang telah ditentukan.</p>
                <div class="hidden sm:block absolute top-8 left-[calc(50%+2rem)] w-[calc(100%-4rem)] h-0.5 bg-gradient-to-r from-[#E4E4EE] to-[#E4E4EE]/30 -z-0" aria-hidden="true"></div>
            </div>

            <!-- Step 3 -->
            <div class="relative text-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#00A2E9] to-[#2E7D3E] flex items-center justify-center mx-auto mb-5 shadow-lg shadow-[#00A2E9]/20 relative z-10">
                    <span class="font-display font-bold text-white text-2xl">3</span>
                </div>
                <h4 class="font-display font-bold text-[#161758] text-lg mb-2">Lihat Hasil &amp; Progress</h4>
                <p class="text-[#3a3a55]/60 text-sm leading-relaxed">Nilai langsung muncul. Pantau grafik perkembangan dari minggu ke minggu.</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== FAQ SECTION ==================== -->
<section id="faq" class="py-20 sm:py-28 px-5 sm:px-8 bg-gradient-to-b from-transparent via-[#FCC626]/5 to-transparent">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-14 sm:mb-18">
            <span class="inline-block text-xs font-bold tracking-widest uppercase text-[#FCC626] mb-3">PERTANYAAN UMUM</span>
            <h2 class="font-display font-bold text-[#161758] text-3xl sm:text-4xl lg:text-5xl tracking-tight">
                Hal yang Sering Ditanyakan
            </h2>
            <p class="text-[#3a3a55]/60 text-base sm:text-lg max-w-2xl mx-auto mt-4">
                Cari tahu lebih lanjut tentang portal English Today KPM Bogor.
            </p>
        </div>

        <!-- FAQ Accordion -->
        <div class="space-y-4">
            <details class="faq-details group bg-white rounded-2xl border border-black/5 shadow-sm hover:shadow-md transition-shadow duration-300" open>
                <summary class="flex items-center justify-between px-6 py-5 font-semibold text-[#161758] text-base sm:text-lg">
                    <span>Siapa yang bisa mengakses portal ini?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#3a3a55]/50 flex-shrink-0 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>
                <div class="faq-answer px-6 pb-5 text-[#3a3a55]/65 leading-relaxed text-sm sm:text-base border-t border-[#E4E4EE]/50 pt-4">
                    Portal English Today dikhususkan untuk seluruh pegawai Klinik Pendidikan MIPA (KPM) Bogor yang terdaftar dalam basis data kepegawaian.
                </div>
            </details>

            <details class="faq-details group bg-white rounded-2xl border border-black/5 shadow-sm hover:shadow-md transition-shadow duration-300">
                <summary class="flex items-center justify-between px-6 py-5 font-semibold text-[#161758] text-base sm:text-lg">
                    <span>Kapan ujian mingguan dilaksanakan?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#3a3a55]/50 flex-shrink-0 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>
                <div class="faq-answer px-6 pb-5 text-[#3a3a55]/65 leading-relaxed text-sm sm:text-base border-t border-[#E4E4EE]/50 pt-4">
                    Ujian tersedia setiap minggu dan dapat diakses kapan saja selama periode yang ditentukan. Setiap sesi memiliki batas waktu pengerjaan.
                </div>
            </details>

            <details class="faq-details group bg-white rounded-2xl border border-black/5 shadow-sm hover:shadow-md transition-shadow duration-300">
                <summary class="flex items-center justify-between px-6 py-5 font-semibold text-[#161758] text-base sm:text-lg">
                    <span>Bagaimana cara melihat progres nilai saya?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#3a3a55]/50 flex-shrink-0 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>
                <div class="faq-answer px-6 pb-5 text-[#3a3a55]/65 leading-relaxed text-sm sm:text-base border-t border-[#E4E4EE]/50 pt-4">
                    Setelah login, Anda dapat mengakses dashboard pribadi yang menampilkan riwayat nilai, grafik perkembangan, dan statistik performa dari waktu ke waktu.
                </div>
            </details>

            <details class="faq-details group bg-white rounded-2xl border border-black/5 shadow-sm hover:shadow-md transition-shadow duration-300">
                <summary class="flex items-center justify-between px-6 py-5 font-semibold text-[#161758] text-base sm:text-lg">
                    <span>Apakah ada batasan jumlah percobaan ujian?</span>
                    <svg class="faq-chevron w-5 h-5 text-[#3a3a55]/50 flex-shrink-0 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>
                <div class="faq-answer px-6 pb-5 text-[#3a3a55]/65 leading-relaxed text-sm sm:text-base border-t border-[#E4E4EE]/50 pt-4">
                    Setiap ujian mingguan hanya dapat dikerjakan satu kali per pegawai. Pastikan Anda siap sebelum memulai sesi ujian.
                </div>
            </details>
        </div>
    </div>
</section>

<!-- ==================== CTA SECTION ==================== -->
<section class="py-16 sm:py-24 px-5 sm:px-8">
    <div class="max-w-4xl mx-auto relative">
        <!-- Glow background -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#161758]/8 via-[#00A2E9]/8 to-[#27438D]/8 blur-3xl rounded-3xl" aria-hidden="true"></div>

        <div class="relative bg-white rounded-3xl border border-black/5 shadow-2xl shadow-black/5 p-8 sm:p-12 lg:p-16 text-center overflow-hidden">
            <!-- Decorative corner -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#FCC626]/20 to-transparent rounded-bl-3xl pointer-events-none" aria-hidden="true"></div>

            <h2 class="font-display font-bold text-[#161758] text-3xl sm:text-4xl lg:text-5xl tracking-tight">
                Siap Tingkatkan<br class="sm:hidden" /> Kemampuanmu?
            </h2>
            <p class="text-[#3a3a55]/65 text-base sm:text-lg max-w-xl mx-auto mt-4">
                Mulai ujian minggu ini dan lihat sejauh mana pemahaman Bahasa Inggris Anda berkembang.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 mt-8 justify-center">
                <a href="{{ route('login') }}"
                class="group inline-flex items-center justify-center gap-2 px-10 py-4 bg-[#161758] text-white rounded-2xl hover:bg-[#27438D] transition-all duration-300 font-semibold text-base shadow-xl shadow-[#161758]/20 hover:shadow-2xl hover:shadow-[#27438D]/25 hover:-translate-y-1">
                <span>Mulai Sekarang</span>
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <a href="#faq"
            class="inline-flex items-center justify-center gap-2 px-10 py-4 bg-gray-50 text-[#161758] rounded-2xl hover:bg-gray-100 transition-all duration-300 font-semibold text-base border-2 border-transparent hover:border-[#E4E4EE]">
            Baca FAQ
        </a>
    </div>
</div>
</div>
</section>

<!-- ==================== FOOTER ==================== -->
<footer class="bg-[#0F1040] text-white/70 pt-16 pb-8 px-5 sm:px-8">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <!-- Brand -->
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                        <span class="font-display font-bold text-white text-sm">ET</span>
                    </div>
                    <span class="font-display font-bold text-lg text-white">English Today</span>
                </div>
                <p class="text-white/50 text-sm leading-relaxed max-w-xs">
                    Portal resmi evaluasi Bahasa Inggris mingguan untuk pegawai Klinik Pendidikan MIPA (KPM) Bogor.
                </p>
            </div>

            <!-- Links -->
            <div>
                <h4 class="font-semibold text-white text-sm uppercase tracking-wider mb-4">Navigasi</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#fitur" class="hover:text-white transition-colors duration-200">Fitur</a></li>
                    <li><a href="#cara-kerja" class="hover:text-white transition-colors duration-200">Cara Kerja</a></li>
                    <li><a href="#faq" class="hover:text-white transition-colors duration-200">FAQ</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors duration-200">Masuk</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-semibold text-white text-sm uppercase tracking-wider mb-4">Kontak</h4>
                <ul class="space-y-2.5 text-sm">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>KPM Bogor, Jawa Barat</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>admin@kpmbogor.id</span>
                    </li>
                </ul>
            </div>

            <!-- Social / Extra -->
            <div>
                <h4 class="font-semibold text-white text-sm uppercase tracking-wider mb-4">KPM Bogor</h4>
                <p class="text-white/50 text-sm leading-relaxed">
                    Klinik Pendidikan MIPA — Lembaga pendidikan terpercaya di Bogor yang berkomitmen pada pengembangan SDM unggul.
                </p>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-white/10 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-white/40 text-xs sm:text-sm text-center sm:text-left">
                &copy; {{ date('Y') }} English Today — KPM Bogor. Seluruh hak cipta dilindungi.
            </p>
            <p class="text-white/30 text-xs text-center sm:text-right">
                Dibangun dengan ❤️ untuk evaluasi berkala pegawai KPM.
            </p>
        </div>
    </div>
</footer> --}}

<!-- ==================== SIMPLE NAV SCROLL SCRIPT ==================== -->
<script>
    // Add border to navbar on scroll
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            navbar.classList.add('border-black/5', 'shadow-sm');
            navbar.classList.remove('border-transparent');
        } else {
            navbar.classList.remove('border-black/5', 'shadow-sm');
            navbar.classList.add('border-transparent');
        }
    });

    // Smooth scroll for anchor links (fallback for older browsers)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>
</body>
</html>
