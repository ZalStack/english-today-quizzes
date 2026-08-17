@extends('layouts.hr')

@section('title', 'Edit Category')
@section('header-title', 'Edit Category')
@section('header-subtitle', 'Update category information')

@section('content')
<div class="max-w-2xl">
    <div class="hr-card overflow-hidden">
        <form action="{{ route('hr.categories.update', $category) }}" method="POST" class="p-6 sm:p-8 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Category Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="hr-input">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="hr-input">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('hr.categories.index') }}" class="hr-btn-secondary">Cancel</a>
                <button type="submit" class="hr-btn-primary">Update Category</button>
            </div>
        </form>
    </div>
</div>
@endsection
