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
            font-family:'Inter', system-ui, sans-serif;
            background: var(--paper);
            color:#232342;
        }
        .font-display{ font-family:'Space Grotesk', sans-serif; }
        .font-mono{ font-family:'JetBrains Mono', monospace; }

        .ruled-bg{
            background-image: repeating-linear-gradient(
                to bottom,
                transparent 0px, transparent 39px,
                rgba(22,23,88,0.05) 40px
            );
        }

        .field{
            width:100%; border:1.5px solid #E4E4EE; border-radius:.75rem;
            padding:.7rem .95rem; font-size:.95rem; color:#232342;
            background:#fff; transition:border-color .2s, box-shadow .2s;
        }
        .field:focus{
            outline:none; border-color: var(--cyan);
            box-shadow: 0 0 0 3px rgba(0,162,233,.15);
        }
        .field-error{ border-color: var(--alert); }

        .stamp-mini{
            position:absolute; top:-1rem; right:1.5rem;
            width:5.5rem; height:5.5rem; border-radius:999px;
            border:3px double var(--cyan); color: var(--cyan);
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            transform: rotate(9deg);
            background: rgba(255,255,255,.7);
            mix-blend-mode:multiply;
            animation: stamp-in .6s cubic-bezier(.2,1.4,.4,1) .3s both;
        }
        .stamp-mini span:first-child{ font-family:'JetBrains Mono', monospace; font-size:.5rem; letter-spacing:.15em; font-weight:700; }
        .stamp-mini span:last-child{ font-family:'Space Grotesk', sans-serif; font-size:.85rem; font-weight:700; }
        @keyframes stamp-in{
            from{ opacity:0; transform: rotate(24deg) scale(.7); }
            to{ opacity:1; transform: rotate(9deg) scale(1); }
        }

        @media (prefers-reduced-motion: reduce){
            .stamp-mini{ animation:none !important; }
        }
        :focus-visible{ outline:2px solid var(--cyan); outline-offset:3px; }
    </style>
</head>
<body class="antialiased ruled-bg min-h-screen">

    <div class="min-h-screen flex flex-col items-center justify-center px-5 py-12">

        <!-- Wordmark -->
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 mb-8">
            <div class="w-9 h-9 rounded-lg bg-[#161758] flex items-center justify-center">
                <span class="font-display font-bold text-white text-sm">ET</span>
            </div>
            <span class="font-display font-bold text-lg text-[#161758]">ET-Quizzes</span>
        </a>

        <div class="relative w-full max-w-sm">
            <div class="stamp-mini" aria-hidden="true">
                <span>SECURE</span>
                <span>LOGIN</span>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-black/5 p-8">
                <h1 class="font-display font-bold text-[#161758] text-2xl mb-1.5">Welcome back</h1>
                <p class="text-sm text-[#3a3a55]/70 mb-7">Sign in to pick up your assessment.</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-5 px-4 py-3 rounded-lg bg-[#2E7D3E]/10 border border-[#2E7D3E]/30 text-sm text-[#2E7D3E] font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-[#161758] mb-1.5">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               autocomplete="username"
                               class="field {{ $errors->has('email') ? 'field-error' : '' }}">
                        @error('email')
                            <p class="mt-1.5 text-xs text-[#EC1D1D] font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-[#161758] mb-1.5">Password</label>
                        <input id="password" type="password" name="password" required
                               autocomplete="current-password"
                               class="field {{ $errors->has('password') ? 'field-error' : '' }}">
                        @error('password')
                            <p class="mt-1.5 text-xs text-[#EC1D1D] font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="w-4 h-4 rounded border-gray-300 text-[#161758] focus:ring-[#00A2E9]">
                            <span class="text-sm text-[#3a3a55]/80">Remember me</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full py-3 bg-[#161758] text-white rounded-xl hover:bg-[#27438D] transition-colors duration-200 font-semibold">
                        Log in
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-[#3a3a55]/50 mt-6 font-mono tracking-wide">
                YOUR SCORE HISTORY IS SAVED TO YOUR PROFILE
            </p>
        </div>
    </div>

</body>
</html>
