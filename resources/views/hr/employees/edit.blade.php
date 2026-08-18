@extends('layouts.hr')

@section('title', 'Edit Employee')
@section('header-title', 'Edit Employee')
@section('header-subtitle', 'Update employee information')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('hr.employees.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<form action="{{ route('hr.employees.update', $employee) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Informasi Dasar</h3>
            <p class="text-sm text-slate-500 mt-0.5">Data utama karyawan</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $employee->full_name) }}" required class="hr-input">
                    @error('full_name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" required class="hr-input">
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="division_id" class="block text-sm font-semibold text-slate-700 mb-2">Division</label>
                    <select name="division_id" id="division_id" class="hr-input">
                        <option value="">Select Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('division_id', $employee->division_id) == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="hr-input">
                        <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}" class="hr-input">
                    @error('phone') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">Address</label>
                    <textarea name="address" id="address" rows="2" class="hr-input">{{ old('address', $employee->address) }}</textarea>
                    @error('address') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Ubah Password</h3>
            <p class="text-sm text-slate-500 mt-0.5">Kosongkan jika tidak ingin mengubah</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">New Password</label>
                    <input type="password" name="password" id="password" class="hr-input" placeholder="Leave blank to keep current">
                    @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="hr-input" placeholder="Confirm new password">
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
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Update Employee
        </button>
    </div>
</form>
@endsection
