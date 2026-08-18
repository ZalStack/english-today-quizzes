@extends('layouts.hr')

@section('title', 'Edit Division')
@section('header-title', 'Edit Division')
@section('header-subtitle', 'Update division information')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('hr.divisions.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
</div>

<form action="{{ route('hr.divisions.update', $division) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Detail Divisi</h3>
            <p class="text-sm text-slate-500 mt-0.5">Perbarui informasi divisi</p>
        </div>
        <div class="p-6">
            <div class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Division Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $division->name) }}" required class="hr-input">
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" class="hr-input">{{ old('description', $division->description) }}</textarea>
                    @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('hr.divisions.index') }}" class="hr-btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel
        </a>
        <button type="submit" class="hr-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Update Division
        </button>
    </div>
</form>
@endsection
