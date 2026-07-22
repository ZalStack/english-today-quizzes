<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>English Today — Portal Evaluasi Bahasa Inggris KPM Bogor</title>
    <meta name="description" content="Platform ujian Bahasa Inggris mingguan resmi Klinik Pendidikan MIPA (KPM) Bogor.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:500,600,700,800|inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            --ink:      #161758;
            --blue:     #27438D;
            --cyan:     #00A2E9;
            --marker:   #FCC626;
            --correct:  #2E7D3E;
            --alert:    #EC1D1D;
            --paper:    #FBFBF9;
        }
        body{
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--paper);
            color: #232342;
        }
        .font-display{ font-family:'Space Grotesk', sans-serif; }

        .ruled-bg{
            background-image: repeating-linear-gradient(
                to bottom,
                transparent 0px, transparent 39px,
                rgba(22,23,88,0.055) 40px
            );
        }

        .quiz-card{
            transform: rotate(-3deg);
            transition: transform .5s cubic-bezier(.16,1,.3,1);
        }
        .quiz-card:hover{ transform: rotate(-1deg) translateY(-4px); }

        .option{
            display:flex; align-items:center; justify-content:space-between;
            border:1.5px solid #E4E4EE; border-radius:.65rem;
            padding:.6rem .85rem; font-size:.875rem; color:#3a3a55;
        }
        .option-correct{
            border-color: var(--correct);
            background: rgba(46,125,62,.08);
            color: var(--correct);
            font-weight:600;
        }
        .timer-chip{
            font-family:'JetBrains Mono', monospace;
            font-weight:600; font-size:.75rem; letter-spacing:.03em;
            background: rgba(252,198,38,.22); color:#7a5b00;
            padding:.3rem .65rem; border-radius:999px;
        }

        .stamp{
            position:absolute; right:-1.1rem; bottom:-1.6rem;
            width:7.5rem; height:7.5rem; border-radius:999px;
            border:3px double var(--alert);
            color: var(--alert);
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            transform: rotate(11deg);
            background: rgba(255,255,255,.6);
            mix-blend-mode:multiply;
            animation: stamp-in .6s cubic-bezier(.2,1.4,.4,1) .5s both;
        }
        .stamp span:first-child{
            font-family:'JetBrains Mono', monospace;
            font-size:.6rem; letter-spacing:.18em; font-weight:700;
        }
        .stamp span:last-child{
            font-family:'Space Grotesk', sans-serif;
            font-size:1.7rem; font-weight:700; line-height:1;
        }
        @keyframes stamp-in{
            from{ opacity:0; transform: rotate(28deg) scale(.7); }
            to{ opacity:1; transform: rotate(11deg) scale(1); }
        }

        .feature-card{
            transition: all .3s cubic-bezier(.16,1,.3,1);
        }
        .feature-card:hover{
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(22,23,88,0.1);
        }

        @media (prefers-reduced-motion: reduce){
            .stamp, .quiz-card, .feature-card{ animation:none !important; transition:none !important; }
        }
        :focus-visible{ outline:2px solid var(--cyan); outline-offset:3px; }
    </style>
</head>
<body class="antialiased">

    <nav class="fixed top-0 left-0 right-0 z-50 bg-[#FBFBF9]/90 backdrop-blur-md border-b border-black/5">
        <div class="max-w-7xl mx-auto px-5 sm:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-[#161758] flex items-center justify-center">
                        <span class="font-display font-bold text-white text-sm">ET</span>
                    </div>
                    <span class="font-display font-bold text-lg text-[#161758]">English Today</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}"
                       class="px-5 py-2 bg-[#161758] text-white rounded-lg hover:bg-[#27438D] transition-colors duration-200 font-semibold text-sm">
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="ruled-bg pt-32 pb-16 px-5 sm:px-8">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#00A2E9]/10 text-[#146a99] text-xs font-semibold tracking-wide mb-6">
                    PORTAL EVALUASI BAHASA INGGRIS MINGGUAN
                </div>
                <h1 class="font-display font-bold text-[#161758] text-4xl sm:text-5xl lg:text-6xl leading-[1.08]">
                    Welcome to English Today!
                </h1>
                <p class="text-lg sm:text-xl text-[#3a3a55]/80 font-display font-semibold mt-4">
                    Portal Evaluasi Bahasa Inggris Pegawai KPM Bogor
                </p>
                <p class="text-base sm:text-lg text-[#3a3a55]/70 leading-relaxed max-w-xl mt-4">
                    Tingkatkan kemampuan Bahasa Inggris secara konsisten! Platform resmi KPM untuk mengukur dan mengevaluasi pemahaman Bahasa Inggris pegawai setiap minggunya.
                </p>
                <div class="flex flex-wrap gap-4 mt-8">
                    <a href="{{ route('login') }}"
                       class="px-8 py-3.5 bg-[#161758] text-white rounded-xl hover:bg-[#27438D] transition-colors duration-200 font-semibold text-center text-base">
                        Mulai Ujian Minggu Ini →
                    </a>
                </div>
            </div>
            <div class="relative flex justify-center">
                <div class="quiz-card w-full max-w-sm bg-white rounded-2xl p-6 border border-black/5 shadow-xl shadow-black/5">
                    <div class="flex items-center justify-between mb-5">
                        <span class="text-xs font-bold tracking-wider text-[#3a3a55]/40 uppercase">Soal #1</span>
                        <span class="timer-chip">⏱ 14:32</span>
                    </div>
                    <p class="font-display font-semibold text-[#161758] mb-5">
                        The word <em>"ubiquitous"</em> in the text is closest in meaning to …
                    </p>
                    <div class="space-y-2.5">
                        <div class="option option-correct"><span>Everywhere</span><span class="text-[10px] opacity-70">✓</span></div>
                        <div class="option"><span>Rare</span></div>
                        <div class="option"><span>Hidden</span></div>
                        <div class="option"><span>Powerful</span></div>
                    </div>
                    <div class="stamp">
                        <span>RESMI</span>
                        <span>KPM</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="pb-20 px-5 sm:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="feature-card bg-white rounded-2xl border border-black/5 p-7 text-center">
                    <div class="w-14 h-14 rounded-xl bg-[#27438D]/10 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-7 h-7 text-[#27438D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-[#161758] text-lg mb-2">Weekly Quiz & Test</h3>
                    <p class="text-[#3a3a55]/70 leading-relaxed text-sm">Akses soal-soal ujian Bahasa Inggris mingguan yang dirancang khusus sesuai standar KPM.</p>
                </div>

                <div class="feature-card bg-white rounded-2xl border border-black/5 p-7 text-center">
                    <div class="w-14 h-14 rounded-xl bg-[#00A2E9]/10 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-7 h-7 text-[#00A2E9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-[#161758] text-lg mb-2">Real-Time Result & Progress</h3>
                    <p class="text-[#3a3a55]/70 leading-relaxed text-sm">Dapatkan hasil nilai langsung setelah selesai mengerjakan ujian dan pantau perkembangan setiap minggu.</p>
                </div>

                <div class="feature-card bg-white rounded-2xl border border-black/5 p-7 text-center">
                    <div class="w-14 h-14 rounded-xl bg-[#2E7D3E]/10 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-7 h-7 text-[#2E7D3E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-[#161758] text-lg mb-2">Terintegrasi Sistem KPM</h3>
                    <p class="text-[#3a3a55]/70 leading-relaxed text-sm">Terhubung dengan basis data pegawai KPM untuk pencatatan nilai dan evaluasi berkala.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    {{-- <footer class="bg-[#0F1040] text-white/70">
        ...
    </footer> --}}

</body>
</html>