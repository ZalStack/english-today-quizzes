@extends('layouts.hr')

@section('title', 'Edit Profile')
@section('header-title', 'Profile Settings')
@section('header-subtitle', 'Update your personal information and password')

@section('content')
<div class="max-w-3xl">
    <div class="hr-card overflow-hidden">
        {{-- Profile Update --}}
        <form action="{{ route('hr.profile.update') }}" method="POST" class="p-6 sm:p-8 space-y-5 border-b border-slate-100">
            @csrf
            @method('PUT')

            <h3 class="text-base font-bold text-slate-900">Personal Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->full_name) }}" required class="hr-input">
                    @error('full_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="hr-input">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="hr-input">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">Address</label>
                    <textarea name="address" id="address" rows="3" class="hr-input">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="hr-btn-primary">Update Profile</button>
            </div>
        </form>

        {{-- Password Update --}}
        <form action="{{ route('hr.profile.password') }}" method="POST" class="p-6 sm:p-8 space-y-5">
            @csrf
            @method('PUT')

            <h3 class="text-base font-bold text-slate-900">Change Password</h3>

            <div class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-2">Current Password</label>
                    <input type="password" name="current_password" id="current_password" required class="hr-input">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">New Password</label>
                    <input type="password" name="password" id="password" required class="hr-input">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="hr-input">
                </div>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="hr-btn-primary">Change Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
