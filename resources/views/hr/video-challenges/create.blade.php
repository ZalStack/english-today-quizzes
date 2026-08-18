@extends('layouts.hr')

@section('title', 'Create Video Challenge')
@section('header-title', 'Create Video Challenge')
@section('header-subtitle', 'Assign a new video challenge to employees')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('hr.video-challenges.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
</div>

<form action="{{ route('hr.video-challenges.store') }}" method="POST">
    @csrf

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Detail Challenge</h3>
            <p class="text-sm text-slate-500 mt-0.5">Informasi utama video challenge</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Challenge Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="hr-input" placeholder="e.g., Presentation Skills Challenge">
                    @error('title') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" class="hr-input" placeholder="Describe the challenge requirements">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="google_drive_link" class="block text-sm font-semibold text-slate-700 mb-2">Google Drive Link</label>
                    <input type="url" name="google_drive_link" id="google_drive_link" value="{{ old('google_drive_link') }}" class="hr-input" placeholder="https://drive.google.com/...">
                    @error('google_drive_link') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="deadline" class="block text-sm font-semibold text-slate-700 mb-2">Deadline</label>
                    <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline') }}" class="hr-input">
                    @error('deadline') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('hr.video-challenges.index') }}" class="hr-btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel
        </a>
        <button type="submit" class="hr-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Challenge
        </button>
    </div>
</form>
@endsection
