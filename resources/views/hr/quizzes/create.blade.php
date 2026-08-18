@extends('layouts.hr')

@section('title', 'Create Quiz')
@section('header-title', 'Create Quiz')
@section('header-subtitle', 'Set up a new assessment for your employees')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('hr.quizzes.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
</div>

<form action="{{ route('hr.quizzes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Informasi Quiz</h3>
            <p class="text-sm text-slate-500 mt-0.5">Detail utama dari quiz</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Quiz Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="hr-input" placeholder="e.g., English Grammar Test - Level 1">
                    @error('title') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="hr-input" placeholder="Brief description about this quiz">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-700 mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" required class="hr-input">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="duration" class="block text-sm font-semibold text-slate-700 mb-2">Duration (minutes) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration" id="duration" value="{{ old('duration', 30) }}" required min="1" class="hr-input">
                    @error('duration') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Pengaturan Tambahan</h3>
            <p class="text-sm text-slate-500 mt-0.5">Enrollment, thumbnail, dan jadwal</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="enroll_key" class="block text-sm font-semibold text-slate-700 mb-2">Enrollment Key</label>
                    <input type="text" name="enroll_key" id="enroll_key" value="{{ old('enroll_key') }}" class="hr-input" placeholder="Leave empty for no key">
                    @error('enroll_key') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="thumbnail" class="block text-sm font-semibold text-slate-700 mb-2">Thumbnail Image</label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="hr-input">
                    @error('thumbnail') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-semibold text-slate-700 mb-2">Start Date</label>
                    <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}" class="hr-input">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-semibold text-slate-700 mb-2">End Date</label>
                    <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}" class="hr-input">
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('hr.quizzes.index') }}" class="hr-btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel
        </a>
        <button type="submit" class="hr-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Quiz
        </button>
    </div>
</form>
@endsection
