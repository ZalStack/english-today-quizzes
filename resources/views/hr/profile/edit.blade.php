@extends('layouts.hr')

@section('title', 'Edit Profile')
@section('header-title', 'Profile Settings')
@section('header-subtitle', 'Update your personal information and password')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('hr.dashboard') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Dashboard
    </a>
</div>

<form action="{{ route('hr.profile.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Informasi Profil</h3>
            <p class="text-sm text-slate-500 mt-0.5">Data diri dan kontak</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->full_name) }}" required class="hr-input">
                    @error('full_name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="hr-input">
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="hr-input">
                    @error('phone') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">Address</label>
                    <textarea name="address" id="address" rows="2" class="hr-input">{{ old('address', $user->address) }}</textarea>
                    @error('address') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="hr-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Update Profile
        </button>
    </div>
</form>

<form action="{{ route('hr.profile.password') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="hr-card overflow-hidden mb-6 mt-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Ubah Password</h3>
            <p class="text-sm text-slate-500 mt-0.5">Pastikan password baru minimal 8 karakter</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-2">Current Password <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" id="current_password" required class="hr-input">
                    @error('current_password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required class="hr-input">
                    @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Confirm New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="hr-input">
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="hr-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Change Password
        </button>
    </div>
</form>
@endsection
