@extends('layouts.app')

@section('title', 'Add Question')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 sm:px-8 py-4 sm:py-6 bg-gradient-to-r from-indigo-600 to-purple-600">
                <h2 class="text-xl sm:text-2xl font-bold text-white">Add Question</h2>
                <p class="text-indigo-100 text-sm mt-1">to {{ $quiz->title }}</p>
            </div>

            {{-- Tab Navigation --}}
            <div class="border-b border-gray-200 overflow-x-auto">
                <nav class="flex -mb-px min-w-max">
                    <button onclick="switchTab('manual')" id="tab-manual"
                            class="tab-button px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium border-b-2 border-indigo-600 text-indigo-600 whitespace-nowrap">
                        ✍️ Manual Input
                    </button>
                    <button onclick="switchTab('import')" id="tab-import"
                            class="tab-button px-4 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        📄 Import from PDF
                    </button>
                </nav>
            </div>

            {{-- Tab Content: Manual Input --}}
            <div id="content-manual" class="tab-content">
                <form action="{{ route('hr.quizzes.questions.store', $quiz) }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-8 space-y-4 sm:space-y-6" id="questionForm">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                        <div>
                            <label for="question_type" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Question Type</label>
                            <select name="question_type" id="question_type" required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base">
                                <option value="">Select Type</option>
                                <option value="multiple_choice" {{ old('question_type') === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                <option value="true_false" {{ old('question_type') === 'true_false' ? 'selected' : '' }}>True / False</option>
                                <option value="short_answer" {{ old('question_type') === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                <option value="essay" {{ old('question_type') === 'essay' ? 'selected' : '' }}>Essay</option>
                            </select>
                        </div>
                        <div>
                            <label for="points" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Points</label>
                            <input type="number" name="points" id="points" value="{{ old('points', 1) }}" required min="1"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base">
                        </div>
                        <div>
                            <label for="order_number" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Order</label>
                            <input type="number" name="order_number" id="order_number" value="{{ old('order_number', $nextOrder) }}" required min="1"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base">
                        </div>
                    </div>

                    <div>
                        <label for="question_text" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Question Text</label>
                        <textarea name="question_text" id="question_text" rows="4" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base"
                            placeholder="Enter your question here...">{{ old('question_text') }}</textarea>
                        @error('question_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="question_image" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Question Image (Optional)</label>
                        <input type="file" name="question_image" id="question_image" accept="image/*"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base">
                    </div>

                    <div id="optionsContainer" style="display: none;">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 sm:mb-3">Answer Options</label>
                        <div class="space-y-2 sm:space-y-3">
                            @for($i = 0; $i < 4; $i++)
                                <div class="flex items-center space-x-2 sm:space-x-3">
                                    <span class="w-6 h-6 sm:w-8 sm:h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-700 font-bold text-xs sm:text-sm flex-shrink-0">
                                        {{ chr(65 + $i) }}
                                    </span>
                                    <input type="text" name="options[]"
                                        class="flex-1 px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base"
                                        placeholder="Option {{ chr(65 + $i) }}">
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div>
                        <label for="correct_answer" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Correct Answer</label>
                        <input type="text" name="correct_answer" id="correct_answer" value="{{ old('correct_answer') }}" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base"
                            placeholder="Enter the correct answer">
                        @error('correct_answer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="explanation" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Explanation (Optional)</label>
                        <textarea name="explanation" id="explanation" rows="3"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base"
                            placeholder="Explain why this is the correct answer">{{ old('explanation') }}</textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4 pt-3 sm:pt-4">
                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}"
                           class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium text-center text-sm sm:text-base">
                            Cancel
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium text-sm sm:text-base">
                            Add Question
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tab Content: Import from PDF --}}
            <div id="content-import" class="tab-content hidden">
                <form action="{{ route('hr.quizzes.questions.import', $quiz) }}"
                      method="POST" enctype="multipart/form-data" class="p-4 sm:p-8 space-y-4 sm:space-y-6">
                    @csrf

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-5">
                        <h3 class="font-bold text-blue-900 mb-2 sm:mb-3 flex items-center text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Format Panduan PDF
                        </h3>
                        <div class="text-xs sm:text-sm text-blue-800 space-y-3">
                            <p>Pastikan PDF mengikuti format berikut:</p>
                            <div class="bg-white p-3 sm:p-4 rounded-lg border border-blue-200 overflow-x-auto">
                                <pre class="text-[10px] sm:text-xs whitespace-pre-wrap text-gray-700">PG
1. Who is the president of America?
a. Joe Biden
b. Donald Trump
c. Barack Obama
d. George Bush
JAWAB: a

JAWABAN SINGKAT
2. What is the capital of France?
JAWAB: Paris

BENAR SALAH
3. The Earth is flat.
JAWAB: False</pre>
                            </div>
                            <div>
                                <p class="font-semibold mb-1 sm:mb-2">Header tipe soal yang didukung:</p>
                                <div class="grid grid-cols-2 gap-1 sm:gap-2 text-[10px] sm:text-xs">
                                    <div class="bg-white px-2 sm:px-3 py-1.5 sm:py-2 rounded border"><code>PG</code> atau <code>PILIHAN GANDA</code></div>
                                    <div class="bg-white px-2 sm:px-3 py-1.5 sm:py-2 rounded border"><code>JAWABAN SINGKAT</code> atau <code>ISIAN</code></div>
                                    <div class="bg-white px-2 sm:px-3 py-1.5 sm:py-2 rounded border"><code>BENAR SALAH</code> atau <code>TRUE FALSE</code></div>
                                    <div class="bg-white px-2 sm:px-3 py-1.5 sm:py-2 rounded border"><code>ESSAY</code> atau <code>URAIAN</code></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">
                            Upload PDF File <span class="text-red-500">*</span>
                        </label>
                        <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-xl p-6 sm:p-8 text-center hover:border-indigo-500 hover:bg-indigo-50/30 transition cursor-pointer">
                            <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf" required class="hidden">
                            <label for="pdf_file" class="cursor-pointer block">
                                <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-400 mb-2 sm:mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-gray-600 font-medium text-sm sm:text-base">Klik untuk upload atau drag & drop PDF</p>
                                <p class="text-xs sm:text-sm text-gray-400 mt-1">Maksimal 10MB</p>
                            </label>
                            <p id="file-name" class="mt-2 sm:mt-3 text-indigo-600 font-medium hidden text-sm"></p>
                        </div>
                        @error('pdf_file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4 pt-3 sm:pt-4">
                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}"
                           class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium text-center text-sm sm:text-base">
                            Cancel
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium text-sm sm:text-base flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Upload & Parse PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(el => {
            el.classList.remove('border-indigo-600', 'text-indigo-600');
            el.classList.add('border-transparent', 'text-gray-500');
        });

        document.getElementById('content-' + tab).classList.remove('hidden');
        const activeTab = document.getElementById('tab-' + tab);
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        activeTab.classList.add('border-indigo-600', 'text-indigo-600');
    }

    document.getElementById('question_type').addEventListener('change', function() {
        const optionsContainer = document.getElementById('optionsContainer');
        const correctAnswer = document.getElementById('correct_answer');

        if (this.value === 'multiple_choice') {
            optionsContainer.style.display = 'block';
            correctAnswer.placeholder = 'Enter the correct option text';
        } else if (this.value === 'true_false') {
            optionsContainer.style.display = 'none';
            correctAnswer.placeholder = 'Enter True or False';
        } else {
            optionsContainer.style.display = 'none';
            correctAnswer.placeholder = 'Enter the correct answer';
        }
    });

    document.getElementById('question_type').dispatchEvent(new Event('change'));

    const fileInput = document.getElementById('pdf_file');
    const fileName = document.getElementById('file-name');
    const dropZone = document.getElementById('dropZone');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                fileName.textContent = '📄 ' + this.files[0].name + ' (' + (this.files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
                fileName.classList.remove('hidden');
                dropZone.classList.add('border-indigo-500', 'bg-indigo-50/30');
            }
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.add('border-indigo-500', 'bg-indigo-50/50');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-indigo-500', 'bg-indigo-50/50');
            });
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type === 'application/pdf') {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            } else {
                alert('Hanya file PDF yang diperbolehkan!');
            }
        });
    }
</script>
@endpush
@endsection
