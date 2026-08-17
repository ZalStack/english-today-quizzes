@extends('layouts.hr')

@section('title', 'Create Employee')
@section('header-title', 'Create Employee')
@section('header-subtitle', 'Register a new employee in the system')

@section('content')
<div class="max-w-3xl">
    <div class="hr-card overflow-hidden">
        <form action="{{ route('hr.employees.store') }}" method="POST" class="p-6 sm:p-8 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required class="hr-input" placeholder="e.g., John Doe">
                    @error('full_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="hr-input" placeholder="john@company.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="division_id" class="block text-sm font-semibold text-slate-700 mb-2">Division</label>
                    <select name="division_id" id="division_id" class="hr-input">
                        <option value="">Select Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <input type="password" name="password" id="password" required class="hr-input" placeholder="Minimum 8 characters">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="hr-input" placeholder="Re-enter password">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="hr-input" placeholder="+62 xxx-xxxx-xxxx">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">Address</label>
                <textarea name="address" id="address" rows="3" class="hr-input" placeholder="Employee address">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('hr.employees.index') }}" class="hr-btn-secondary">Cancel</a>
                <button type="submit" class="hr-btn-primary">Create Employee</button>
            </div>
        </form>
    </div>
</div>
@endsection
