@extends('layouts.app')

@section('title', 'Taking Quiz: ' . $quiz->title)

@section('content')
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Quiz Header -->
                    <div class="flex justify-between items-center mb-6 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $quiz->title }}</h2>
                            <p class="text-sm text-gray-600">Question <span id="currentQuestionNum">1</span> of
                                {{ $questions->count() }}</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600" id="timer">
                                <span id="minutes">{{ floor($remainingSeconds / 60) }}</span>:<span
                                    id="seconds">{{ sprintf('%02d', $remainingSeconds % 60) }}</span>
                            </div>
                            <p class="text-sm text-gray-500">Time Remaining</p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                        <div id="progressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                            style="width: 0%"></div>
                    </div>

                    <!-- Questions Container -->
                    <form id="quizForm" action="{{ route('employee.quizzes.submit', $attempt->id) }}" method="POST">
                        @csrf
                        <div id="questionsContainer">
                            @foreach ($questions as $index => $question)
                                <div class="question-slide" data-question="{{ $index + 1 }}"
                                    style="{{ $index === 0 ? '' : 'display: none;' }}">
                                    <div class="bg-white border rounded-lg p-6 mb-4">
                                        <h3 class="text-lg font-semibold mb-4">
                                            {{ $index + 1 }}. {{ $question->question_text }}
                                        </h3>

                                        @if ($question->question_image)
                                            <img src="{{ asset('storage/' . $question->question_image) }}"
                                                alt="Question Image" class="mb-4 max-w-full rounded-lg">
                                        @endif

                                        @if ($question->question_type === 'multiple_choice')
                                            <div class="space-y-3">
                                                @foreach ($question->options as $key => $option)
                                                    <label
                                                        class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                                        <input type="radio" name="answers[{{ $question->id }}]"
                                                            value="{{ $option }}"
                                                            class="mr-3 text-indigo-600 focus:ring-indigo-500"
                                                            onchange="saveAnswer({{ $question->id }}, '{{ $option }}')">
                                                        <span class="font-semibold mr-2">{{ chr(65 + $key) }}.</span>
                                                        {{ $option }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif($question->question_type === 'true_false')
                                            <div class="space-y-3">
                                                <label
                                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                                    <input type="radio" name="answers[{{ $question->id }}]"
                                                        value="True" class="mr-3 text-indigo-600 focus:ring-indigo-500"
                                                        onchange="saveAnswer({{ $question->id }}, 'True')">
                                                    True
                                                </label>
                                                <label
                                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                                    <input type="radio" name="answers[{{ $question->id }}]"
                                                        value="False" class="mr-3 text-indigo-600 focus:ring-indigo-500"
                                                        onchange="saveAnswer({{ $question->id }}, 'False')">
                                                    False
                                                </label>
                                            </div>
                                        @elseif($question->question_type === 'short_answer')
                                            <input type="text" name="answers[{{ $question->id }}]"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="Type your answer here..."
                                                onchange="saveAnswer({{ $question->id }}, this.value)">
                                        @elseif($question->question_type === 'essay')
                                            <textarea name="answers[{{ $question->id }}]" rows="6"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="Write your answer here..." onchange="saveAnswer({{ $question->id }}, this.value)"></textarea>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between items-center mt-6">
                            <button type="button" id="prevBtn" onclick="navigateQuestion(-1)"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition disabled:opacity-50"
                                disabled>
                                Previous
                            </button>

                            <div class="flex space-x-2">
                                @foreach ($questions as $index => $question)
                                    <button type="button" onclick="goToQuestion({{ $index + 1 }})"
                                        class="w-8 h-8 rounded-full text-sm font-semibold question-dot bg-gray-200 text-gray-600 hover:bg-indigo-200"
                                        data-question="{{ $index + 1 }}">
                                        {{ $index + 1 }}
                                    </button>
                                @endforeach
                            </div>

                            <button type="button" id="nextBtn" onclick="navigateQuestion(1)"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Next
                            </button>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 text-center">
                            <button type="button" onclick="submitQuiz()"
                                class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                Submit Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentQuestion = 1;
            const totalQuestions = {{ $questions->count() }};
            let timeLeft = {{ $remainingSeconds }};
            const attemptId = {{ $attempt->id }};

            // Timer
            function updateTimer() {
                if (timeLeft <= 0) {
                    submitQuiz();
                    return;
                }

                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                document.getElementById('minutes').textContent = minutes;
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');

                if (timeLeft <= 60) {
                    document.getElementById('timer').classList.add('text-red-600');
                }
            }

            setInterval(updateTimer, 1000);

            // Navigation
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
                if (targetSlide) {
                    targetSlide.style.display = 'block';
                }

                currentQuestion = questionNum;
                document.getElementById('currentQuestionNum').textContent = questionNum;

                // Update buttons
                document.getElementById('prevBtn').disabled = questionNum === 1;
                document.getElementById('nextBtn').textContent = questionNum === totalQuestions ? 'Finish' : 'Next';

                // Update dots
                document.querySelectorAll('.question-dot').forEach(dot => {
                    dot.classList.remove('bg-indigo-600', 'text-white');
                    dot.classList.add('bg-gray-200', 'text-gray-600');
                });

                const activeDot = document.querySelector(`.question-dot[data-question="${questionNum}"]`);
                if (activeDot) {
                    activeDot.classList.remove('bg-gray-200', 'text-gray-600');
                    activeDot.classList.add('bg-indigo-600', 'text-white');
                }

                // Update progress bar
                const progress = (questionNum / totalQuestions) * 100;
                document.getElementById('progressBar').style.width = progress + '%';
            }

            // Save answer
            function saveAnswer(questionId, answer) {
                fetch(`/employee/quizzes/take/${attemptId}/save`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            question_id: questionId,
                            answer: answer,
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mark question as answered
                            const dot = document.querySelector(`.question-dot[data-question="${currentQuestion}"]`);
                            if (dot && !dot.classList.contains('bg-green-600')) {
                                dot.classList.add('answered');
                                dot.style.borderColor = '#10B981';
                                dot.style.borderWidth = '2px';
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Submit quiz
            function submitQuiz() {
                if (confirm('Are you sure you want to submit your quiz? You cannot undo this action.')) {
                    document.getElementById('quizForm').submit();
                }
            }

            // Initialize
            showQuestion(1);
        </script>
    @endpush
