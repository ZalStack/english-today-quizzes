<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ET-Quizzes — English Assessment for Your Team</title>
    <meta name="description" content="Timed English quizzes, instant scoring, and a record HR can actually use. Built for workplace proficiency assessment.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:500,600,700,800|inter:400,500,600,700,800|jetbrains-mono:400,500,600,700&display=swap" rel="stylesheet" />
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
        .font-mono{ font-family:'JetBrains Mono', monospace; }

        /* ---------- Hero background: faint ruled-paper lines, not a gradient blob ---------- */
        .ruled-bg{
            background-image: repeating-linear-gradient(
                to bottom,
                transparent 0px, transparent 39px,
                rgba(22,23,88,0.055) 40px
            );
        }

        /* ---------- Quiz card mockup ---------- */
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

        /* ---------- Ink stamp: the signature element ---------- */
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

        /* ---------- Step numerals (a real 1-2-3 sequence) ---------- */
        .step-num{
            font-family:'JetBrains Mono', monospace;
            font-weight:700; font-size:.9rem; color: var(--cyan);
            letter-spacing:.05em;
        }

        /* ---------- CTA band texture ---------- */
        .cta-band{ position:relative; overflow:hidden; background: var(--ink); }
        .cta-band::before{
            content:"";
            position:absolute; top:-4rem; right:-4rem;
            width:22rem; height:22rem; border-radius:999px;
            border:3px double rgba(255,255,255,.14);
        }

        @media (prefers-reduced-motion: reduce){
            .stamp, .quiz-card{ animation:none !important; transition:none !important; }
        }

        :focus-visible{ outline:2px solid var(--cyan); outline-offset:3px; }
    </style>
</head>
<body class="antialiased">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-[#FBFBF9]/90 backdrop-blur-md border-b border-black/5">
        <div class="max-w-7xl mx-auto px-5 sm:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-[#161758] flex items-center justify-center">
                        <span class="font-display font-bold text-white text-sm">ET</span>
                    </div>
                    <span class="font-display font-bold text-lg text-[#161758]">ET-Quizzes</span>
                </div>
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-5 py-2 bg-[#161758] text-white rounded-lg hover:bg-[#27438D] transition-colors duration-200 font-semibold text-sm">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-5 py-2 bg-[#161758] text-white rounded-lg hover:bg-[#27438D] transition-colors duration-200 font-semibold text-sm">
                        Sign in
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="ruled-bg pt-32 pb-20 px-5 sm:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 lg:gap-10 items-center">

                <div class="max-w-xl">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#00A2E9]/10 text-[#146a99] text-xs font-mono font-semibold tracking-wide">
                        WORKPLACE ENGLISH ASSESSMENT
                    </span>
                    <h1 class="font-display font-bold text-[#161758] text-4xl sm:text-5xl lg:text-6xl leading-[1.08] mt-6">
                        Know exactly where your team's English stands.
                    </h1>
                    <p class="text-lg text-[#3a3a55]/80 leading-relaxed mt-6">
                        ET-Quizzes turns proficiency testing into a five-minute check-in — timed
                        questions, grading the instant an employee submits, and a clean record
                        HR can pull up without chasing anyone for a paper score sheet.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 mt-9">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="px-7 py-3.5 bg-[#161758] text-white rounded-xl hover:bg-[#27438D] transition-colors duration-200 font-semibold text-center">
                                Go to dashboard →
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="px-7 py-3.5 bg-[#161758] text-white rounded-xl hover:bg-[#27438D] transition-colors duration-200 font-semibold text-center">
                                Sign in to start →
                            </a>
                        @endauth
                        <a href="#how-it-works"
                           class="px-7 py-3.5 border-2 border-[#161758]/15 text-[#161758] rounded-xl hover:border-[#161758]/40 transition-colors duration-200 font-semibold text-center">
                            See how scoring works
                        </a>
                    </div>
                </div>

                <!-- Signature hero visual: an actual quiz in progress, not a gradient blob -->
                <div class="relative mx-auto w-full max-w-sm mt-4 lg:mt-0">
                    <div class="quiz-card relative rounded-2xl bg-white shadow-xl border border-black/5 p-6">
                        <div class="flex items-center justify-between mb-5">
                            <span class="font-mono text-[11px] tracking-wider text-[#27438D] font-semibold">QUESTION 4 / 10</span>
                            <span class="timer-chip">⏱ 00:42</span>
                        </div>
                        <p class="font-semibold text-[#161758] mb-4 leading-snug">
                            Choose the correct form: "By next year, she ___ here for a decade."
                        </p>
                        <ul class="space-y-2">
                            <li class="option">will have worked</li>
                            <li class="option option-correct">
                                will have been working
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </li>
                            <li class="option">has worked</li>
                            <li class="option">was working</li>
                        </ul>
                    </div>
                    <div class="stamp" aria-hidden="true">
                        <span>SCORED</span>
                        <span>92%</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- How it works: a genuine 3-step sequence -->
    <section id="how-it-works" class="py-20 px-5 sm:px-8 border-t border-black/5">
        <div class="max-w-6xl mx-auto">
            <p class="font-mono text-xs tracking-widest text-[#00A2E9] font-semibold mb-3">HOW IT WORKS</p>
            <h2 class="font-display font-bold text-[#161758] text-3xl sm:text-4xl mb-12 max-w-2xl">
                From "assigned" to "on record" in one sitting.
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <p class="step-num mb-3">01</p>
                    <h3 class="font-display font-bold text-[#161758] text-xl mb-2">Take the quiz, on the clock</h3>
                    <p class="text-[#3a3a55]/80 leading-relaxed">HR assigns the quiz and the timer starts the moment the employee opens it — one sitting, no re-runs.</p>
                </div>
                <div>
                    <p class="step-num mb-3">02</p>
                    <h3 class="font-display font-bold text-[#161758] text-xl mb-2">Graded on submit</h3>
                    <p class="text-[#3a3a55]/80 leading-relaxed">MCQ, true/false, and short answer score themselves the second the employee hits submit. No queue, no waiting on a marker.</p>
                </div>
                <div>
                    <p class="step-num mb-3">03</p>
                    <h3 class="font-display font-bold text-[#161758] text-xl mb-2">Lands on the record</h3>
                    <p class="text-[#3a3a55]/80 leading-relaxed">The score attaches straight to the employee's profile, ready for HR to review or export — no spreadsheet stitching.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-20 px-5 sm:px-8 bg-white border-t border-black/5">
        <div class="max-w-7xl mx-auto">
            <p class="font-mono text-xs tracking-widest text-[#00A2E9] font-semibold mb-3">BUILT FOR THE WHOLE TEAM</p>
            <h2 class="font-display font-bold text-[#161758] text-3xl sm:text-4xl mb-12 max-w-2xl">
                Everything an assessment actually needs — nothing it doesn't.
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <div class="p-7 rounded-2xl border border-black/5 hover:border-[#00A2E9]/40 hover:shadow-lg transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#27438D]/10 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-[#27438D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-[#161758] text-lg mb-2">Every question type you need</h3>
                    <p class="text-[#3a3a55]/80 leading-relaxed">Multiple choice, true/false, short answer, and essay — mix them in the same quiz.</p>
                </div>

                <div class="p-7 rounded-2xl border border-black/5 hover:border-[#00A2E9]/40 hover:shadow-lg transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#00A2E9]/10 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-[#00A2E9]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-[#161758] text-lg mb-2">Scores that mean something</h3>
                    <p class="text-[#3a3a55]/80 leading-relaxed">Track results over time per employee, spot who's stalling, and see it before a review meeting does.</p>
                </div>

                <div class="p-7 rounded-2xl border border-black/5 hover:border-[#00A2E9]/40 hover:shadow-lg transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#161758]/10 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-[#161758]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-[#161758] text-lg mb-2">Locked behind an enrollment key</h3>
                    <p class="text-[#3a3a55]/80 leading-relaxed">Only employees with the right key get in — no stray links floating around the office chat.</p>
                </div>

                <div class="p-7 rounded-2xl border border-black/5 hover:border-[#00A2E9]/40 hover:shadow-lg transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#2E7D3E]/10 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-[#2E7D3E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-[#161758] text-lg mb-2">The clock is part of the test</h3>
                    <p class="text-[#3a3a55]/80 leading-relaxed">Set a time limit and it auto-submits when it runs out — every employee gets the same pressure.</p>
                </div>

                <div class="p-7 rounded-2xl border border-black/5 hover:border-[#00A2E9]/40 hover:shadow-lg transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#FCC626]/20 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-[#7a5b00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-[#161758] text-lg mb-2">Works on whatever's in your pocket</h3>
                    <p class="text-[#3a3a55]/80 leading-relaxed">Phone, tablet, or the desktop at the front desk — same quiz, same layout, no zooming around.</p>
                </div>

                <div class="p-7 rounded-2xl border border-black/5 hover:border-[#00A2E9]/40 hover:shadow-lg transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#EC1D1D]/10 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-[#EC1D1D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-[#161758] text-lg mb-2">Skip the retyping</h3>
                    <p class="text-[#3a3a55]/80 leading-relaxed">Pull questions straight out of an existing PDF instead of typing every option in by hand.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-band py-24 px-5 sm:px-8">
        <div class="max-w-3xl mx-auto text-center relative">
            <h2 class="font-display font-bold text-white text-3xl sm:text-4xl lg:text-5xl leading-tight mb-5">
                Put your team's English on the record.
            </h2>
            <p class="text-white/70 text-lg mb-9">
                No more chasing a paper score sheet across departments.
            </p>
            @auth
                <a href="{{ route('dashboard') }}"
                   class="inline-block px-9 py-4 bg-white text-[#161758] rounded-xl hover:bg-[#FBFBF9] transition-colors duration-200 font-bold text-lg">
                    Go to dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-block px-9 py-4 bg-white text-[#161758] rounded-xl hover:bg-[#FBFBF9] transition-colors duration-200 font-bold text-lg">
                    Sign in to start
                </a>
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#0F1040] text-white/70 py-14 px-5 sm:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                            <span class="font-display font-bold text-white text-xs">ET</span>
                        </div>
                        <span class="font-display font-bold text-white text-lg">ET-Quizzes</span>
                    </div>
                    <p class="text-sm leading-relaxed max-w-xs">English proficiency assessment for the workplace — timed, graded, and on the record.</p>
                </div>
                <div>
                    <h4 class="text-xs font-mono font-semibold tracking-widest text-white/50 mb-4">PLATFORM</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#how-it-works" class="hover:text-white transition-colors">How it works</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Question types</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign in</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-mono font-semibold tracking-widest text-white/50 mb-4">SUPPORT</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Contact HR</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Report an issue</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 mt-12 pt-8 text-center text-xs text-white/40">
                <p>&copy; {{ date('Y') }} ET-Quizzes. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
