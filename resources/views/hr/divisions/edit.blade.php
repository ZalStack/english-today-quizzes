@extends('layouts.hr')

@section('title', 'Edit Division')
@section('header-title', 'Edit Division')
@section('header-subtitle', 'Update division information')

@section('content')
<div class="max-w-2xl">
    <div class="hr-card overflow-hidden">
        <form action="{{ route('hr.divisions.update', $division) }}" method="POST" class="p-6 sm:p-8 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Division Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $division->name) }}" required class="hr-input">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="hr-input">{{ old('description', $division->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('hr.divisions.index') }}" class="hr-btn-secondary">Cancel</a>
                <button type="submit" class="hr-btn-primary">Update Division</button>
            </div>
        </form>
    </div>
</div>
@endsection
