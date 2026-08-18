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
    <div class="hr-card overflow-hidden mb-8 border-2 border-emerald-200/80">
        <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-green-600 text-white">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-lg font-bold">Preview Import: {{ count($importedQuestions) }} Soal Terdeteksi</h3>
                    <p class="text-emerald-100 text-sm mt-0.5">Review dan edit sebelum menyimpan ke database</p>
                </div>
                <button onclick="document.getElementById('importPreview').scrollIntoView({behavior: 'smooth'})" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm font-medium transition">
                    Review Sekarang &darr;
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
        $base = floor(100 / $total);
        $remainder = 100 % $total;
    @endphp

    <div id="importPreview" class="mt-8 hr-card overflow-hidden border-2 border-emerald-200/80">
        <div class="px-6 sm:px-8 py-6 bg-gradient-to-r from-emerald-600 to-green-600 text-white">
            <h2 class="text-xl font-bold">Preview Imported Questions</h2>
            <p class="text-emerald-100 text-sm mt-0.5">Review {{ $total }} soal yang terdeteksi dari PDF</p>
        </div>

        <form action="{{ route('hr.quizzes.questions.import.confirm', $quiz) }}" method="POST" class="p-6 sm:p-8">
            @csrf

            <div class="mb-4 p-4 bg-amber-50/80 border border-amber-200/80 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="text-sm text-amber-800">
                    <p class="font-semibold">Review & Edit Sebelum Menyimpan</p>
                    <p>Periksa setiap soal. Uncheck soal yang tidak ingin diimport.</p>
                </div>
            </div>

            <div class="mb-6 p-4 bg-primary-50/80 border border-primary-200/80 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-primary-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-primary-800">
                    <p class="font-semibold">Auto-Calculate Points (Max 100)</p>
                    <p>Sistem akan otomatis membagi total <strong>100 poin</strong> ke {{ $total }} soal yang dipilih.</p>
                    <p class="mt-1 text-xs">
                        <strong>Distribusi:</strong>
                        @if($remainder > 0)
                            {{ $remainder }} soal &times; {{ $base + 1 }} poin + {{ $total - $remainder }} soal &times; {{ $base }} poin = 100 poin
                        @else
                            {{ $total }} soal &times; {{ $base }} poin = 100 poin
                        @endif
                    </p>
                </div>
            </div>

            <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                @foreach($importedQuestions as $index => $question)
                    @php
                        $autoPoints = $base + ($index < $remainder ? 1 : 0);
                    @endphp
                    <div class="border border-slate-200/80 rounded-xl p-5 hover:shadow-soft transition-all duration-200 bg-white">
                        <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <input type="checkbox" name="questions[{{ $index }}][import]" value="1" checked class="w-5 h-5 text-emerald-600 rounded focus:ring-emerald-500">
                                <span class="hr-avatar w-8 h-8 text-xs bg-gradient-to-br from-emerald-500 to-green-600">{{ $question['order_number'] }}</span>
                                <select name="questions[{{ $index }}][question_type]" class="hr-badge hr-badge--success border-0 focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                                    <option value="multiple_choice" {{ $question['question_type'] === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                    <option value="short_answer" {{ $question['question_type'] === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                    <option value="true_false" {{ $question['question_type'] === 'true_false' ? 'selected' : '' }}>True/False</option>
                                    <option value="essay" {{ $question['question_type'] === 'essay' ? 'selected' : '' }}>Essay</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-slate-500">Points:</label>
                                <input type="number" name="questions[{{ $index }}][points]" value="{{ $autoPoints }}" min="1" class="hr-input w-20 text-sm">
                            </div>
                        </div>

                        <input type="hidden" name="questions[{{ $index }}][order_number]" value="{{ $question['order_number'] }}">

                        <textarea name="questions[{{ $index }}][question_text]" rows="2" class="hr-input mb-3">{{ $question['question_text'] }}</textarea>

                        @if(!empty($question['options']))
                            <div class="bg-slate-50/80 rounded-xl p-3 mb-3 space-y-2">
                                <p class="text-xs font-semibold text-slate-600">Pilihan Jawaban:</p>
                                @foreach($question['options'] as $optIndex => $option)
                                    <div class="flex items-center gap-2">
                                        <span class="hr-avatar w-6 h-6 text-xs bg-gradient-to-br from-emerald-400 to-green-500">{{ chr(65 + $optIndex) }}</span>
                                        <input type="text" name="questions[{{ $index }}][options][{{ $optIndex }}]" value="{{ $option }}" class="flex-1 hr-input text-sm">
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="text-xs font-semibold text-slate-600 flex items-center gap-1">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Jawaban Benar:
                                @if($question['question_type'] === 'essay')
                                    <span class="ml-1 text-xs text-amber-600 font-normal italic">(Essay dinilai manual)</span>
                                @endif
                            </label>

                            @if($question['question_type'] === 'essay')
                                <div class="hr-input bg-slate-100 text-slate-500 italic mt-1">Jawaban essay dinilai manual</div>
                                <input type="hidden" name="questions[{{ $index }}][correct_answer]" value="{{ $question['correct_answer'] }}">
                            @else
                                <textarea name="questions[{{ $index }}][correct_answer]" rows="{{ $question['question_type'] === 'short_answer' ? 1 : 2 }}" class="hr-input border-emerald-300 bg-emerald-50/80 mt-1">{{ $question['correct_answer'] }}</textarea>
                            @endif
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-600">Pembahasan (Opsional):</label>
                            <input type="text" name="questions[{{ $index }}][explanation]" value="{{ $question['explanation'] ?? '' }}" class="hr-input mt-1">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center pt-6 mt-6 border-t gap-3">
                <a href="{{ route('hr.quizzes.questions.import.cancel', $quiz) }}" onclick="return confirm('Batalkan import? Data yang sudah diparse akan dihapus.')" class="hr-btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batalkan Import
                </a>
                <button type="submit" class="hr-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save All Questions
                </button>
            </div>
        </form>
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
