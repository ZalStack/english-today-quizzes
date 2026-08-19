@extends('layouts.hr')

@section('title', 'Questions - ' . $quiz->title)
@section('header-title', $quiz->title)
@section('header-subtitle', 'Manage questions for this quiz')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div>
        <a href="{{ route('hr.quizzes.show', $quiz) }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 mb-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Quiz
        </a>
    </div>
    <a href="{{ route('hr.quizzes.questions.create', $quiz) }}" class="hr-btn-primary w-full sm:w-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Question
    </a>
</div>

{{-- Quiz Info Card --}}
<div class="bg-gradient-to-r from-primary-600 to-primary-500 rounded-2xl p-6 mb-8 text-white shadow-glow-primary">
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div>
            <p class="text-primary-200 text-sm font-medium">Total Questions</p>
            <p class="text-xl font-bold tracking-tight">{{ $quiz->total_questions }}</p>
        </div>
        <div>
            <p class="text-primary-200 text-sm font-medium">Total Points</p>
            <p class="text-xl font-bold tracking-tight">{{ $quiz->questions->sum('points') ?? 0 }}/100</p>
        </div>
        <div>
            <p class="text-primary-200 text-sm font-medium">Duration</p>
            <p class="text-xl font-bold tracking-tight">{{ $quiz->duration }} min</p>
        </div>
        <div>
            <p class="text-primary-200 text-sm font-medium">Status</p>
            <p class="text-xl font-bold tracking-tight capitalize">{{ $quiz->status }}</p>
        </div>
        <div class="col-span-2 sm:col-span-1">
            <p class="text-primary-200 text-sm font-medium">Enroll Key</p>
            <p class="text-xl font-bold tracking-tight">{{ $quiz->enroll_key ?? 'None' }}</p>
        </div>
    </div>
</div>

{{-- Import Preview Banner --}}
@if(session('show_import_preview') && session('imported_questions'))
    @php $importedQuestions = session('imported_questions'); @endphp
    <div class="mb-8 rounded-2xl overflow-hidden border border-emerald-200/60 shadow-soft animate-fade-in">
        <div class="px-6 py-5 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-10 w-20 h-20 bg-white/5 rounded-full translate-y-1/2"></div>
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold tracking-tight">{{ count($importedQuestions) }} Soal Siap Direview</h3>
                        <p class="text-emerald-100 text-sm">Review dan edit sebelum menyimpan ke database</p>
                    </div>
                </div>
                <button onclick="document.getElementById('importPreview').scrollIntoView({behavior: 'smooth', block: 'start'})" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] whitespace-nowrap flex items-center gap-2">
                    Review Sekarang
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>
        </div>
    </div>
@endif

{{-- Questions List --}}
@if($questions->count() > 0)
    <div class="space-y-4">
        @foreach($questions as $index => $question)
            <div class="hr-card p-5 sm:p-6 hover:shadow-soft-md transition-all duration-300">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="hr-avatar w-8 h-8 text-xs">{{ $question->order_number }}</span>
                            <span class="hr-badge hr-badge--info">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                            <span class="hr-badge hr-badge--success">{{ $question->points }} pts</span>
                        </div>

                        <h3 class="text-base font-semibold text-slate-900 mb-3 break-words">{{ $question->question_text }}</h3>

                        @if($question->question_image)
                            <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question Image" class="mb-3 rounded-xl max-w-full sm:max-w-md h-auto">
                        @endif

                        @if($question->options && is_array($question->options))
                            <div class="bg-slate-50/80 rounded-xl p-4 ml-0 sm:ml-4 overflow-x-auto">
                                <p class="text-xs font-semibold text-slate-600 mb-2">Options:</p>
                                <div class="space-y-2">
                                    @foreach($question->options as $key => $option)
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full {{ $option === $question->correct_answer ? 'bg-emerald-500' : 'bg-slate-300' }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                {{ chr(65 + $key) }}
                                            </span>
                                            <span class="text-sm {{ $option === $question->correct_answer ? 'text-emerald-700 font-semibold' : 'text-slate-600' }} break-words">
                                                {{ $option }}
                                                @if($option === $question->correct_answer)
                                                    <span class="text-emerald-600 ml-1">&#10003;</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="bg-emerald-50/80 rounded-xl p-4 ml-0 sm:ml-4 border border-emerald-200/80">
                                <p class="text-xs font-semibold text-emerald-800">Correct Answer:</p>
                                <p class="text-emerald-700 whitespace-pre-wrap text-sm break-words">{{ $question->correct_answer }}</p>
                            </div>
                        @endif

                        @if($question->explanation)
                            <div class="bg-blue-50/80 rounded-xl p-4 mt-3 border border-blue-200/80">
                                <p class="text-xs font-semibold text-blue-800">Explanation:</p>
                                <p class="text-blue-700 whitespace-pre-wrap text-sm break-words">{{ $question->explanation }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex lg:flex-col flex-row gap-2">
                        <a href="{{ route('hr.quizzes.questions.edit', [$quiz, $question]) }}" class="flex-1 lg:flex-none px-4 py-2 bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-all duration-200 text-sm font-semibold text-center">
                            Edit
                        </a>
                        <form action="{{ route('hr.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" class="flex-1 lg:flex-none">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all duration-200 text-sm font-semibold" onclick="return confirm('Delete this question?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">
        {{ $questions->links() }}
    </div>
@else
    <div class="hr-card p-12 text-center">
        <div class="hr-empty__icon mx-auto mb-6" style="width:80px;height:80px;">
            <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">No Questions Yet</h3>
        <p class="text-slate-500 mb-6">Start building your quiz by adding questions</p>
        <a href="{{ route('hr.quizzes.questions.create', $quiz) }}" class="hr-btn-primary inline-flex">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add First Question
        </a>
    </div>
@endif

{{-- Import Preview Section --}}
@if(session('show_import_preview') && session('imported_questions'))
    @php
        $importedQuestions = session('imported_questions');
        $total = count($importedQuestions);
        $base = floor(100 / max($total, 1));
        $remainder = $total > 0 ? 100 % $total : 0;

        $mcCount = collect($importedQuestions)->where('question_type', 'multiple_choice')->count();
        $tfCount = collect($importedQuestions)->where('question_type', 'true_false')->count();
        $saCount = collect($importedQuestions)->where('question_type', 'short_answer')->count();
        $esCount = collect($importedQuestions)->where('question_type', 'essay')->count();
    @endphp

    <div id="importPreview" class="mt-8 overflow-hidden"
         x-data="{
             allChecked: true,
             expandedQuestions: {},
             toggleAll() {
                 document.querySelectorAll('.import-q-check').forEach(cb => cb.checked = this.allChecked);
             },
             toggleExpand(idx) {
                 this.expandedQuestions[idx] = !this.expandedQuestions[idx];
             },
             isExpanded(idx) {
                 return this.expandedQuestions[idx] === true;
             },
             countChecked() {
                 return document.querySelectorAll('.import-q-check:checked').length;
             }
         }">

        {{-- Header --}}
        <div class="rounded-t-2xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="relative">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl sm:text-2xl font-bold tracking-tight">Review Import Soal</h2>
                                <p class="text-emerald-100 text-sm mt-0.5">{{ $total }} soal terdeteksi dari PDF</p>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Pills --}}
                    <div class="flex flex-wrap gap-2">
                        @if($mcCount > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur-sm text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                {{ $mcCount }} Pilihan Ganda
                            </span>
                        @endif
                        @if($tfCount > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur-sm text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                {{ $tfCount }} Benar/Salah
                            </span>
                        @endif
                        @if($saCount > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur-sm text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                {{ $saCount }} Jawaban Singkat
                            </span>
                        @endif
                        @if($esCount > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur-sm text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                {{ $esCount }} Essay
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="bg-white rounded-b-2xl border border-t-0 border-emerald-200/60 shadow-soft-md">

            {{-- Alert Info --}}
            <div class="px-6 sm:px-8 pt-6">
                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/60 rounded-2xl">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="text-sm">
                        <p class="font-semibold text-amber-800">Review & Edit Sebelum Menyimpan</p>
                        <p class="text-amber-700/80 mt-0.5">Periksa setiap soal. Uncheck soal yang tidak ingin diimport. Klik judul soal untuk expand/collapse.</p>
                    </div>
                </div>
            </div>

            {{-- Point Distribution Info --}}
            <div class="px-6 sm:px-8 pt-4">
                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-200/60 rounded-2xl">
                    <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="text-sm">
                        <p class="font-semibold text-primary-800">Auto-Calculate Points (Total 100)</p>
                        <p class="text-primary-700/80 mt-0.5">Sistem akan otomatis membagi <strong>100 poin</strong> ke semua soal yang dipilih.</p>
                        <p class="mt-1.5 text-xs text-primary-600/80">
                            @if($remainder > 0)
                                <span class="font-semibold">Distribusi:</span> {{ $remainder }} soal &times; {{ $base + 1 }} pts + {{ $total - $remainder }} soal &times; {{ $base }} pts = 100 pts
                            @else
                                <span class="font-semibold">Distribusi:</span> {{ $total }} soal &times; {{ $base }} pts = 100 pts
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('hr.quizzes.questions.import.confirm', $quiz) }}" method="POST">
                @csrf

                {{-- Toolbar --}}
                <div class="px-6 sm:px-8 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" x-model="allChecked" @change="toggleAll()" class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500 focus:ring-offset-0 border-slate-300 cursor-pointer">
                            <span class="text-sm font-semibold text-slate-700 group-hover:text-slate-900 transition-colors">Pilih Semua</span>
                        </label>
                        <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-lg font-medium" x-text="countChecked() + ' / {{ $total }} dipilih'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="document.querySelectorAll('.import-q-card').forEach(c => c.classList.remove('hidden'))" class="text-xs font-medium text-primary-600 hover:text-primary-700 px-3 py-1.5 rounded-lg hover:bg-primary-50 transition-all">
                            Expand All
                        </button>
                        <span class="text-slate-200">|</span>
                        <button type="button" @click="expandedQuestions = {}; document.querySelectorAll('.import-q-detail').forEach(d => d.classList.add('hidden'))" class="text-xs font-medium text-slate-500 hover:text-slate-700 px-3 py-1.5 rounded-lg hover:bg-slate-100 transition-all">
                            Collapse All
                        </button>
                    </div>
                </div>

                {{-- Questions List --}}
                <div class="divide-y divide-slate-100 max-h-[700px] overflow-y-auto import-q-list">
                    @foreach($importedQuestions as $index => $question)
                        @php
                            $autoPoints = $base + ($index < $remainder ? 1 : 0);
                            $typeBadgeClass = match($question['question_type']) {
                                'multiple_choice' => 'hr-badge--info',
                                'true_false' => 'hr-badge--danger',
                                'short_answer' => 'hr-badge--warning',
                                'essay' => 'hr-badge--neutral',
                                default => 'hr-badge--info'
                            };
                            $typeLabel = match($question['question_type']) {
                                'multiple_choice' => 'PG',
                                'true_false' => 'B/S',
                                'short_answer' => 'Singkat',
                                'essay' => 'Essay',
                                default => ucfirst($question['question_type'])
                            };
                        @endphp
                        <div class="import-q-item px-6 sm:px-8 py-4 hover:bg-slate-50/50 transition-colors" x-data="{ open: false }">
                            {{-- Question Header --}}
                            <div class="flex items-start gap-3 cursor-pointer select-none" @click="open = !open">
                                <input type="checkbox" name="questions[{{ $index }}][import]" value="1" checked
                                       class="import-q-check w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500 focus:ring-offset-0 border-slate-300 mt-0.5 flex-shrink-0 cursor-pointer"
                                       onclick="event.stopPropagation()">

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 text-white text-xs font-bold flex-shrink-0">{{ $question['order_number'] }}</span>
                                        <span class="hr-badge {{ $typeBadgeClass }} text-[11px]">{{ $typeLabel }}</span>
                                        <span class="hr-badge hr-badge--success text-[11px]">{{ $autoPoints }} pts</span>
                                        @if(!empty($question['options']))
                                            <span class="text-[11px] text-slate-400">{{ count($question['options']) }} opsi</span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-medium text-slate-800 leading-relaxed line-clamp-2">{{ $question['question_text'] }}</p>
                                </div>

                                <svg class="w-5 h-5 text-slate-300 flex-shrink-0 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>

                            {{-- Question Detail (Expandable) --}}
                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-4 ml-8 space-y-4">

                                {{-- Type & Points Row --}}
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs font-semibold text-slate-500 whitespace-nowrap">Tipe:</label>
                                        <select name="questions[{{ $index }}][question_type]" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer transition-all">
                                            <option value="multiple_choice" {{ $question['question_type'] === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                            <option value="short_answer" {{ $question['question_type'] === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                            <option value="true_false" {{ $question['question_type'] === 'true_false' ? 'selected' : '' }}>True/False</option>
                                            <option value="essay" {{ $question['question_type'] === 'essay' ? 'selected' : '' }}>Essay</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs font-semibold text-slate-500 whitespace-nowrap">Poin:</label>
                                        <input type="number" name="questions[{{ $index }}][points]" value="{{ $autoPoints }}" min="1" class="w-20 text-xs px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                    </div>
                                </div>

                                <input type="hidden" name="questions[{{ $index }}][order_number]" value="{{ $question['order_number'] }}">

                                {{-- Question Text --}}
                                <div>
                                    <label class="text-xs font-semibold text-slate-500 flex items-center gap-1.5 mb-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Teks Soal
                                    </label>
                                    <textarea name="questions[{{ $index }}][question_text]" rows="3" class="hr-input text-sm leading-relaxed">{{ $question['question_text'] }}</textarea>
                                </div>

                                {{-- Options --}}
                                @if(!empty($question['options']))
                                    <div>
                                        <label class="text-xs font-semibold text-slate-500 flex items-center gap-1.5 mb-2">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                            Pilihan Jawaban
                                        </label>
                                        <div class="space-y-2 pl-1">
                                            @foreach($question['options'] as $optIndex => $option)
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 shadow-sm">{{ chr(65 + $optIndex) }}</span>
                                                    <input type="text" name="questions[{{ $index }}][options][{{ $optIndex }}]" value="{{ $option }}" class="flex-1 hr-input text-sm" placeholder="Opsi {{ chr(65 + $optIndex) }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Correct Answer --}}
                                <div>
                                    <label class="text-xs font-semibold text-slate-500 flex items-center gap-1.5 mb-1.5">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Jawaban Benar
                                        @if($question['question_type'] === 'essay')
                                            <span class="text-xs text-amber-500 font-normal italic ml-1">(Dinilai manual)</span>
                                        @endif
                                    </label>
                                    @if($question['question_type'] === 'essay')
                                        <div class="hr-input text-sm bg-slate-50 text-slate-400 italic border-dashed">Jawaban essay akan dinilai secara manual</div>
                                        <input type="hidden" name="questions[{{ $index }}][correct_answer]" value="{{ $question['correct_answer'] }}">
                                    @else
                                        <textarea name="questions[{{ $index }}][correct_answer]" rows="{{ $question['question_type'] === 'short_answer' ? 1 : 2 }}" class="hr-input text-sm border-emerald-300 bg-emerald-50/50 focus:border-emerald-500">{{ $question['correct_answer'] }}</textarea>
                                    @endif
                                </div>

                                {{-- Explanation --}}
                                <div>
                                    <label class="text-xs font-semibold text-slate-500 flex items-center gap-1.5 mb-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Pembahasan <span class="font-normal text-slate-400">(opsional)</span>
                                    </label>
                                    <input type="text" name="questions[{{ $index }}][explanation]" value="{{ $question['explanation'] ?? '' }}" class="hr-input text-sm" placeholder="Tambahkan pembahasan...">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer Actions --}}
                <div class="px-6 sm:px-8 py-5 bg-slate-50/80 border-t border-slate-100 rounded-b-2xl">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <a href="{{ route('hr.quizzes.questions.import.cancel', $quiz) }}"
                           onclick="return confirm('Batalkan import? Semua data parsing akan dihapus.')"
                           class="hr-btn-secondary w-full sm:w-auto justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batalkan Import
                        </a>
                        <button type="submit" class="hr-btn-primary w-full sm:w-auto justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Semua Soal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif

@push('scripts')
<script>
    @if(session('show_import_preview'))
        setTimeout(() => {
            const preview = document.getElementById('importPreview');
            if (preview) {
                preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 500);
    @endif
</script>
@endpush
@endsection
