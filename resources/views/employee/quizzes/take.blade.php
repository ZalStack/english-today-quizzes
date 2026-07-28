@extends('layouts.app')

@section('title', 'Taking: ' . $quiz->title)

@section('content')
<div class="py-0">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <!-- Quiz Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-3 sm:p-6 text-white sticky top-0 z-10 rounded-b-2xl shadow-lg">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex-1 min-w-0">
                    <h2 class="text-base sm:text-xl font-bold truncate">{{ $quiz->title }}</h2>
                    <p class="text-indigo-200 text-xs sm:text-sm mt-1">
                        Question <span id="currentQuestionNum">1</span> of {{ $questions->count() }}
                    </p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-3 sm:px-6 py-1.5 sm:py-3 shrink-0">
                    <div class="text-xl sm:text-3xl font-bold text-center font-mono" id="timer">
                        <span id="minutes">{{ floor($remainingSeconds / 60) }}</span>:<span id="seconds">{{ sprintf('%02d', $remainingSeconds % 60) }}</span>
                    </div>
                    <p class="text-[8px] sm:text-xs text-center text-indigo-200">Time Remaining</p>
                </div>
            </div>
            <div class="w-full bg-white/20 rounded-full h-1.5 sm:h-2 mt-3 sm:mt-4">
                <div id="progressBar" class="bg-white h-1.5 sm:h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-4 mt-4">
            <!-- Questions Area -->
            <div class="flex-1 min-w-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <form id="quizForm" action="{{ route('employee.quizzes.submit', $attempt->id) }}" method="POST" class="p-3 sm:p-6">
                        @csrf

                        <div id="questionsContainer" class="min-h-[300px] sm:min-h-[400px]">
                            @php
                                $lastType = null;
                                $typeLabels = [
                                    'multiple_choice' => 'Multiple Choice',
                                    'true_false' => 'True / False',
                                    'short_answer' => 'Short Answer',
                                    'essay' => 'Essay',
                                ];
                            @endphp
                            @foreach($questions as $index => $question)
                                @php
                                    $showHeader = $lastType !== $question->question_type;
                                    $lastType = $question->question_type;
                                @endphp
                                <div class="question-slide" data-question="{{ $index + 1 }}" data-type="{{ $question->question_type }}" data-qid="{{ $question->id }}" data-answered="{{ in_array($question->id, $answeredQuestionIds) ? '1' : '0' }}" style="{{ $index === 0 ? '' : 'display: none;' }}">

                                    {{-- Section header --}}
                                    @if($showHeader)
                                        <div class="mb-4 sm:mb-6">
                                            <div class="flex items-center gap-3 mb-2">
                                                <div class="h-px flex-1 bg-gradient-to-r from-indigo-200 to-transparent"></div>
                                                <span class="px-3 py-1 sm:px-4 sm:py-1.5 bg-indigo-100 text-indigo-700 text-[10px] sm:text-xs font-bold rounded-full uppercase tracking-wider">
                                                    {{ $typeLabels[$question->question_type] ?? ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                                </span>
                                                <div class="h-px flex-1 bg-gradient-to-l from-indigo-200 to-transparent"></div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-3 sm:mb-4">
                                            <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-indigo-50 text-indigo-600 text-[10px] sm:text-xs font-semibold rounded-full inline-block">
                                                {{ $typeLabels[$question->question_type] ?? ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="mb-4 sm:mb-6">
                                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3 sm:mb-4">
                                            <div class="flex items-center gap-2 sm:gap-3">
                                                <span class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-sm sm:text-base shrink-0">
                                                    {{ $index + 1 }}
                                                </span>
                                                <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-green-100 text-green-700 text-[10px] sm:text-xs font-semibold rounded-full">
                                                    {{ $question->points }} pts
                                                </span>
                                            </div>
                                        </div>

                                        <h3 class="text-sm sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 leading-relaxed">{{ $question->question_text }}</h3>

                                        @if($question->question_image)
                                            <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question" class="mb-4 rounded-xl max-w-full h-auto">
                                        @endif

                                        @if($question->question_type === 'multiple_choice')
                                            @if(!empty($question->options) && is_array($question->options))
                                                <div class="space-y-2 sm:space-y-3">
                                                    @foreach($question->options as $key => $option)
                                                        <label class="mc-option flex items-center p-3 sm:p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200">
                                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}"
                                                                class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600 focus:ring-indigo-500 shrink-0"
                                                                onchange="saveAnswer({{ $question->id }}, '{{ addslashes($option) }}'); highlightSelectedOption(this);">
                                                            <span class="ml-2 sm:ml-3 font-semibold text-gray-500 mr-1 sm:mr-2 text-sm sm:text-base">{{ chr(65 + $key) }}.</span>
                                                            <span class="text-sm sm:text-base text-gray-700">{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 sm:p-4 text-yellow-800 text-xs sm:text-sm">
                                                    <strong>Warning:</strong> Soal ini tidak memiliki pilihan jawaban. Silakan hubungi admin.
                                                </div>
                                            @endif
                                        @elseif($question->question_type === 'true_false')
                                            <div class="space-y-2 sm:space-y-3">
                                                <label class="tf-option flex items-center p-3 sm:p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-400 hover:bg-purple-50 transition-all duration-200">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="True"
                                                        class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 focus:ring-purple-500 shrink-0"
                                                        onchange="saveAnswer({{ $question->id }}, 'True'); highlightSelectedOption(this);">
                                                    <span class="ml-2 sm:ml-3 text-sm sm:text-lg">True</span>
                                                </label>
                                                <label class="tf-option flex items-center p-3 sm:p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-400 hover:bg-purple-50 transition-all duration-200">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="False"
                                                        class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 focus:ring-purple-500 shrink-0"
                                                        onchange="saveAnswer({{ $question->id }}, 'False'); highlightSelectedOption(this);">
                                                    <span class="ml-2 sm:ml-3 text-sm sm:text-lg">False</span>
                                                </label>
                                            </div>
                                        @elseif($question->question_type === 'short_answer')
                                            <input type="text" name="answers[{{ $question->id }}]"
                                                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-lg rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                                placeholder="Type your answer here..."
                                                onchange="saveAnswer({{ $question->id }}, this.value)">
                                        @elseif($question->question_type === 'essay')
                                            <textarea name="answers[{{ $question->id }}]" rows="6"
                                                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-lg rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                                placeholder="Write your essay answer here..."
                                                onchange="saveAnswer({{ $question->id }}, this.value)"></textarea>
                                        @endif
                                    </div>

                                    {{-- Transition button --}}
                                    @php
                                        $nextIndex = $index + 1;
                                        $nextQuestion = $questions->get($nextIndex);
                                        $isLastOfType = $nextQuestion && $nextQuestion->question_type !== $question->question_type;
                                    @endphp
                                    @if($isLastOfType && $nextQuestion)
                                        <div class="my-4 sm:my-6 text-center">
                                            <div class="border-t border-dashed border-gray-300 mb-3 sm:mb-4"></div>
                                            <button type="button" onclick="goToQuestion({{ $nextIndex + 1 }})"
                                                class="px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold text-xs sm:text-sm">
                                                Next Section: {{ $typeLabels[$nextQuestion->question_type] ?? ucfirst(str_replace('_', ' ', $nextQuestion->question_type)) }} →
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigation -->
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-4 sm:mt-6 pt-3 sm:pt-4 border-t border-gray-200">
                            <button type="button" id="prevBtn" onclick="navigateQuestion(-1)"
                                class="w-full sm:w-auto px-3 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-medium disabled:opacity-50 text-xs sm:text-base"
                                disabled>
                                ← Previous
                            </button>

                            <button type="button" id="nextBtn" onclick="navigateQuestion(1)"
                                class="w-full sm:w-auto px-3 sm:px-6 py-2 sm:py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium text-xs sm:text-base">
                                Next →
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <div class="w-full lg:w-56 xl:w-64 shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 sm:p-4 lg:sticky lg:top-28">
                    <h4 class="text-[10px] sm:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 sm:mb-3">Soal</h4>

                    @php
                        $groupedByType = $questions->groupBy('question_type');
                        $typeFullLabels = [
                            'multiple_choice' => 'MC',
                            'true_false' => 'TF',
                            'short_answer' => 'SA',
                            'essay' => 'Essay',
                        ];
                        $typeColors = [
                            'multiple_choice' => 'text-indigo-700',
                            'true_false' => 'text-green-700',
                            'short_answer' => 'text-orange-700',
                            'essay' => 'text-purple-700',
                        ];
                        $globalIndex = 0;
                    @endphp
                    <div id="questionNav" class="space-y-2 sm:space-y-3 max-h-[300px] sm:max-h-[420px] overflow-y-auto">
                        @foreach($groupedByType as $type => $typeQuestions)
                            <div>
                                <p class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider mb-1 {{ $typeColors[$type] ?? 'text-gray-500' }}">
                                    {{ $typeFullLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}
                                </p>
                                <div class="grid grid-cols-5 gap-1">
                                    @foreach($typeQuestions as $question)
                                        @php $globalIndex++; @endphp
                                        <button type="button" onclick="goToQuestion({{ $globalIndex }})"
                                            class="question-dot w-full aspect-square text-[10px] sm:text-xs font-bold bg-gray-200 text-gray-600 hover:bg-indigo-200 hover:text-indigo-700 transition-all duration-200 rounded-lg"
                                            data-question="{{ $globalIndex }}">
                                            {{ $globalIndex }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-2 sm:mt-3 flex items-center gap-3 sm:gap-4 text-[9px] sm:text-[11px] text-gray-500">
                        <span class="flex items-center gap-1">
                            <span class="inline-block w-2.5 h-2.5 sm:w-3 sm:h-3 rounded bg-green-500"></span>
                            Terjawab
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="inline-block w-2.5 h-2.5 sm:w-3 sm:h-3 rounded bg-gray-200"></span>
                            Kosong
                        </span>
                    </div>

                    <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-100 flex flex-col gap-2">
                        <button type="button" onclick="submitQuiz()"
                            class="flex items-center justify-center gap-2 w-full px-2 sm:px-3 py-1.5 sm:py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:shadow-md transition-all duration-300 font-bold text-[10px] sm:text-sm">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentQuestion = 1;
    const totalQuestions = {{ $questions->count() }};
    const attemptId = {{ $attempt->id }};
    let timeLeft = Math.floor({{ (int) $remainingSeconds }});
    const answeredSet = new Set();
    let isAutoSubmitting = false;

    function updateTimer() {
        if (timeLeft <= 0) {
            timeLeft = 0;
            updateTimerDisplay();
            isAutoSubmitting = true;
            setTimeout(() => {
                document.getElementById('quizForm').submit();
            }, 500);
            return;
        }
        timeLeft--;
        updateTimerDisplay();
    }

    function updateTimerDisplay() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;

        document.getElementById('minutes').textContent = minutes;
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');

        const timerEl = document.getElementById('timer');
        timerEl.classList.remove('text-red-300', 'text-red-500', 'text-white');
        if (timeLeft <= 60 && timeLeft > 0) {
            timerEl.classList.add('text-red-300');
        } else if (timeLeft <= 0) {
            timerEl.classList.add('text-red-500');
        } else {
            timerEl.classList.add('text-white');
        }
    }

    setInterval(updateTimer, 1000);

    function updateDots() {
        document.querySelectorAll('.question-dot').forEach(dot => {
            const qNum = parseInt(dot.dataset.question);
            const isActive = qNum === currentQuestion;
            const slide = document.querySelector(`.question-slide[data-question="${qNum}"]`);
            const isAnswered = !!slide && slide.dataset.answered === '1';

            dot.classList.remove(
                'bg-indigo-600', 'bg-green-500', 'bg-gray-200',
                'text-white', 'text-gray-600',
                'ring-2', 'ring-indigo-700', 'ring-offset-1'
            );

            // Warna dasar: hijau kalau sudah dijawab, abu-abu kalau masih kosong
            if (isAnswered) {
                dot.classList.add('bg-green-500', 'text-white');
            } else {
                dot.classList.add('bg-gray-200', 'text-gray-600');
            }

            // Soal yang sedang dibuka diberi cincin penanda, warna dasar tetap menunjukkan status jawaban
            if (isActive) {
                dot.classList.add('ring-2', 'ring-indigo-700', 'ring-offset-1');
            }
        });
    }

    function highlightSelectedOption(inputEl) {
        const name = inputEl.name;
        const isTrueFalse = inputEl.closest('.question-slide')?.dataset.type === 'true_false';
        const selectedClasses = isTrueFalse
            ? ['border-purple-500', 'bg-purple-50', 'ring-2', 'ring-purple-400']
            : ['border-indigo-500', 'bg-indigo-50', 'ring-2', 'ring-indigo-400'];

        document.querySelectorAll(`input[name="${CSS.escape(name)}"]`).forEach(radio => {
            const label = radio.closest('label');
            if (!label) return;
            label.classList.remove(
                'border-purple-500', 'bg-purple-50', 'ring-2', 'ring-purple-400',
                'border-indigo-500', 'bg-indigo-50', 'ring-2', 'ring-indigo-400'
            );
        });

        const selectedLabel = inputEl.closest('label');
        if (selectedLabel) {
            selectedLabel.classList.add(...selectedClasses);
        }
    }

    function navigateQuestion(direction) {
        const newQuestion = currentQuestion + direction;
        if (newQuestion >= 1 && newQuestion <= totalQuestions) {
            showQuestion(newQuestion);
        }
    }

    function goToQuestion(questionNum) {
        showQuestion(questionNum);
    }

    function showQuestion(questionNum) {
        document.querySelectorAll('.question-slide').forEach(slide => {
            slide.style.display = 'none';
        });
        const targetSlide = document.querySelector(`.question-slide[data-question="${questionNum}"]`);
        if (targetSlide) targetSlide.style.display = 'block';

        currentQuestion = questionNum;
        document.getElementById('currentQuestionNum').textContent = questionNum;
        document.getElementById('prevBtn').disabled = questionNum === 1;

        const nextBtn = document.getElementById('nextBtn');
        if (questionNum === totalQuestions) {
            nextBtn.textContent = 'Submit';
            nextBtn.className = 'w-full sm:w-auto px-3 sm:px-6 py-2 sm:py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium text-xs sm:text-base';
            nextBtn.onclick = submitQuiz;
        } else {
            nextBtn.textContent = 'Next →';
            nextBtn.className = 'w-full sm:w-auto px-3 sm:px-6 py-2 sm:py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium text-xs sm:text-base';
            nextBtn.onclick = function() { navigateQuestion(1); };
        }

        updateDots();
        document.getElementById('progressBar').style.width = (questionNum / totalQuestions) * 100 + '%';
    }

    function saveAnswer(questionId, answer) {
        fetch(`/employee/quizzes/take/${attemptId}/save`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ question_id: questionId, answer: answer }),
        }).then(() => {
            answeredSet.add(questionId);
            updateDotAnswered(questionId);
        });
    }

    function updateDotAnswered(questionId) {
        const slide = document.querySelector(`.question-slide[data-qid="${questionId}"]`);
        if (slide) {
            slide.dataset.answered = '1';
        }
        updateDots();
    }

    function getUnansweredCount() {
        let count = 0;
        for (let i = 1; i <= totalQuestions; i++) {
            const slide = document.querySelector(`.question-slide[data-question="${i}"]`);
            if (!slide || slide.dataset.answered !== '1') {
                count++;
            }
        }
        return count;
    }

    function submitQuiz() {
        if (isAutoSubmitting) {
            document.getElementById('quizForm').submit();
            return;
        }

        const unanswered = getUnansweredCount();
        if (unanswered > 0) {
            if (!confirm(`Ada ${unanswered} soal yang belum dijawab.\n\nSoal yang tidak dijawab akan dianggap salah.\n\nTetap kirim jawaban?`)) {
                return;
            }
        } else {
            if (!confirm('Yakin ingin mengumpulkan jawaban? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }
        }
        document.getElementById('quizForm').submit();
    }

    // Pre-populate answeredSet
    document.querySelectorAll('.question-slide').forEach(slide => {
        if (slide.dataset.answered === '1') {
            answeredSet.add(parseInt(slide.dataset.qid));
        }
    });

    // Pre-populate highlight untuk opsi yang sudah tercentang (mis. saat reload halaman)
    document.querySelectorAll('.mc-option input:checked, .tf-option input:checked').forEach(input => {
        highlightSelectedOption(input);
    });

    showQuestion(1);
    updateTimer();
</script>
@endpush
@endsection
