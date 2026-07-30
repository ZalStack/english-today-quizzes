<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Keamanan — ET-Quizzes</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:600,700,800|inter:400,500,600,700|jetbrains-mono:500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink: #161758; --blue: #27438D; --cyan: #00A2E9;
            --marker: #FCC626; --correct: #2E7D3E; --alert: #EC1D1D; --paper: #FBFBF9;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--paper); color: #232342;
            min-height: 100vh; min-height: 100dvh;
            display: flex; align-items: center; justify-content: center; padding: 1rem;
        }

        .font-display { font-family: 'Space Grotesk', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        .ruled-bg {
            background-image: repeating-linear-gradient(to bottom,
                    transparent 0px, transparent 39px, rgba(22, 23, 88, 0.05) 40px);
        }

        .field {
            width: 100%; border: 1.5px solid #E4E4EE; border-radius: 0.75rem;
            padding: 0.7rem 0.95rem; font-size: 1.1rem; text-align: center; font-weight: 700;
            color: #232342; background: #fff; transition: border-color 0.2s, box-shadow 0.2s;
            -webkit-appearance: none; appearance: none;
        }
        .field:focus { outline: none; border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(0, 162, 233, 0.15); }
        .field-error { border-color: var(--alert); }

        :focus-visible { outline: 2px solid var(--cyan); outline-offset: 3px; }

        .auth-card { width: 100%; max-width: 440px; margin-inline: auto; }
        .field, button[type="submit"] { min-height: 48px; }

        .wordmark { display: flex; align-items: center; gap: 0.6rem; text-decoration: none; }
        .wordmark .badge {
            width: 2.25rem; height: 2.25rem; border-radius: 0.6rem; background: #161758;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .wordmark .badge span { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 0.85rem; color: #fff; }
        .wordmark .label { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.15rem; color: #161758; letter-spacing: -0.02em; }

        .step-track { display: flex; align-items: center; gap: 0.4rem; margin-bottom: 1.5rem; }
        .step-dot { flex: 1; height: 4px; border-radius: 999px; background: #E4E4EE; }
        .step-dot.active { background: var(--cyan); }

        .equation-box {
            display: flex; align-items: center; justify-content: center; gap: 0.75rem;
            background: rgba(22, 23, 88, 0.04);
            border: 1.5px dashed #C5C5D8;
            border-radius: 0.75rem;
            padding: 1.25rem 1rem;
            margin-bottom: 1.5rem;
        }
        .equation-box .num, .equation-box .op {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--ink);
        }
        .equation-box .op { color: var(--cyan); }

        .footer-note {
            text-align: center; font-size: 0.65rem; font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.08em; color: rgba(58, 58, 85, 0.45); margin-top: 1.5rem; padding-inline: 0.5rem;
        }
    </style>
</head>
<body class="antialiased ruled-bg">

    <div class="auth-card">

        <a href="{{ url('/') }}" class="wordmark" style="justify-content:center; margin-bottom:1.75rem;">
            <div class="badge"><span>ET</span></div>
            <span class="label">ET-Quizzes</span>
        </a>

        <div class="relative bg-white rounded-2xl shadow-xl border border-black/5 p-6 sm:p-8">

            <!-- Step indicator: 2 dari 3 -->
            <div class="step-track" aria-hidden="true">
                <div class="step-dot active"></div>
                <div class="step-dot active"></div>
                <div class="step-dot"></div>
            </div>

            <h1 class="font-display font-bold text-[#161758] text-xl sm:text-2xl mb-1">
                Verifikasi keamanan
            </h1>
            <p class="text-sm text-[#3a3a55]/70 mb-6 sm:mb-7">
                Selesaikan operasi hitung berikut untuk melanjutkan.
            </p>

            @php
                $opSymbol = match($operation) {
                    '+' => '+',
                    '-' => '−',
                    '/' => '÷',
                    default => $operation,
                };
            @endphp

            <div class="equation-box">
                <span class="num">{{ $num_a }}</span>
                <span class="op">{{ $opSymbol }}</span>
                <span class="num">{{ $num_b }}</span>
                <span class="op">=</span>
                <span class="num">?</span>
            </div>

            <form method="POST" action="{{ route('password.captcha.verify') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="captcha_answer" class="block text-sm font-semibold text-[#161758] mb-1.5">
                        Jawaban Anda
                    </label>
                    <input
                        id="captcha_answer"
                        type="number"
                        name="captcha_answer"
                        required
                        autofocus
                        inputmode="numeric"
                        class="field {{ $errors->has('captcha_answer') ? 'field-error' : '' }}"
                        placeholder="0"
                    >
                    @error('captcha_answer')
                        <p class="mt-1.5 text-xs text-[#EC1D1D] font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#161758] text-white rounded-xl hover:bg-[#27438D] transition-colors duration-200 font-semibold text-base sm:text-lg"
                    style="min-height:52px;"
                >
                    Verifikasi
                </button>

                <p class="text-center text-sm text-[#3a3a55]/70">
                    <a href="{{ route('password.request') }}" class="font-semibold text-[#27438D] hover:text-[#00A2E9] transition-colors">
                        Ulangi dari awal
                    </a>
                </p>
            </form>
        </div>

        <p class="footer-note">LANGKAH 2 DARI 3 — VERIFIKASI KEAMANAN</p>
    </div>

</body>
</html>
