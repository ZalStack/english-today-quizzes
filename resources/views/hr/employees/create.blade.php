@extends('layouts.hr')

@section('title', 'Create Employee')
@section('header-title', 'Create Employee')
@section('header-subtitle', 'Register a new employee in the system')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('hr.employees.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<form action="{{ route('hr.employees.store') }}" method="POST">
    @csrf

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Informasi Dasar</h3>
            <p class="text-sm text-slate-500 mt-0.5">Data utama karyawan</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required class="hr-input" placeholder="e.g., John Doe">
                    @error('full_name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="hr-input" placeholder="john@company.com">
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="division_id" class="block text-sm font-semibold text-slate-700 mb-2">Division</label>
                    <select name="division_id" id="division_id" class="hr-input">
                        <option value="">Select Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="hr-input" placeholder="+62 xxx-xxxx-xxxx">
                    @error('phone') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">Address</label>
                    <textarea name="address" id="address" rows="2" class="hr-input" placeholder="Employee address">{{ old('address') }}</textarea>
                    @error('address') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Akun Login</h3>
            <p class="text-sm text-slate-500 mt-0.5">Kredensial untuk masuk ke sistem</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required class="hr-input" placeholder="Minimum 8 characters">
                    @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="hr-input" placeholder="Re-enter password">
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('hr.employees.index') }}" class="hr-btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel
        </a>
        <button type="submit" class="hr-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Create Employee
        </button>
    </div>
</form>
@endsection
