{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — ET-Quizzes</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:600,700,800|inter:400,500,600,700|jetbrains-mono:500,600&display=swap" rel="stylesheet" />
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

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--paper);
            color: #232342;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .font-display {
            font-family: 'Space Grotesk', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .ruled-bg {
            background-image: repeating-linear-gradient(to bottom,
                    transparent 0px, transparent 39px,
                    rgba(22, 23, 88, 0.05) 40px);
        }

        .field {
            width: 100%;
            border: 1.5px solid #E4E4EE;
            border-radius: 0.75rem;
            padding: 0.7rem 0.95rem;
            font-size: 1rem;
            color: #232342;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            -webkit-appearance: none;
            appearance: none;
        }
        .field:focus {
            outline: none;
            border-color: var(--cyan);
            box-shadow: 0 0 0 3px rgba(0, 162, 233, 0.15);
        }
        .field-error {
            border-color: var(--alert);
        }

        .stamp-mini {
            position: absolute;
            top: -0.75rem;
            right: 1rem;
            width: 4.5rem;
            height: 4.5rem;
            border-radius: 999px;
            border: 3px double var(--cyan);
            color: var(--cyan);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transform: rotate(9deg);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            mix-blend-mode: multiply;
            animation: stamp-in 0.6s cubic-bezier(0.2, 1.4, 0.4, 1) 0.3s both;
            z-index: 2;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }
        .stamp-mini span:first-child {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.45rem;
            letter-spacing: 0.15em;
            font-weight: 700;
            text-transform: uppercase;
        }
        .stamp-mini span:last-child {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.7rem;
            font-weight: 700;
        }

        @keyframes stamp-in {
            from {
                opacity: 0;
                transform: rotate(24deg) scale(0.7);
            }
            to {
                opacity: 1;
                transform: rotate(9deg) scale(1);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .stamp-mini {
                animation: none !important;
            }
        }

        :focus-visible {
            outline: 2px solid var(--cyan);
            outline-offset: 3px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            margin-inline: auto;
        }

        .field,
        button[type="submit"] {
            min-height: 48px;
        }

        .checkbox-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.25rem 0;
        }
        .checkbox-wrapper input[type="checkbox"] {
            width: 1.125rem;
            height: 1.125rem;
            flex-shrink: 0;
            accent-color: #161758;
            border-radius: 0.25rem;
            border: 1.5px solid #C5C5D8;
            transition: border-color 0.2s;
            cursor: pointer;
        }
        .checkbox-wrapper input[type="checkbox"]:checked {
            border-color: #161758;
        }
        .checkbox-wrapper input[type="checkbox"]:focus-visible {
            outline: 2px solid var(--cyan);
            outline-offset: 2px;
        }

        .wordmark {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }
        .wordmark .badge {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.6rem;
            background: #161758;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .wordmark .badge span {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
        }
        .wordmark .label {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: #161758;
            letter-spacing: -0.02em;
        }

        .password-toggle-btn {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0.25rem;
            color: #8a8aa0;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-toggle-btn:hover {
            color: #161758;
        }
        .password-toggle-btn:focus-visible {
            outline: 2px solid var(--cyan);
            outline-offset: 2px;
            border-radius: 0.25rem;
        }

        .password-input-wrapper {
            position: relative;
        }
        .password-input-wrapper .field {
            padding-right: 2.75rem;
        }

        .footer-note {
            text-align: center;
            font-size: 0.65rem;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.08em;
            color: rgba(58, 58, 85, 0.45);
            margin-top: 1.5rem;
            padding-inline: 0.5rem;
        }

        @media (max-width: 400px) {
            body {
                padding: 0.5rem;
            }
            .stamp-mini {
                width: 3.75rem;
                height: 3.75rem;
                top: -0.5rem;
                right: 0.75rem;
            }
            .stamp-mini span:first-child {
                font-size: 0.4rem;
            }
            .stamp-mini span:last-child {
                font-size: 0.6rem;
            }
            .field {
                font-size: 0.9rem;
                padding: 0.6rem 0.8rem;
            }
            .wordmark .badge {
                width: 2rem;
                height: 2rem;
            }
            .wordmark .badge span {
                font-size: 0.7rem;
            }
            .wordmark .label {
                font-size: 1rem;
            }
            .footer-note {
                font-size: 0.55rem;
                margin-top: 1.25rem;
            }
        }

        @media (min-width: 401px) and (max-width: 640px) {
            .stamp-mini {
                width: 4.25rem;
                height: 4.25rem;
                top: -0.6rem;
                right: 0.9rem;
            }
            .stamp-mini span:first-child {
                font-size: 0.4rem;
            }
            .stamp-mini span:last-child {
                font-size: 0.65rem;
            }
        }

        @media (min-width: 641px) and (max-width: 1024px) {
            .stamp-mini {
                width: 5rem;
                height: 5rem;
                top: -0.7rem;
                right: 1.2rem;
            }
            .stamp-mini span:first-child {
                font-size: 0.5rem;
            }
            .stamp-mini span:last-child {
                font-size: 0.75rem;
            }
        }

        @media (min-width: 1025px) {
            .stamp-mini {
                width: 5.5rem;
                height: 5.5rem;
                top: -1rem;
                right: 1.5rem;
            }
            .stamp-mini span:first-child {
                font-size: 0.5rem;
            }
            .stamp-mini span:last-child {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body class="antialiased ruled-bg">

    <div class="login-card">

        <!-- Wordmark -->
        <a href="{{ url('/') }}" class="wordmark justify-center mb-7">
            <div class="badge">
                <span>ET</span>
            </div>
            <span class="label">English Today KPM</span>
        </a>

        <!-- Card -->
        <div class="relative bg-white rounded-2xl shadow-xl border border-black/5 p-6 sm:p-8">

            <!-- Stamp -->
            <div class="stamp-mini" aria-hidden="true">
                <span>SECURE</span>
                <span>LOGIN</span>
            </div>

            <!-- Heading -->
            <h1 class="font-display font-bold text-[#161758] text-xl sm:text-2xl mb-1 pr-12 sm:pr-16">
                Welcome back
            </h1>
            <p class="text-sm text-[#3a3a55]/70 mb-6 sm:mb-7">
                Sign in to pick up your assessment.
            </p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-5 px-4 py-3 rounded-lg bg-[#2E7D3E]/10 border border-[#2E7D3E]/30 text-sm text-[#2E7D3E] font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-[#161758] mb-1.5">
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="field {{ $errors->has('email') ? 'field-error' : '' }}"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-[#EC1D1D] font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password with Toggle -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-[#161758] mb-1.5">
                        Password
                    </label>
                    <div class="password-input-wrapper">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="field {{ $errors->has('password') ? 'field-error' : '' }}"
                            placeholder="••••••••"
                        >
                        <button
                            type="button"
                            class="password-toggle-btn"
                            id="togglePasswordBtn"
                            aria-label="Toggle password visibility"
                            title="Toggle password visibility"
                        >
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-[#EC1D1D] font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                    <label class="checkbox-wrapper">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                        >
                        <span class="text-sm text-[#3a3a55]/80">Remember me</span>
                    </label>

                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[#27438D] hover:text-[#00A2E9] transition-colors">
                        Forgot password?
                    </a>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full py-3 bg-[#161758] text-white rounded-xl hover:bg-[#27438D] transition-colors duration-200 font-semibold text-base sm:text-lg"
                    style="min-height:52px;"
                >
                    Log in
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="footer-note">
            YOUR SCORE HISTORY IS SAVED TO YOUR PROFILE
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Update icon
                    if (type === 'text') {
                        eyeIcon.innerHTML = `
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        `;
                    } else {
                        eyeIcon.innerHTML = `
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        `;
                    }
                });
            }
        });
    </script>
</body>
</html>
