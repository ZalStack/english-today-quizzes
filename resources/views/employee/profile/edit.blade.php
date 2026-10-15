@extends('layouts.app')

@section('title', 'Profil & Keamanan')

@section('content')
@php
    $profileName = trim(($user->full_name ?? $user->name) ?: 'User');
    $profileInitial = strtoupper(substr($profileName, 0, 1));
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-14 sm:pb-16">

    {{-- ================= HEADER ================= --}}
    <header class="et-reveal mb-6 sm:mb-8 flex items-start gap-4">
        <span class="et-mascot et-mascot--bounce w-14 h-14 shrink-0 rounded-3xl bg-amber-100 dark:bg-amber-500/15 border-2 border-amber-200 dark:border-amber-500/25 flex items-center justify-center text-3xl" aria-hidden="true">🦉</span>
        <div class="min-w-0">
            <p class="et-eyebrow">👤 Akun Saya</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Profil & Keamanan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Perbarui informasi pribadi dan kata sandi akunmu.</p>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 items-start">

        {{-- ================= RINGKASAN PROFIL ================= --}}
        <aside class="et-panel et-reveal et-delay-1 overflow-hidden">
            <div class="relative h-20 sm:h-24 bg-gradient-to-r from-lime-500 via-emerald-500 to-sky-500 overflow-hidden">
                <div class="absolute inset-0 opacity-25"
                     style="background-image: linear-gradient(rgba(255,255,255,.4) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.4) 1px, transparent 1px); background-size: 26px 26px;"></div>
            </div>

            <div class="px-5 sm:px-6 pb-5 sm:pb-6 -mt-10 relative z-10">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-violet-500 to-sky-500 text-white flex items-center justify-center text-3xl font-extrabold ring-4 ring-white dark:ring-slate-900 shadow-xl shadow-sky-900/30 et-hover-pop">
                    {{ $profileInitial }}
                </div>

                <h2 class="mt-3 text-lg font-extrabold text-slate-900 dark:text-white break-words">{{ $profileName }}</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 break-all">{{ $user->email }}</p>

                <div class="mt-3 flex flex-wrap gap-2">
                    @if($user->division)
                        <span class="et-badge et-badge-info">🏢 {{ $user->division->name }}</span>
                    @endif
                    @if($user->role)
                        <span class="et-badge et-badge-purple">{{ ucfirst($user->role) }}</span>
                    @endif
                </div>

                <dl class="mt-4 pt-4 border-t border-dashed border-slate-200 dark:border-slate-800 space-y-2.5">
                    <div class="flex items-start gap-2.5 min-w-0">
                        <dt class="text-sm shrink-0" aria-label="Telepon">📱</dt>
                        <dd class="text-[13px] text-slate-600 dark:text-slate-300 min-w-0 break-words">{{ $user->phone ?: 'Belum diisi' }}</dd>
                    </div>
                    <div class="flex items-start gap-2.5 min-w-0">
                        <dt class="text-sm shrink-0" aria-label="Alamat">📍</dt>
                        <dd class="text-[13px] text-slate-600 dark:text-slate-300 min-w-0 break-words">{{ $user->address ?: 'Belum diisi' }}</dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <span class="et-chip">🔒 Data kamu aman & terlindungi</span>
                </div>
            </div>
        </aside>

        {{-- ================= FORMULIR ================= --}}
        <div class="lg:col-span-2 space-y-5 sm:space-y-6">

            {{-- Informasi Pribadi --}}
            <form action="{{ route('employee.profile.update') }}" method="POST" class="et-panel et-reveal et-delay-2 overflow-hidden">
                @csrf
                @method('PUT')

                <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="et-section-title">👤 Informasi Pribadi</h2>
                        <p class="et-section-sub">Data diri yang tampil pada akunmu</p>
                    </div>
                    <span class="et-badge et-badge-success shrink-0">Profil</span>
                </div>

                <div class="p-5 sm:p-6 space-y-4 sm:space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div class="sm:col-span-2">
                            <label for="full_name" class="et-label">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" name="full_name" id="full_name"
                                   value="{{ old('full_name', $user->full_name) }}" required
                                   placeholder="Masukkan nama lengkapmu"
                                   class="et-input @error('full_name') has-error @enderror">
                            @error('full_name')
                                <p class="et-error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="et-label">Alamat Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" id="email"
                                   value="{{ old('email', $user->email) }}" required
                                   placeholder="nama@email.com"
                                   class="et-input @error('email') has-error @enderror">
                            @error('email')
                                <p class="et-error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="et-label">Nomor Telepon</label>
                            <input type="text" name="phone" id="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="08xxxxxxxxxx"
                                   class="et-input @error('phone') has-error @enderror">
                            @error('phone')
                                <p class="et-error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address" class="et-label">Alamat</label>
                            <textarea name="address" id="address" rows="3"
                                      placeholder="Tulis alamat lengkapmu"
                                      class="et-textarea @error('address') has-error @enderror">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <p class="et-error-msg">{{ $message }}</p>
                            @enderror
                            <p class="et-hint">Opsional — boleh dikosongkan bila belum ingin menampilkan alamat.</p>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5 pt-1">
                        <button type="submit" class="et-btn et-btn-primary et-wiggle et-btn-block sm:w-auto et-press">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>

            {{-- Ubah Password --}}
            <form action="{{ route('employee.profile.password') }}" method="POST" class="et-panel et-reveal et-delay-3 overflow-hidden">
                @csrf
                @method('PUT')

                <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="et-section-title">🔐 Ubah Password</h2>
                        <p class="et-section-sub">Pastikan password baru aman dan mudah diingat</p>
                    </div>
                    <span class="et-badge et-badge-warning shrink-0">Keamanan</span>
                </div>

                <div class="p-5 sm:p-6 space-y-4 sm:space-y-5">
                    <div>
                        <label for="current_password" class="et-label">Password Saat Ini <span class="text-rose-500">*</span></label>
                        <input type="password" name="current_password" id="current_password" required
                               placeholder="Masukkan password yang sedang dipakai"
                               autocomplete="current-password"
                               class="et-input @error('current_password') has-error @enderror">
                        @error('current_password')
                            <p class="et-error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div>
                            <label for="password" class="et-label">Password Baru <span class="text-rose-500">*</span></label>
                            <input type="password" name="password" id="password" required
                                   placeholder="Minimal 8 karakter"
                                   autocomplete="new-password"
                                   class="et-input @error('password') has-error @enderror">
                            @error('password')
                                <p class="et-error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="et-label">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   placeholder="Ulangi password baru"
                                   autocomplete="new-password"
                                   class="et-input">
                            <p class="et-hint">Gunakan kombinasi huruf & angka agar lebih aman.</p>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5 pt-1">
                        <button type="submit" class="et-btn et-btn-outline et-btn-block sm:w-auto et-press">
                            🔐 Perbarui Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
