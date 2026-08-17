@extends('layouts.hr')

@section('title', 'Create Video Challenge')
@section('header-title', 'Create Video Challenge')
@section('header-subtitle', 'Assign a new video challenge to employees')

@section('content')
<div class="max-w-4xl">
    <div class="hr-card overflow-hidden">
        <form action="{{ route('hr.video-challenges.store') }}" method="POST" class="p-6 sm:p-8 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Challenge Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="hr-input" placeholder="e.g., Presentation Skills Challenge">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" class="hr-input" placeholder="Describe the challenge requirements">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="google_drive_link" class="block text-sm font-semibold text-slate-700 mb-2">Google Drive Link</label>
                    <input type="url" name="google_drive_link" id="google_drive_link" value="{{ old('google_drive_link') }}" class="hr-input" placeholder="https://drive.google.com/...">
                    @error('google_drive_link')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deadline" class="block text-sm font-semibold text-slate-700 mb-2">Deadline</label>
                    <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline') }}" class="hr-input">
                    @error('deadline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('hr.video-challenges.index') }}" class="hr-btn-secondary">Cancel</a>
                <button type="submit" class="hr-btn-primary">Create Challenge</button>
            </div>
        </form>
    </div>
</div>
@endsection
