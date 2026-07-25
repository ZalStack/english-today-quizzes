@extends('layouts.app')

@section('title', 'Questions - ' . $quiz->title)

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3 sm:gap-0">
            <div>
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1 sm:mb-2">
                    <a href="{{ route('hr.quizzes.show', $quiz) }}" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $quiz->title }}</h1>
                </div>
                <p class="text-gray-500 text-sm sm:text-base">Manage questions for this quiz</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                <a href="{{ route('hr.quizzes.questions.create', $quiz) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Question
                </a>
            </div>
        </div>

        {{-- Quiz Info Card --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-4 sm:p-6 mb-6 sm:mb-8 text-white">
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
                <div>
                    <p class="text-indigo-200 text-[10px] sm:text-sm">Total Questions</p>
                    <p class="text-xl sm:text-2xl font-bold">{{ $quiz->total_questions }}</p>
                </div>
                <div>
                    <p class="text-indigo-200 text-[10px] sm:text-sm">Total Points</p>
                    <p class="text-xl sm:text-2xl font-bold">{{ $quiz->questions->sum('points') ?? 0 }}/100</p>
                </div>
                <div>
                    <p class="text-indigo-200 text-[10px] sm:text-sm">Duration</p>
                    <p class="text-xl sm:text-2xl font-bold">{{ $quiz->duration }} min</p>
                </div>
                <div>
                    <p class="text-indigo-200 text-[10px] sm:text-sm">Status</p>
                    <p class="text-xl sm:text-2xl font-bold capitalize">{{ $quiz->status }}</p>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <p class="text-indigo-200 text-[10px] sm:text-sm">Enroll Key</p>
                    <p class="text-xl sm:text-2xl font-bold">{{ $quiz->enroll_key ?? 'None' }}</p>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl flex items-center text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        {{-- Import Preview Banner --}}
        @if(session('show_import_preview') && session('imported_questions'))
            @php $importedQuestions = session('imported_questions'); @endphp
            <div class="bg-white rounded-2xl shadow-sm border-2 border-green-200 overflow-hidden mb-6 sm:mb-8">
                <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
                        <div>
                            <h3 class="text-base sm:text-xl font-bold flex items-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Preview Import: {{ count($importedQuestions) }} Soal Terdeteksi
                            </h3>
                            <p class="text-green-100 text-xs sm:text-sm mt-0.5">Review dan edit sebelum menyimpan ke database</p>
                        </div>
                        <button onclick="document.getElementById('importPreview').scrollIntoView({behavior: 'smooth'})"
                                class="bg-white/20 hover:bg-white/30 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition">
                            Review Sekarang ↓
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Questions List --}}
        @if($questions->count() > 0)
            <div class="space-y-3 sm:space-y-4">
                @foreach($questions as $index => $question)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-3 sm:gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center space-x-2 sm:space-x-3 mb-2 sm:mb-3">
                                    <span class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-xs sm:text-sm flex-shrink-0">
                                        {{ $question->order_number }}
                                    </span>
                                    <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-indigo-100 text-indigo-700 text-[10px] sm:text-xs font-semibold rounded-full">
                                        {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                    </span>
                                    <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-green-100 text-green-700 text-[10px] sm:text-xs font-semibold rounded-full">
                                        {{ $question->points }} pts
                                    </span>
                                </div>

                                <h3 class="text-sm sm:text-lg font-semibold text-gray-900 mb-2 sm:mb-3 break-words">{{ $question->question_text }}</h3>

                                @if($question->question_image)
                                    <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question Image" class="mb-3 rounded-lg max-w-full sm:max-w-md h-auto">
                                @endif

                                @if($question->options && is_array($question->options))
                                    <div class="bg-gray-50 rounded-xl p-3 sm:p-4 ml-0 sm:ml-4 overflow-x-auto">
                                        <p class="text-xs sm:text-sm font-semibold text-gray-700 mb-2">Options:</p>
                                        <div class="space-y-1.5 sm:space-y-2">
                                            @foreach($question->options as $key => $option)
                                                <div class="flex items-center space-x-2">
                                                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full {{ $option === $question->correct_answer ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center text-white text-[10px] sm:text-xs font-bold flex-shrink-0">
                                                        {{ chr(65 + $key) }}
                                                    </span>
                                                    <span class="text-xs sm:text-sm {{ $option === $question->correct_answer ? 'text-green-700 font-semibold' : 'text-gray-600' }} break-words">
                                                        {{ $option }}
                                                        @if($option === $question->correct_answer)
                                                            <span class="text-green-600 ml-1">✓</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-green-50 rounded-xl p-3 sm:p-4 ml-0 sm:ml-4 border border-green-200">
                                        <p class="text-xs sm:text-sm font-semibold text-green-800">Correct Answer:</p>
                                        <p class="text-green-700 whitespace-pre-wrap text-xs sm:text-sm break-words">{{ $question->correct_answer }}</p>
                                    </div>
                                @endif

                                @if($question->explanation)
                                    <div class="bg-blue-50 rounded-xl p-3 sm:p-4 mt-3 border border-blue-200">
                                        <p class="text-xs sm:text-sm font-semibold text-blue-800">Explanation:</p>
                                        <p class="text-blue-700 whitespace-pre-wrap text-xs sm:text-sm break-words">{{ $question->explanation }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex lg:flex-col flex-row lg:space-x-0 space-x-2 lg:space-y-2">
                                <a href="{{ route('hr.quizzes.questions.edit', [$quiz, $question]) }}"
                                   class="flex-1 lg:flex-none px-3 sm:px-4 py-1.5 sm:py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition text-xs sm:text-sm font-medium text-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('hr.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" class="flex-1 lg:flex-none">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full px-3 sm:px-4 py-1.5 sm:py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-xs sm:text-sm font-medium"
                                            onclick="return confirm('Delete this question?')">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 sm:mt-6">
                {{ $questions->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-16 text-center">
                <div class="w-16 h-16 sm:w-24 sm:h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                    <svg class="w-8 h-8 sm:w-12 sm:h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">No Questions Yet</h3>
                <p class="text-gray-500 text-sm sm:text-base mb-4 sm:mb-6">Start building your quiz by adding questions</p>
                <a href="{{ route('hr.quizzes.questions.create', $quiz) }}"
                   class="inline-flex items-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
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

            <div id="importPreview" class="mt-6 sm:mt-8 bg-white rounded-2xl shadow-lg border-2 border-green-200 overflow-hidden">
                <div class="px-4 sm:px-8 py-3 sm:py-6 bg-gradient-to-r from-green-600 to-emerald-600 text-white">
                    <h2 class="text-lg sm:text-2xl font-bold flex items-center">
                        <svg class="w-5 h-5 sm:w-7 sm:h-7 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Preview Imported Questions
                    </h2>
                    <p class="text-green-100 text-xs sm:text-sm mt-0.5">Review {{ $total }} soal yang terdeteksi dari PDF</p>
                </div>

                <form action="{{ route('hr.quizzes.questions.import.confirm', $quiz) }}" method="POST" class="p-4 sm:p-8">
                    @csrf

                    <div class="mb-4 p-3 sm:p-4 bg-amber-50 border border-amber-200 rounded-xl flex flex-col sm:flex-row items-start gap-2 sm:gap-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="text-xs sm:text-sm text-amber-800">
                            <p class="font-semibold">Review & Edit Sebelum Menyimpan</p>
                            <p>Periksa setiap soal. Uncheck soal yang tidak ingin diimport.</p>
                        </div>
                    </div>

                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-indigo-50 border border-indigo-200 rounded-xl flex flex-col sm:flex-row items-start gap-2 sm:gap-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-xs sm:text-sm text-indigo-800">
                            <p class="font-semibold">💯 Auto-Calculate Points (Max 100)</p>
                            <p>Sistem akan otomatis membagi total <strong>100 poin</strong> ke {{ $total }} soal yang dipilih.</p>
                            <p class="mt-1 text-[10px] sm:text-xs">
                                <strong>Distribusi:</strong>
                                @if($remainder > 0)
                                    {{ $remainder }} soal × {{ $base + 1 }} poin + {{ $total - $remainder }} soal × {{ $base }} poin = 100 poin
                                @else
                                    {{ $total }} soal × {{ $base }} poin = 100 poin
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3 sm:space-y-4 max-h-[600px] overflow-y-auto pr-2">
                        @foreach($importedQuestions as $index => $question)
                            @php
                                $autoPoints = $base + ($index < $remainder ? 1 : 0);
                            @endphp
                            <div class="border border-gray-200 rounded-xl p-3 sm:p-5 hover:shadow-md transition bg-white">
                                <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                                    <div class="flex flex-wrap items-center space-x-2 sm:space-x-3">
                                        <input type="checkbox" name="questions[{{ $index }}][import]" value="1" checked
                                               class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 rounded focus:ring-green-500">
                                        <span class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-xs sm:text-sm">
                                            {{ $question['order_number'] }}
                                        </span>
                                        <select name="questions[{{ $index }}][question_type]"
                                                class="px-2 py-0.5 sm:px-3 sm:py-1 bg-green-100 text-green-700 text-[10px] sm:text-xs font-semibold rounded-full border-0 focus:ring-2 focus:ring-green-500">
                                            <option value="multiple_choice" {{ $question['question_type'] === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                            <option value="short_answer" {{ $question['question_type'] === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                            <option value="true_false" {{ $question['question_type'] === 'true_false' ? 'selected' : '' }}>True/False</option>
                                            <option value="essay" {{ $question['question_type'] === 'essay' ? 'selected' : '' }}>Essay</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <label class="text-[10px] sm:text-xs text-gray-500">Points:</label>
                                        <input type="number"
                                               name="questions[{{ $index }}][points]"
                                               value="{{ $autoPoints }}"
                                               min="1"
                                               class="w-16 sm:w-20 px-2 sm:px-3 py-1 border rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-green-500">
                                    </div>
                                </div>

                                <input type="hidden" name="questions[{{ $index }}][order_number]" value="{{ $question['order_number'] }}">

                                <textarea name="questions[{{ $index }}][question_text]" rows="2"
                                          class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg mb-3 focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">{{ $question['question_text'] }}</textarea>

                                @if(!empty($question['options']))
                                    <div class="bg-gray-50 rounded-lg p-2 sm:p-3 mb-3 space-y-1.5 sm:space-y-2">
                                        <p class="text-[10px] sm:text-xs font-semibold text-gray-600">Pilihan Jawaban:</p>
                                        @foreach($question['options'] as $optIndex => $option)
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 sm:w-6 sm:h-6 bg-green-100 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold text-green-700">
                                                    {{ chr(65 + $optIndex) }}
                                                </span>
                                                <input type="text" name="questions[{{ $index }}][options][{{ $optIndex }}]"
                                                       value="{{ $option }}" class="flex-1 px-2 sm:px-3 py-1 border rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-green-500">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mb-2">
                                    <label class="text-[10px] sm:text-xs font-semibold text-gray-600 flex flex-wrap items-center gap-1">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Jawaban Benar:
                                        @if($question['question_type'] === 'essay')
                                            <span class="ml-1 text-[10px] sm:text-xs text-amber-600 font-normal italic">
                                                (Essay dinilai manual)
                                            </span>
                                        @endif
                                    </label>

                                    @if($question['question_type'] === 'essay')
                                        <div class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-lg text-xs sm:text-sm text-gray-500 italic">
                                            - <span class="text-[10px]">(Jawaban essay dinilai manual)</span>
                                        </div>
                                        <input type="hidden"
                                            name="questions[{{ $index }}][correct_answer]"
                                            value="{{ $question['correct_answer'] }}">
                                    @else
                                        <textarea name="questions[{{ $index }}][correct_answer]"
                                                rows="{{ $question['question_type'] === 'short_answer' ? 1 : 2 }}"
                                                class="w-full px-3 py-2 border border-green-300 bg-green-50 rounded-lg text-xs sm:text-sm mt-1 focus:ring-2 focus:ring-green-500">{{ $question['correct_answer'] }}</textarea>
                                    @endif
                                </div>

                                <div>
                                    <label class="text-[10px] sm:text-xs font-semibold text-gray-600">Pembahasan (Opsional):</label>
                                    <input type="text" name="questions[{{ $index }}][explanation]"
                                           value="{{ $question['explanation'] ?? '' }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm mt-1 focus:ring-2 focus:ring-green-500">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-center pt-4 sm:pt-6 mt-4 sm:mt-6 border-t gap-3 sm:gap-0">
                        <a href="{{ route('hr.quizzes.questions.import.cancel', $quiz) }}"
                           onclick="return confirm('Batalkan import? Data yang sudah diparse akan dihapus.')"
                           class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-red-50 hover:border-red-300 hover:text-red-600 transition font-medium text-center text-sm">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batalkan Import
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium text-sm flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save All Questions
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

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
