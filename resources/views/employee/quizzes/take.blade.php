@extends('layouts.app')

@section('title', 'Taking: ' . $quiz->title)

@section('content')
<div class="py-4">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Quiz Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h2 class="text-xl font-bold">{{ $quiz->title }}</h2>
                        <p class="text-indigo-200 text-sm mt-1">
                            Question <span id="currentQuestionNum">1</span> of {{ $questions->count() }}
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 bg-white/20 backdrop-blur-sm rounded-xl px-6 py-3">
                        <div class="text-3xl font-bold text-center" id="timer">
                            <span id="minutes">{{ floor($remainingSeconds / 60) }}</span>:<span id="seconds">{{ sprintf('%02d', $remainingSeconds % 60) }}</span>
                        </div>
                        <p class="text-xs text-center text-indigo-200">Time Remaining</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-white/20 rounded-full h-2 mt-4">
                    <div id="progressBar" class="bg-white h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

            <!-- Questions -->
            <form id="quizForm" action="{{ route('employee.quizzes.submit', $attempt->id) }}" method="POST" class="p-6">
                @csrf

                <div id="questionsContainer" class="min-h-[400px]">
                    @foreach($questions as $index => $question)
                        <div class="question-slide" data-question="{{ $index + 1 }}" style="{{ $index === 0 ? '' : 'display: none;' }}">
                            <div class="mb-6">
                                <div class="flex items-center space-x-3 mb-4">
                                    <span class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">
                                        {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                    </span>
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                        {{ $question->points }} pts
                                    </span>
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $question->question_text }}</h3>

                                @if($question->question_image)
                                    <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question" class="mb-4 rounded-xl max-w-full">
                                @endif

                                {{-- ✅ PERBAIKAN: Null-safe untuk options --}}
                                @if($question->question_type === 'multiple_choice')
                                    @if(!empty($question->options) && is_array($question->options))
                                        <div class="space-y-3">
                                            @foreach($question->options as $key => $option)
                                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}"
                                                        class="w-5 h-5 text-indigo-600 focus:ring-indigo-500"
                                                        onchange="saveAnswer({{ $question->id }}, '{{ addslashes($option) }}')">
                                                    <span class="ml-3 font-semibold text-gray-500 mr-2">{{ chr(65 + $key) }}.</span>
                                                    <span class="text-gray-700">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-yellow-800 text-sm">
                                            <strong>⚠️ Warning:</strong> Soal ini tidak memiliki pilihan jawaban. Silakan hubungi admin.
                                        </div>
                                    @endif
                                @elseif($question->question_type === 'true_false')
                                    <div class="space-y-3">
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-green-400 hover:bg-green-50 transition-all duration-200">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="True"
                                                class="w-5 h-5 text-green-600 focus:ring-green-500"
                                                onchange="saveAnswer({{ $question->id }}, 'True')">
                                            <span class="ml-3 text-lg">✅ True</span>
                                        </label>
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-400 hover:bg-red-50 transition-all duration-200">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="False"
                                                class="w-5 h-5 text-red-600 focus:ring-red-500"
                                                onchange="saveAnswer({{ $question->id }}, 'False')">
                                            <span class="ml-3 text-lg">❌ False</span>
                                        </label>
                                    </div>
                                @elseif($question->question_type === 'short_answer')
                                    <input type="text" name="answers[{{ $question->id }}]"
                                        class="w-full px-4 py-3 text-lg rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                        placeholder="Type your answer here..."
                                        onchange="saveAnswer({{ $question->id }}, this.value)">
                                @elseif($question->question_type === 'essay')
                                    <textarea name="answers[{{ $question->id }}]" rows="8"
                                        class="w-full px-4 py-3 text-lg rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                        placeholder="Write your essay answer here..."
                                        onchange="saveAnswer({{ $question->id }}, this.value)"></textarea>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Navigation -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" id="prevBtn" onclick="navigateQuestion(-1)"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-medium disabled:opacity-50 w-full sm:w-auto"
                        disabled>
                        ← Previous
                    </button>

                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach($questions as $index => $question)
                            <button type="button" onclick="goToQuestion({{ $index + 1 }})"
                                class="w-10 h-10 rounded-xl text-sm font-semibold question-dot bg-gray-200 text-gray-600 hover:bg-indigo-200 transition-all duration-200"
                                data-question="{{ $index + 1 }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <button type="button" id="nextBtn" onclick="navigateQuestion(1)"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium w-full sm:w-auto">
                        Next →
                    </button>
                </div>

                <!-- Submit -->
                <div class="mt-6 text-center">
                    <button type="button" onclick="submitQuiz()"
                        class="px-10 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-bold text-lg">
                        Submit Quiz ✓
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentQuestion = 1;
    const totalQuestions = {{ $questions->count() }};
    // ✅ PERBAIKAN: Paksa jadi integer dengan Math.floor
    let timeLeft = Math.floor({{ (int) $remainingSeconds }});
    const attemptId = {{ $attempt->id }};

    function updateTimer() {
        if (timeLeft <= 0) {
            timeLeft = 0;
            updateTimerDisplay();
            // Auto submit setelah 1 detik
            setTimeout(() => {
                document.getElementById('quizForm').submit();
            }, 1000);
            return;
        }
        timeLeft--;
        updateTimerDisplay();
    }

    function updateTimerDisplay() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        // ✅ Tampilkan dengan format yang benar
        document.getElementById('minutes').textContent = minutes;
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        
        // Efek merah saat waktu hampir habis
        const timerEl = document.getElementById('timer');
        if (timeLeft <= 60 && timeLeft > 0) {
            timerEl.classList.add('text-red-300');
            timerEl.classList.remove('text-white');
        } else if (timeLeft <= 0) {
            timerEl.classList.add('text-red-500');
        }
    }

    // Jalankan timer setiap 1 detik
    setInterval(updateTimer, 1000);

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
            nextBtn.textContent = 'Finish';
            nextBtn.className = 'px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium w-full sm:w-auto';
        } else {
            nextBtn.textContent = 'Next →';
            nextBtn.className = 'px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium w-full sm:w-auto';
        }

        document.querySelectorAll('.question-dot').forEach(dot => {
            dot.classList.remove('bg-indigo-600', 'text-white');
            dot.classList.add('bg-gray-200', 'text-gray-600');
        });

        const activeDot = document.querySelector(`.question-dot[data-question="${questionNum}"]`);
        if (activeDot) {
            activeDot.classList.remove('bg-gray-200', 'text-gray-600');
            activeDot.classList.add('bg-indigo-600', 'text-white');
        }

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
        });
    }

    function submitQuiz() {
        if (confirm('Are you sure you want to submit your quiz? This action cannot be undone.')) {
            document.getElementById('quizForm').submit();
        }
    }

    // Inisialisasi tampilan pertama
    showQuestion(1);
    updateTimerDisplay();
</script>
@endpush