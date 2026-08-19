@extends('layouts.hr')

@section('title', 'Add Question - ' . $quiz->title)
@section('header-title', 'Add Question')
@section('header-subtitle', 'Add a new question to ' . $quiz->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Questions
    </a>
</div>

<div class="max-w-4xl">
    <div class="hr-card overflow-hidden">
        {{-- Tabs --}}
        <div class="border-b border-slate-100/80" x-data="{ tab: 'manual' }">
            <div class="flex">
                <button @click="tab = 'manual'" :class="tab === 'manual' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-6 py-3 text-sm font-semibold border-b-2 transition-all duration-200">
                    Manual Input
                </button>
                <button @click="tab = 'pdf'" :class="tab === 'pdf' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-6 py-3 text-sm font-semibold border-b-2 transition-all duration-200">
                    Import from PDF
                </button>
            </div>

            {{-- Manual Tab --}}
            <div x-show="tab === 'manual'" class="p-6 sm:p-8">
                <form action="{{ route('hr.quizzes.questions.store', $quiz) }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="question_type" class="block text-sm font-semibold text-slate-700 mb-2">Question Type</label>
                            <select name="question_type" id="question_type" class="hr-input">
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="true_false">True/False</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>
                        <div>
                            <label for="order_number" class="block text-sm font-semibold text-slate-700 mb-2">Order Number</label>
                            <input type="number" name="order_number" id="order_number" value="{{ old('order_number', $nextOrder ?? 1) }}" min="1" class="hr-input">
                        </div>
                    </div>

                    <div>
                        <label for="question_text" class="block text-sm font-semibold text-slate-700 mb-2">Question Text</label>
                        <textarea name="question_text" id="question_text" rows="3" class="hr-input" placeholder="Enter the question text" required>{{ old('question_text') }}</textarea>
                        @error('question_text')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="question_image" class="block text-sm font-semibold text-slate-700 mb-2">Question Image (optional)</label>
                        <input type="file" name="question_image" id="question_image" accept="image/*" class="hr-input">
                    </div>

                    <div id="options-container" class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-700">Options</label>
                        <div class="flex items-center gap-3">
                            <span class="hr-avatar w-8 h-8 text-xs">A</span>
                            <input type="text" name="options[]" class="flex-1 hr-input" placeholder="Option A">
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="hr-avatar w-8 h-8 text-xs">B</span>
                            <input type="text" name="options[]" class="flex-1 hr-input" placeholder="Option B">
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="hr-avatar w-8 h-8 text-xs">C</span>
                            <input type="text" name="options[]" class="flex-1 hr-input" placeholder="Option C">
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="hr-avatar w-8 h-8 text-xs">D</span>
                            <input type="text" name="options[]" class="flex-1 hr-input" placeholder="Option D">
                        </div>
                    </div>

                    <div>
                        <label for="correct_answer" class="block text-sm font-semibold text-slate-700 mb-2">Correct Answer</label>
                        <textarea name="correct_answer" id="correct_answer" rows="2" class="hr-input" placeholder="Enter the correct answer" required>{{ old('correct_answer') }}</textarea>
                        @error('correct_answer')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="explanation" class="block text-sm font-semibold text-slate-700 mb-2">Explanation (optional)</label>
                        <textarea name="explanation" id="explanation" rows="2" class="hr-input" placeholder="Explain the correct answer">{{ old('explanation') }}</textarea>
                    </div>

                    <div>
                        <label for="points" class="block text-sm font-semibold text-slate-700 mb-2">Points</label>
                        <input type="number" name="points" id="points" value="{{ old('points', 10) }}" min="1" class="hr-input w-32">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100/80">
                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="hr-btn-secondary">Cancel</a>
                        <button type="submit" class="hr-btn-primary">Add Question</button>
                    </div>
                </form>
            </div>

            {{-- PDF Import Tab --}}
            <div x-show="tab === 'pdf'" class="p-6 sm:p-8"
                 x-data="{
                     dragging: false,
                     fileName: '',
                     fileSize: '',
                     fileReady: false,
                     uploading: false,
                     handleDrop(e) {
                         this.dragging = false;
                         const files = e.dataTransfer.files;
                         if (files.length && files[0].type === 'application/pdf') {
                             this.setFile(files[0]);
                             this.$refs.pdfInput.files = files;
                         }
                     },
                     handleSelect(e) {
                         if (e.target.files.length) {
                             this.setFile(e.target.files[0]);
                         }
                     },
                     setFile(file) {
                         this.fileName = file.name;
                         this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
                         this.fileReady = true;
                     },
                     clearFile() {
                         this.fileName = '';
                         this.fileSize = '';
                         this.fileReady = false;
                         this.$refs.pdfInput.value = '';
                     }
                 }">
                <form action="{{ route('hr.quizzes.questions.import', $quiz) }}" method="POST" enctype="multipart/form-data"
                      @submit="uploading = true" class="space-y-6">
                    @csrf

                    {{-- Info Banner --}}
                    <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-200/60 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <p class="font-semibold text-primary-800">Import Soal dari PDF</p>
                            <p class="text-primary-700/80 mt-0.5">Upload file PDF berisi soal. Sistem akan otomatis mendeteksi dan memparse soal untuk Anda review sebelum disimpan.</p>
                        </div>
                    </div>

                    {{-- Drop Zone --}}
                    <div class="relative"
                         x-show="!fileReady"
                         @dragover.prevent="dragging = true"
                         @dragleave.prevent="dragging = false"
                         @drop.prevent="handleDrop($event)">
                        <label for="pdf_file" class="block cursor-pointer">
                            <div class="import-dropzone rounded-2xl border-2 border-dashed p-8 sm:p-12 text-center transition-all duration-300"
                                 :class="dragging ? 'border-primary-400 bg-primary-50/60 scale-[1.01]' : 'border-slate-200 bg-slate-50/50 hover:border-primary-300 hover:bg-primary-50/30'">
                                <div class="w-16 h-16 mx-auto mb-5 rounded-2xl flex items-center justify-center transition-all duration-300"
                                     :class="dragging ? 'bg-primary-200 scale-110' : 'bg-primary-100'">
                                    <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-slate-700 mb-1">
                                    <span class="text-primary-600">Klik untuk upload</span> atau seret file ke sini
                                </p>
                                <p class="text-sm text-slate-400">Format: PDF (Maks. 10 MB)</p>
                            </div>
                        </label>
                        <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" required
                               x-ref="pdfInput" @change="handleSelect($event)" class="hidden">
                    </div>

                    {{-- Selected File Card --}}
                    <div x-show="fileReady" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="hidden" :class="fileReady && 'block'">
                        <div class="flex items-center gap-4 p-4 bg-white border border-emerald-200/80 rounded-2xl shadow-soft">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate" x-text="fileName"></p>
                                <p class="text-xs text-slate-400 mt-0.5" x-text="fileSize"></p>
                            </div>
                            <button type="button" @click="clearFile()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-red-50 flex items-center justify-center text-slate-400 hover:text-red-500 transition-all duration-200 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Format Guidelines --}}
                    <div class="bg-slate-50/80 border border-slate-200/60 rounded-2xl overflow-hidden">
                        <button type="button" @click="$el.closest('.bg-slate-50\\/80').querySelector('.format-guide-content').classList.toggle('hidden')" class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-slate-100/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-700">Format PDF yang Didukung</span>
                            </div>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="format-guide-content hidden px-5 pb-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Header Tipe Soal</h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="hr-badge hr-badge--info text-[11px]">PG</span>
                                            <span class="text-xs text-slate-600">Pilihan Ganda / Multiple Choice</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="hr-badge hr-badge--danger text-[11px]">B/S</span>
                                            <span class="text-xs text-slate-600">Benar Salah / True False</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="hr-badge hr-badge--warning text-[11px]">Essay</span>
                                            <span class="text-xs text-slate-600">Essay / Uraian</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="hr-badge hr-badge--neutral text-[11px]">Singkat</span>
                                            <span class="text-xs text-slate-600">Jawaban Singkat / Short Answer</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Format Penulisan</h4>
                                    <div class="bg-white rounded-xl p-3 border border-slate-200/60 font-mono text-xs text-slate-600 space-y-1.5 leading-relaxed">
                                        <p><span class="text-primary-600 font-bold">1.</span> Soal pertanyaan di sini</p>
                                        <p class="pl-4"><span class="text-emerald-600 font-bold">a.</span> Pilihan A</p>
                                        <p class="pl-4"><span class="text-emerald-600 font-bold">b.</span> Pilihan B</p>
                                        <p class="pl-4"><span class="text-emerald-600 font-bold">c.</span> Pilihan C</p>
                                        <p class="pl-4"><span class="text-emerald-600 font-bold">d.</span> Pilihan D</p>
                                        <p><span class="text-amber-600 font-bold">Kunci:</span> a</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @error('pdf_file')
                        <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200/80 rounded-2xl">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                        </div>
                    @enderror

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100/80">
                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="hr-btn-secondary">Cancel</a>
                        <button type="submit" class="hr-btn-primary" :disabled="!fileReady || uploading" :class="(!fileReady || uploading) && 'opacity-50 cursor-not-allowed'">
                            <template x-if="!uploading">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Import dari PDF
                                </span>
                            </template>
                            <template x-if="uploading">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Memproses PDF...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
