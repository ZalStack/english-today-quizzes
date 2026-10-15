@extends('layouts.app')

@section('title', 'Gabung Kuis')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-14 sm:pb-16">
    <div class="max-w-2xl mx-auto w-full">

        {{-- Page header --}}
        <header class="et-reveal mb-6 sm:mb-8">
            <p class="et-eyebrow">🔑 Join Kuis</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Gabung Kuis dengan Kode</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Masukkan kode enrollment dari HRD untuk membuka kuis barumu.</p>
        </header>

        {{-- Join card --}}
        <div class="et-panel et-reveal et-delay-2">
            {{-- Card hero --}}
            <div class="et-hero">
                <div class="et-hero__grid"></div>
                <div class="relative z-10 p-6 sm:p-8 text-center">
                    <span class="et-mascot et-mascot--bounce mx-auto w-16 h-16 sm:w-20 sm:h-20 text-4xl sm:text-5xl" aria-hidden="true">🦉</span>
                    <h2 class="mt-4 text-xl sm:text-2xl font-extrabold tracking-tight">Satu Kode, Satu Kuis</h2>
                    <p class="mt-1.5 text-sm text-white/80 max-w-md mx-auto">
                        Kode enrollment bersifat unik untuk setiap kuis. Pastikan kamu mengetiknya dengan benar.
                    </p>
                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        <span class="et-glass rounded-full px-3 py-1.5 text-[11px] font-bold uppercase tracking-widest">Enrollment Key</span>
                        <span class="et-glass rounded-full px-3 py-1.5 text-[11px] font-bold uppercase tracking-widest">🔒 Data aman</span>
                    </div>
                </div>
            </div>

            {{-- Form body --}}
            <div class="p-5 sm:p-7">
                <form action="{{ route('employee.quizzes.enroll') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="enroll_key" class="et-label">Kode Enrollment</label>
                        <input type="text" name="enroll_key" id="enroll_key"
                            value="{{ old('enroll_key') }}" required
                            autocomplete="off" autocapitalize="characters" spellcheck="false"
                            class="et-input text-center text-xl sm:text-2xl font-extrabold uppercase tracking-[0.3em] sm:tracking-[0.4em] py-4 sm:py-5 placeholder:text-slate-300 dark:placeholder:text-slate-600"
                            placeholder="AB12CD">
                        <p class="et-hint">💡 Masukkan kode yang diberikan oleh admin HRD.</p>
                        @error('enroll_key')
                            <p class="et-error-msg">⚠️ {{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="et-btn et-btn-primary et-btn-lg et-btn-block et-press et-wiggle">
                        🚀 Gabung Kuis Sekarang
                    </button>
                </form>

                {{-- Tips --}}
                <div class="mt-6 p-4 sm:p-5 rounded-2xl border-2 border-dashed border-amber-200/70 dark:border-amber-500/30 bg-amber-50/70 dark:bg-amber-500/10 et-lift">
                    <h4 class="text-[12px] font-extrabold uppercase tracking-wider text-amber-800 dark:text-amber-300">
                        💡 Cara mendapatkan kode enrollment
                    </h4>
                    <ul class="mt-3 space-y-2">
                        @php
                            $joinTips = [
                                'Minta kode enrollment ke admin HRD penyelenggara kuis.',
                                'Setiap kuis punya kode unik yang berbeda satu sama lain.',
                                'Kamu hanya bisa mengikuti kuis yang masih berstatus aktif.',
                            ];
                        @endphp
                        @foreach($joinTips as $tip)
                            <li class="flex items-start gap-2.5 text-[13px] leading-snug text-slate-600 dark:text-slate-300">
                                <span class="mt-0.5 w-5 h-5 shrink-0 rounded-full bg-lime-100 dark:bg-lime-500/25 text-lime-600 dark:text-lime-300 flex items-center justify-center text-[11px] font-extrabold">✓</span>
                                <span>{{ $tip }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-5 pt-4 border-t border-dashed border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center sm:justify-between gap-3">
                    <a href="{{ route('employee.dashboard') }}"
                       class="et-btn et-btn-ghost et-press justify-center">← Kembali ke Dashboard</a>
                    <a href="{{ route('employee.quizzes.history') }}"
                       class="et-btn et-btn-outline et-press justify-center">🕒 Lihat Riwayat Kuis</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
