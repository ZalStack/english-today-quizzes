{{-- resources/views/auth/reset-password-new.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Baru — ET-Quizzes</title>

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
            padding: 0.7rem 0.95rem; font-size: 1rem; color: #232342; background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            -webkit-appearance: none; appearance: none;
        }
        .field:focus { outline: none; border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(0, 162, 233, 0.15); }
        .field-error { border-color: var(--alert); }

        :focus-visible { outline: 2px solid var(--cyan); outline-offset: 3px; }

        .auth-card { width: 100%; max-width: 440px; margin-inline: auto; }
        .field, button[type="submit"] { min-height: 48px; }

        .wordmark {
            display: flex; align-items: center; gap: 0.6rem; text-decoration: none;
            justify-content: center; margin-bottom: 1.75rem;
        }
        .wordmark .badge {
            width: 2.25rem; height: 2.25rem; border-radius: 0.6rem; background: #161758;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .wordmark .badge span { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 0.85rem; color: #fff; }
        .wordmark .label { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.15rem; color: #161758; letter-spacing: -0.02em; }

        .step-track { display: flex; align-items: center; gap: 0.4rem; margin-bottom: 1.5rem; }
        .step-dot { flex: 1; height: 4px; border-radius: 999px; background: #E4E4EE; }
        .step-dot.active { background: var(--cyan); }

        .footer-note {
            text-align: center; font-size: 0.65rem; font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.08em; color: rgba(58, 58, 85, 0.45); margin-top: 1.5rem; padding-inline: 0.5rem;
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

        @media (max-width: 400px) {
            body { padding: 0.5rem; }
            .wordmark .badge { width: 2rem; height: 2rem; }
            .wordmark .badge span { font-size: 0.7rem; }
            .wordmark .label { font-size: 1rem; }
            .field { font-size: 0.9rem; padding: 0.6rem 0.8rem; }
            .footer-note { font-size: 0.55rem; margin-top: 1.25rem; }
            .password-input-wrapper .field { padding-right: 2.5rem; }
        }
    </style>
</head>
<body class="antialiased ruled-bg">

    <div class="auth-card">

        <a href="{{ url('/') }}" class="wordmark">
            <div class="badge"><span>ET</span></div>
            <span class="label">ET-Quizzes</span>
        </a>

        <div class="relative bg-white rounded-2xl shadow-xl border border-black/5 p-6 sm:p-8">

            <!-- Step indicator: 3 dari 3 -->
            <div class="step-track" aria-hidden="true">
                <div class="step-dot active"></div>
                <div class="step-dot active"></div>
                <div class="step-dot active"></div>
            </div>

            <h1 class="font-display font-bold text-[#161758] text-xl sm:text-2xl mb-1">
                Buat password baru
            </h1>
            <p class="text-sm text-[#3a3a55]/70 mb-6 sm:mb-7">
                Verifikasi berhasil. Silakan masukkan password baru Anda.
            </p>

            @if ($errors->any())
                <div class="mb-5 px-4 py-3 rounded-lg bg-[#EC1D1D]/10 border border-[#EC1D1D]/30 text-sm text-[#EC1D1D] font-medium">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.new.update') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="password" class="block text-sm font-semibold text-[#161758] mb-1.5">
                        Password Baru
                    </label>
                    <div class="password-input-wrapper">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autofocus
                            autocomplete="new-password"
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

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-[#161758] mb-1.5">
                        Konfirmasi Password Baru
                    </label>
                    <div class="password-input-wrapper">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="field {{ $errors->has('password_confirmation') ? 'field-error' : '' }}"
                            placeholder="••••••••"
                        >
                        <button
                            type="button"
                            class="password-toggle-btn"
                            id="toggleConfirmPasswordBtn"
                            aria-label="Toggle confirm password visibility"
                            title="Toggle confirm password visibility"
                        >
                            <svg id="confirmEyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1.5 text-xs text-[#EC1D1D] font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#161758] text-white rounded-xl hover:bg-[#27438D] transition-colors duration-200 font-semibold text-base sm:text-lg"
                    style="min-height:52px;"
                >
                    Simpan Password &amp; Login
                </button>

                <p class="text-center text-sm text-[#3a3a55]/70">
                    <a href="{{ route('login') }}" class="font-semibold text-[#27438D] hover:text-[#00A2E9] transition-colors">
                        Kembali ke halaman login
                    </a>
                </p>
            </form>
        </div>

        <p class="footer-note">LANGKAH 3 DARI 3 — PASSWORD BARU</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle for password field
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

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

            // Toggle for confirm password field
            const toggleConfirmBtn = document.getElementById('toggleConfirmPasswordBtn');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const confirmEyeIcon = document.getElementById('confirmEyeIcon');

            if (toggleConfirmBtn && confirmPasswordInput) {
                toggleConfirmBtn.addEventListener('click', function() {
                    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordInput.setAttribute('type', type);

                    if (type === 'text') {
                        confirmEyeIcon.innerHTML = `
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        `;
                    } else {
                        confirmEyeIcon.innerHTML = `
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
