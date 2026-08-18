@extends('layouts.hr')

@section('title', $quiz->title)
@section('header-title', $quiz->title)
@section('header-subtitle', 'Quiz details and overview')

@section('content')
<div class="mb-6">
    <a href="{{ route('hr.quizzes.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Quizzes
    </a>
</div>

{{-- Quiz Info Card --}}
<div class="bg-gradient-to-r from-primary-600 to-primary-500 rounded-2xl p-6 sm:p-8 mb-8 text-white shadow-glow-primary">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
        <div>
            <p class="text-primary-200 text-sm font-medium">Total Questions</p>
            <p class="text-2xl font-bold mt-1 tracking-tight">{{ $quiz->total_questions }}</p>
        </div>
        <div>
            <p class="text-primary-200 text-sm font-medium">Duration</p>
            <p class="text-2xl font-bold mt-1 tracking-tight">{{ $quiz->duration }} min</p>
        </div>
        <div>
            <p class="text-primary-200 text-sm font-medium">Status</p>
            <p class="text-2xl font-bold mt-1 tracking-tight capitalize">{{ $quiz->status }}</p>
        </div>
        <div>
            <p class="text-primary-200 text-sm font-medium">Enroll Key</p>
            <p class="text-2xl font-bold mt-1 tracking-tight">{{ $quiz->enroll_key ?? 'None' }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="hr-card p-6">
        <h3 class="text-base font-bold text-slate-900 mb-4 tracking-tight">Quiz Information</h3>
        <dl class="space-y-3">
            <div class="flex justify-between">
                <dt class="text-sm text-slate-500">Category</dt>
                <dd class="text-sm font-semibold text-slate-900">{{ $quiz->category->name ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-slate-500">Total Attempts</dt>
                <dd class="text-sm font-semibold text-slate-900">{{ $quiz->attempts_count }}</dd>
            </div>
            @if($quiz->start_date)
            <div class="flex justify-between">
                <dt class="text-sm text-slate-500">Start Date</dt>
                <dd class="text-sm font-semibold text-slate-900">{{ $quiz->start_date->format('d M Y, H:i') }}</dd>
            </div>
            @endif
            @if($quiz->end_date)
            <div class="flex justify-between">
                <dt class="text-sm text-slate-500">End Date</dt>
                <dd class="text-sm font-semibold text-slate-900">{{ $quiz->end_date->format('d M Y, H:i') }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <div class="hr-card p-6">
        <h3 class="text-base font-bold text-slate-900 mb-4 tracking-tight">Actions</h3>
        <div class="space-y-3">
            <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="hr-btn-primary w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Manage Questions
            </a>
            <a href="{{ route('hr.quizzes.edit', $quiz) }}" class="hr-btn-secondary w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Quiz
            </a>
            <a href="{{ route('hr.reports.show', $quiz) }}" class="hr-btn-secondary w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                View Reports
            </a>
        </div>
    </div>
</div>
@endsection
