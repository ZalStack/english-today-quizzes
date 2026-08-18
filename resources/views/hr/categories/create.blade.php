@extends('layouts.hr')

@section('title', 'Create Category')
@section('header-title', 'Create Category')
@section('header-subtitle', 'Add a new category to organize quizzes')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('hr.categories.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
</div>

<form action="{{ route('hr.categories.store') }}" method="POST">
    @csrf

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Detail Kategori</h3>
            <p class="text-sm text-slate-500 mt-0.5">Isi informasi kategori baru</p>
        </div>
        <div class="p-6">
            <div class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Category Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="hr-input" placeholder="e.g., Grammar, Vocabulary, Business English">
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" class="hr-input" placeholder="Describe what this category is about">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('hr.categories.index') }}" class="hr-btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel
        </a>
        <button type="submit" class="hr-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Category
        </button>
    </div>
</form>
@endsection
