@extends('layouts.hr')

@section('title', 'Create Division')
@section('header-title', 'Create Division')
@section('header-subtitle', 'Add a new department to your organization')

@section('content')
<div class="max-w-2xl">
    <div class="hr-card overflow-hidden">
        <form action="{{ route('hr.divisions.store') }}" method="POST" class="p-6 sm:p-8 space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Division Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="hr-input" placeholder="e.g., Engineering, Marketing, HR">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="hr-input" placeholder="Brief description of this division's responsibilities">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('hr.divisions.index') }}" class="hr-btn-secondary">Cancel</a>
                <button type="submit" class="hr-btn-primary">Create Division</button>
            </div>
        </form>
    </div>
</div>
@endsection
