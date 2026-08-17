@extends('layouts.hr')

@section('title', 'Edit Quiz')
@section('header-title', 'Edit Quiz')
@section('header-subtitle', 'Update quiz information')

@section('content')
<div class="max-w-4xl">
    <div class="hr-card overflow-hidden">
        <form action="{{ route('hr.quizzes.update', $quiz) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Quiz Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $quiz->title) }}" required class="hr-input">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="hr-input">{{ old('description', $quiz->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-700 mb-2">Category</label>
                    <select name="category_id" id="category_id" required class="hr-input">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $quiz->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="status" id="status" required class="hr-input">
                        <option value="draft" {{ $quiz->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ $quiz->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ $quiz->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div>
                    <label for="duration" class="block text-sm font-semibold text-slate-700 mb-2">Duration (minutes)</label>
                    <input type="number" name="duration" id="duration" value="{{ old('duration', $quiz->duration) }}" required min="1" class="hr-input">
                    @error('duration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="enroll_key" class="block text-sm font-semibold text-slate-700 mb-2">Enrollment Key</label>
                    <input type="text" name="enroll_key" id="enroll_key" value="{{ old('enroll_key', $quiz->enroll_key) }}" class="hr-input" placeholder="Leave empty for no key">
                    @error('enroll_key')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="thumbnail" class="block text-sm font-semibold text-slate-700 mb-2">Thumbnail Image</label>
                    @if($quiz->thumbnail)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$quiz->thumbnail) }}" class="w-16 h-16 rounded-lg object-cover">
                        </div>
                    @endif
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="hr-input">
                    @error('thumbnail')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-semibold text-slate-700 mb-2">Start Date</label>
                    <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date', $quiz->start_date?->format('Y-m-d\TH:i')) }}" class="hr-input">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-semibold text-slate-700 mb-2">End Date</label>
                    <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date', $quiz->end_date?->format('Y-m-d\TH:i')) }}" class="hr-input">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('hr.quizzes.index') }}" class="hr-btn-secondary">Cancel</a>
                <button type="submit" class="hr-btn-primary">Update Quiz</button>
            </div>
        </form>
    </div>
</div>
@endsection
