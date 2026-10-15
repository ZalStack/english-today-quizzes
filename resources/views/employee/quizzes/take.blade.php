@extends('layouts.app')

@section('title', 'Mengerjakan: ' . $quiz->title)

@section('hide_footer', true)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 sm:pt-5 pb-28 lg:pb-10">

    {{-- ================= EXAM BAR (sticky) ================= --}}
    <div class="et-exambar et-sticky-offset et-reveal">
        <div class="et-exambar__inner">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('employee.dashboard') }}" class="et-exambar__back et-press" title="Keluar ke dashboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="min-w-0">
                    <p class="et-exambar__title truncate">{{ $quiz->title }}</p>
                    <p class="et-exambar__sub">
                        Soal <span id="currentQuestionNum" class="font-bold text-white">1</span> dari {{ $questions->count() }}
                        <span class="hidden sm:inline">• <span id="answeredCounter">{{ count($answeredQuestionIds) }}</span> terjawab</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
                <div id="saveIndicator" class="et-save-pill" aria-live="polite">
                    <span class="w-1.5 h-1.5 rounded-full bg-lime-300"></span>
                    <span id="saveIndicatorText">Tersimpan</span>
                </div>

                <div id="timerBox" class="et-timer">
                    <svg class="w-9 h-9 sm:w-11 sm:h-11" viewBox="0 0 44 44">
                        <circle cx="22" cy="22" r="18" fill="none" stroke="currentColor" stroke-opacity=".25" stroke-width="4"/>
                        <circle id="timerRing" cx="22" cy="22" r="18" fill="none" stroke="currentColor" stroke-width="4"
                                stroke-linecap="round" stroke-dasharray="113.1" stroke-dashoffset="0"
                                style="transform: rotate(-90deg); transform-origin: center; transition: stroke-dashoffset 1s linear;"/>
                    </svg>
                    <div class="leading-none text-center">
                        <div id="timer" class="et-timer__value">
                            <span id="minutes">{{ floor($remainingSeconds / 60) }}</span>:<span id="seconds">{{ sprintf('%02d', $remainingSeconds % 60) }}</span>
                        </div>
                        <p class="et-timer__label">sisa waktu</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="et-exambar__progress">
            <div id="progressBar" class="et-exambar__progress-bar" style="width: {{ ($questions->count() > 0 ? (1 / $questions->count()) * 100 : 0) }}%"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_290px] gap-5 mt-5 sm:mt-6">

        {{-- ================= QUESTION AREA ================= --}}
        <main class="min-w-0">
            <form id="quizForm" action="{{ route('employee.quizzes.submit', $attempt->id) }}" method="POST" class="et-panel et-reveal et-delay-1">
                @csrf

                <div id="questionsContainer" class="p-4 sm:p-6 lg:p-8">
                    @php
                        $lastType = null;
                        $typeLabels = [
                            'multiple_choice' => 'Pilihan Ganda',
                            'true_false' => 'Benar / Salah',
                            'short_answer' => 'Isian Singkat',
                            'essay' => 'Esai',
                        ];
                        $typeColors = [
                            'multiple_choice' => 'et-badge-info',
                            'true_false' => 'et-badge-success',
                            'short_answer' => 'et-badge-warning',
                            'essay' => 'et-badge-purple',
                        ];
                    @endphp
                    @foreach($questions as $index => $question)
                        @php
                            $showHeader = $lastType !== $question->question_type;
                            $lastType = $question->question_type;
                        @endphp
                        <div class="question-slide" data-question="{{ $index + 1 }}" data-type="{{ $question->question_type }}" data-qid="{{ $question->id }}" data-answered="{{ in_array($question->id, $answeredQuestionIds) ? '1' : '0' }}" style="{{ $index === 0 ? '' : 'display: none;' }}">

                            {{-- Type chip --}}
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                <span class="et-badge {{ $typeColors[$question->question_type] ?? 'et-badge-neutral' }}">
                                    {{ $typeLabels[$question->question_type] ?? ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                </span>
                                <span class="et-badge et-badge-neutral">⭐ {{ $question->points }} poin</span>
                            </div>

                            {{-- Question number + text --}}
                            <div class="flex items-start gap-3">
                                <span class="et-qnum">{{ $index + 1 }}</span>
                                <h3 class="et-qtext">{{ $question->question_text }}</h3>
                            </div>

                            @if($question->question_image)
                                <img src="{{ asset('storage/' . $question->question_image) }}" alt="Gambar soal" class="mt-4 rounded-2xl max-w-full h-auto border border-slate-200 dark:border-slate-800">
                            @endif

                            {{-- Answers --}}
                            <div class="mt-5">
                                @if($question->question_type === 'multiple_choice')
                                    @if(!empty($question->options) && is_array($question->options))
                                        <div class="space-y-2.5">
                                            @foreach($question->options as $key => $option)
                                                <label class="mc-option et-option">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}"
                                                        class="sr-only"
                                                        onchange="saveAnswer({{ $question->id }}, '{{ addslashes($option) }}'); highlightSelectedOption(this);">
                                                    <span class="et-option__letter">{{ chr(65 + $key) }}</span>
                                                    <span class="et-option__text">{{ $option }}</span>
                                                    <span class="et-option__check">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="et-option et-option--warning">
                                            <span class="et-option__letter">!</span>
                                            <span class="et-option__text">Soal ini belum memiliki pilihan jawaban. Silakan hubungi HRD.</span>
                                        </div>
                                    @endif
                                @elseif($question->question_type === 'true_false')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        <label class="tf-option et-option">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="True" class="sr-only"
                                                   onchange="saveAnswer({{ $question->id }}, 'True'); highlightSelectedOption(this);">
                                            <span class="et-option__letter">✓</span>
                                            <span class="et-option__text">True</span>
                                            <span class="et-option__check">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        </label>
                                        <label class="tf-option et-option">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="False" class="sr-only"
                                                   onchange="saveAnswer({{ $question->id }}, 'False'); highlightSelectedOption(this);">
                                            <span class="et-option__letter">✕</span>
                                            <span class="et-option__text">False</span>
                                            <span class="et-option__check">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        </label>
                                    </div>
                                @elseif($question->question_type === 'short_answer')
                                    <input type="text" name="answers[{{ $question->id }}]" class="et-input"
                                           placeholder="Ketik jawabanmu di sini..."
                                           onchange="saveAnswer({{ $question->id }}, this.value)">
                                @elseif($question->question_type === 'essay')
                                    <textarea name="answers[{{ $question->id }}]" rows="6" class="et-input"
                                              placeholder="Tulis jawaban esaimu di sini..."
                                              onchange="saveAnswer({{ $question->id }}, this.value)">{{ old('answers.' . $question->id) }}</textarea>
                                @endif
                            </div>

                            {{-- Section transition --}}
                            @php
                                $nextIndex = $index + 1;
                                $nextQuestion = $questions->get($nextIndex);
                                $isLastOfType = $nextQuestion && $nextQuestion->question_type !== $question->question_type;
                            @endphp
                            @if($isLastOfType && $nextQuestion)
                                <div class="my-6 text-center">
                                    <div class="border-t border-dashed border-slate-200 dark:border-slate-800 mb-4"></div>
                                    <button type="button" onclick="goToQuestion({{ $nextIndex + 1 }})"
                                            class="et-btn et-btn-soft et-btn-sm et-press">
                                        Lanjut ke bagian {{ $typeLabels[$nextQuestion->question_type] ?? ucfirst(str_replace('_', ' ', $nextQuestion->question_type)) }} →
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Desktop nav buttons --}}
                <div class="hidden lg:flex items-center justify-between gap-3 px-6 py-4 border-t border-slate-200/70 dark:border-slate-800">
                    <button type="button" id="prevBtn" onclick="navigateQuestion(-1)" class="et-btn et-btn-outline et-press" disabled>
                        ← Sebelumnya
                    </button>
                    <button type="button" id="nextBtn" onclick="navigateQuestion(1)" class="et-btn et-btn-primary et-press">
                        Berikutnya →
                    </button>
                </div>
            </form>
        </main>

        {{-- ================= NAVIGATOR ================= --}}
        <aside class="hidden lg:block">
            <div class="et-panel et-reveal et-delay-2 sticky" style="top: 190px;">
                <div class="px-5 py-4 border-b border-slate-200/70 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="et-section-title">🗺️ Peta Soal</h3>
                    <span class="et-badge et-badge-info"><span id="answeredBadge">{{ count($answeredQuestionIds) }}</span>/{{ $questions->count() }}</span>
                </div>

                <div class="p-4 et-scroll-y" style="max-height: 46vh;">
                    @php
                        $groupedByType = $questions->groupBy('question_type');
                        $typeFullLabels = [
                            'multiple_choice' => 'PG',
                            'true_false' => 'B/S',
                            'short_answer' => 'Isian',
                            'essay' => 'Esai',
                        ];
                        $globalIndex = 0;
                    @endphp
                    <div id="questionNav" class="space-y-3">
                        @foreach($groupedByType as $type => $typeQuestions)
                            <div>
                                <p class="et-nav-type {{ $typeColors[$type] ?? 'et-badge-neutral' }}">{{ $typeFullLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}</p>
                                <div class="grid grid-cols-6 gap-1.5">
                                    @foreach($typeQuestions as $question)
                                        @php $globalIndex++; @endphp
                                        <button type="button" onclick="goToQuestion({{ $globalIndex }})"
                                                class="question-dot"
                                                data-question="{{ $globalIndex }}">{{ $globalIndex }}</button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="px-4 py-3 border-t border-slate-200/70 dark:border-slate-800 flex items-center gap-4 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                    <span class="flex items-center gap-1.5"><span class="et-dot-legend et-dot-legend--answered"></span> Terjawab</span>
                    <span class="flex items-center gap-1.5"><span class="et-dot-legend et-dot-legend--empty"></span> Kosong</span>
                    <span class="flex items-center gap-1.5"><span class="et-dot-legend et-dot-legend--current"></span> Aktif</span>
                </div>

                <div class="p-4 pt-0">
                    <button type="button" onclick="openSubmitModal()" class="et-btn et-btn-success et-btn-block et-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Kumpulkan Jawaban
                    </button>
                </div>
            </div>
        </aside>
    </div>
</div>

{{-- ================= MOBILE ACTION BAR ================= --}}
<div class="et-mobile-actions lg:hidden">
    <button type="button" id="prevBtnMobile" onclick="navigateQuestion(-1)" class="et-btn et-btn-outline et-press" disabled>
        ←
    </button>
    <div class="flex-1 text-center min-w-0 px-1">
        <p class="text-[12px] font-bold text-slate-700 dark:text-slate-200 truncate">Soal <span id="currentQuestionNumMobile">1</span>/{{ $questions->count() }}</p>
        <p class="text-[10.5px] text-slate-400"><span id="answeredCounterMobile">{{ count($answeredQuestionIds) }}</span> terjawab</p>
    </div>
    <button type="button" id="nextBtnMobile" onclick="navigateQuestion(1)" class="et-btn et-btn-primary et-press">
        Lanjut →
    </button>
    <button type="button" onclick="openSubmitModal()" class="et-btn et-btn-success et-press">
        Kumpulkan
    </button>
</div>

{{-- ================= SUBMIT MODAL ================= --}}
<div id="submitModal" class="et-modal" role="dialog" aria-modal="true" aria-labelledby="submitModalTitle" style="display:none;">
    <div class="et-modal__backdrop" onclick="closeSubmitModal()"></div>
    <div class="et-modal__panel et-reveal-pop">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-12 h-12 rounded-2xl bg-lime-100 text-lime-600 dark:bg-lime-500/15 dark:text-lime-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <h3 id="submitModalTitle" class="text-base font-extrabold text-slate-900 dark:text-slate-100">Kumpulkan jawaban?</h3>
                    <p class="text-[13px] text-slate-500 dark:text-slate-400">Pastikan semua jawaban sudah benar.</p>
                </div>
            </div>
            <button type="button" onclick="closeSubmitModal()" class="et-icon-btn et-press" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="grid grid-cols-3 gap-2.5 mt-5">
            <div class="et-modal__stat">
                <p class="et-modal__stat-value text-lime-600 dark:text-lime-400" id="modalAnswered">0</p>
                <p class="et-modal__stat-label">Terjawab</p>
            </div>
            <div class="et-modal__stat">
                <p class="et-modal__stat-value text-rose-600 dark:text-rose-400" id="modalUnanswered">0</p>
                <p class="et-modal__stat-label">Kosong</p>
            </div>
            <div class="et-modal__stat">
                <p class="et-modal__stat-value text-sky-600 dark:text-sky-400" id="modalTime">0:00</p>
                <p class="et-modal__stat-label">Sisa waktu</p>
            </div>
        </div>

        <p id="modalWarning" class="hidden mt-4 text-[13px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/25 rounded-xl px-3.5 py-2.5">
            ⚠️ Ada soal yang belum dijawab. Soal kosong akan dianggap salah.
        </p>

        <div class="flex flex-col-reverse sm:flex-row gap-2.5 mt-5">
            <button type="button" onclick="closeSubmitModal()" class="et-btn et-btn-outline et-press sm:flex-1">Kembali Mengerjakan</button>
            <button type="button" onclick="doSubmit()" class="et-btn et-btn-success et-press sm:flex-1">Ya, Kumpulkan</button>
        </div>
    </div>
</div>

@push('scripts')
<style>
    /* ---- Exam shell ---- */
    .et-exambar {
        position: sticky;
        z-index: 35;
        border-radius: 20px;
        overflow: hidden;
        color: #fff;
        background-image: linear-gradient(135deg, #1cb0f6 0%, #58cc02 100%);
        box-shadow: 0 18px 40px -22px rgba(28, 176, 246, .9);
    }
    .et-exambar__inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
    }
    @media (min-width: 640px) { .et-exambar__inner { padding: 14px 18px; } }

    .et-exambar__back {
        display: flex; align-items: center; justify-content: center;
        width: 38px; height: 38px; border-radius: 13px;
        background: rgba(255,255,255,.16); color: #fff; flex-shrink: 0;
        transition: background .2s ease, transform .15s ease;
    }
    .et-exambar__back:hover { background: rgba(255,255,255,.28); }

    .et-exambar__title { font-size: 14.5px; font-weight: 800; letter-spacing: -0.01em; }
    @media (min-width: 640px) { .et-exambar__title { font-size: 16px; } }
    .et-exambar__sub { font-size: 11.5px; color: rgba(255,255,255,.82); margin-top: 1px; }

    .et-save-pill {
        display: none;
        align-items: center; gap: 6px;
        padding: 6px 11px; border-radius: 999px;
        background: rgba(255,255,255,.16);
        font-size: 11px; font-weight: 700;
        white-space: nowrap;
    }
    @media (min-width: 640px) { .et-save-pill { display: inline-flex; } }
    .et-save-pill.is-saving { background: rgba(255, 255, 255, .28); }

    .et-timer {
        position: relative;
        display: flex; align-items: center; gap: 8px;
        padding: 7px 12px 7px 8px; border-radius: 16px;
        background: rgba(255,255,255,.16);
        color: #fff;
    }
    .et-timer__value { font-size: 17px; font-weight: 800; font-variant-numeric: tabular-nums; letter-spacing: .02em; }
    @media (min-width: 640px) { .et-timer__value { font-size: 20px; } }
    .et-timer__label { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.75); font-weight: 700; margin-top: 2px; }
    .et-timer.is-warning { background: rgba(245, 158, 11, .9); animation: etPulseDot 1.4s ease-in-out infinite; }
    .et-timer.is-danger { background: rgba(239, 68, 68, .95); animation: etPulseDot .9s ease-in-out infinite; }

    .et-exambar__progress { height: 5px; background: rgba(255,255,255,.22); }
    .et-exambar__progress-bar {
        height: 100%;
        background-image: linear-gradient(90deg, #7ee23a, #58cc02);
        transition: width .4s cubic-bezier(.16,.84,.44,1);
        box-shadow: 0 0 14px rgba(88, 204, 2, .8);
    }

    /* ---- Question ---- */
    .et-qnum {
        flex-shrink: 0;
        width: 38px; height: 38px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        background-image: linear-gradient(135deg, #58cc02, #1cb0f6);
        color: #fff; font-weight: 800; font-size: 15px;
        box-shadow: 0 10px 20px -12px rgba(88, 204, 2, .9);
    }
    .et-qtext { font-size: 16px; font-weight: 700; line-height: 1.6; color: #0f172a; padding-top: 4px; }
    @media (min-width: 640px) { .et-qtext { font-size: 18.5px; } }
    html.dark .et-qtext { color: #f1f5f9; }

    /* ---- Options ---- */
    .et-option {
        display: flex; align-items: center; gap: 12px;
        padding: 13px 14px;
        border: 2px solid rgba(226, 232, 240, .95);
        border-radius: 16px;
        background: #fff;
        cursor: pointer;
        transition: all .18s ease;
        min-height: 52px;
    }
    html.dark .et-option { background: #0b1220; border-color: rgba(30, 41, 59, .95); }
    .et-option:hover { border-color: #8fdcff; background: rgba(28, 176, 246, .06); transform: translateX(3px); }
    .et-option:has(input:checked) {
        border-color: #58cc02;
        background: rgba(88, 204, 2, .08);
        box-shadow: 0 0 0 4px rgba(88, 204, 2, .16);
        transform: translateX(3px);
    }
    .et-option__letter {
        flex-shrink: 0;
        width: 32px; height: 32px;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 13.5px;
        background: rgba(28, 176, 246, .14); color: #1899d6;
    }
    html.dark .et-option__letter { color: #8fdcff; background: rgba(28, 176, 246, .18); }
    .et-option__text { flex: 1; font-size: 14.5px; color: #1e293b; line-height: 1.5; }
    html.dark .et-option__text { color: #e2e8f0; }
    .et-option__check {
        flex-shrink: 0; width: 26px; height: 26px; border-radius: 999px;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid rgba(148, 163, 184, .45); color: transparent;
        transition: all .18s ease;
    }
    .et-option:has(input:checked) .et-option__check {
        background-image: linear-gradient(135deg, #58cc02, #1cb0f6);
        border-color: transparent; color: #fff;
    }
    .et-option--warning { cursor: default; border-style: dashed; border-color: #fcd34d; background: rgba(245, 158, 11, .08); }
    .et-option--warning:hover { transform: none; }

    /* ---- Navigator ---- */
    .et-nav-type { font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 7px; }
    .question-dot {
        aspect-ratio: 1 / 1;
        border-radius: 11px;
        font-size: 12px; font-weight: 800;
        background: rgba(148, 163, 184, .16);
        color: #64748b;
        border: 2px solid transparent;
        transition: all .18s ease;
    }
    html.dark .question-dot { background: rgba(148, 163, 184, .12); color: #94a3b8; }
    .question-dot:hover { background: rgba(28, 176, 246, .16); color: #1899d6; }
    .question-dot.is-answered { background: rgba(88, 204, 2, .16); color: #3f8f02; }
    html.dark .question-dot.is-answered { color: #8ee758; }
    .question-dot.is-current { border-color: #58cc02; box-shadow: 0 0 0 3px rgba(88, 204, 2, .22); }

    .et-dot-legend { width: 12px; height: 12px; border-radius: 5px; display: inline-block; }
    .et-dot-legend--answered { background: rgba(88, 204, 2, .8); }
    .et-dot-legend--empty { background: rgba(148, 163, 184, .4); }
    .et-dot-legend--current { background: transparent; border: 2px solid #58cc02; }

    /* ---- Mobile action bar ---- */
    .et-mobile-actions {
        position: fixed;
        left: 0; right: 0; bottom: 0;
        z-index: 40;
        display: flex; align-items: center; gap: 8px;
        padding: 10px 12px calc(10px + env(safe-area-inset-bottom));
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(14px);
        border-top: 1px solid rgba(226, 232, 240, .9);
    }
    html.dark .et-mobile-actions { background: rgba(2, 6, 23, .92); border-top-color: rgba(30, 41, 59, .95); }
    @media (min-width: 1024px) { .et-mobile-actions { display: none !important; } }
    .et-mobile-actions .et-btn { padding: 11px 14px; }

    .et-option.is-selected {
        border-color: #58cc02;
        background: rgba(88, 204, 2, .08);
        box-shadow: 0 0 0 4px rgba(88, 204, 2, .16);
    }
    .et-option.is-selected .et-option__check {
        background-image: linear-gradient(135deg, #58cc02, #1cb0f6);
        border-color: transparent;
        color: #fff;
    }

    /* ---- Modal ---- */
    .et-modal { position: fixed; inset: 0; z-index: 80; display: flex; align-items: center; justify-content: center; padding: 16px; }
    .et-modal__backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, .6); backdrop-filter: blur(6px); }
    .et-modal__panel {
        position: relative;
        width: 100%; max-width: 440px;
        background: #fff;
        border: 1px solid rgba(226, 232, 240, .9);
        border-radius: 24px;
        padding: 22px;
        box-shadow: 0 40px 80px -30px rgba(15, 23, 42, .55);
    }
    html.dark .et-modal__panel { background: #0f172a; border-color: rgba(30, 41, 59, .95); }
    .et-modal__stat {
        text-align: center; padding: 12px 6px; border-radius: 16px;
        background: rgba(148, 163, 184, .1);
    }
    .et-modal__stat-value { font-size: 19px; font-weight: 800; }
    .et-modal__stat-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-top: 2px; }
</style>

<script>
    let currentQuestion = 1;
    const totalQuestions = {{ $questions->count() }};
    const attemptId = {{ $attempt->id }};
    let timeLeft = Math.floor({{ (int) $remainingSeconds }});
    const totalTime = Math.max(1, timeLeft);
    const answeredSet = new Set();
    let isAutoSubmitting = false;

    function setAnsweredCounters() {
        const count = document.querySelectorAll('.question-slide[data-answered="1"]').length;
        ['answeredCounter', 'answeredCounterMobile', 'answeredBadge'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.textContent = count;
        });
    }

    function flashSave(state) {
        const pill = document.getElementById('saveIndicator');
        const text = document.getElementById('saveIndicatorText');
        if (!pill || !text) return;
        if (state === 'saving') {
            pill.classList.add('is-saving');
            text.textContent = 'Menyimpan...';
        } else {
            pill.classList.remove('is-saving');
            text.textContent = 'Tersimpan ✓';
        }
    }

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

        // Circular progress ring
        const ring = document.getElementById('timerRing');
        if (ring) {
            const circumference = 113.1;
            ring.setAttribute('stroke-dashoffset', (circumference * (1 - timeLeft / totalTime)).toFixed(2));
        }

        // Warning states
        const timerBox = document.getElementById('timerBox');
        const timerEl = document.getElementById('timer');
        if (timerBox && timerEl) {
            timerBox.classList.remove('is-warning', 'is-danger');
            timerEl.classList.remove('text-amber-200', 'text-rose-100');
            if (timeLeft <= 60 && timeLeft > 15) {
                timerBox.classList.add('is-warning');
            } else if (timeLeft <= 15) {
                timerBox.classList.add('is-danger');
            }
        }
    }

    setInterval(updateTimer, 1000);

    function updateDots() {
        document.querySelectorAll('.question-dot').forEach(dot => {
            const qNum = parseInt(dot.dataset.question);
            const isActive = qNum === currentQuestion;
            const slide = document.querySelector('.question-slide[data-question="' + qNum + '"]');
            const isAnswered = !!slide && slide.dataset.answered === '1';

            dot.classList.toggle('is-answered', isAnswered);
            dot.classList.toggle('is-current', isActive);
        });
        setAnsweredCounters();
    }

    function highlightSelectedOption(inputEl) {
        const name = inputEl.name;
        document.querySelectorAll('input[name="' + CSS.escape(name) + '"]').forEach(radio => {
            const label = radio.closest('label');
            if (label) label.classList.remove('is-selected');
        });
        const selectedLabel = inputEl.closest('label');
        if (selectedLabel) selectedLabel.classList.add('is-selected');
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
        const targetSlide = document.querySelector('.question-slide[data-question="' + questionNum + '"]');
        if (targetSlide) targetSlide.style.display = 'block';

        currentQuestion = questionNum;
        document.getElementById('currentQuestionNum').textContent = questionNum;
        const numMobile = document.getElementById('currentQuestionNumMobile');
        if (numMobile) numMobile.textContent = questionNum;

        const prevDisabled = questionNum === 1;
        document.getElementById('prevBtn').disabled = prevDisabled;
        const prevMobile = document.getElementById('prevBtnMobile');
        if (prevMobile) prevMobile.disabled = prevDisabled;

        const isLast = questionNum === totalQuestions;
        const nextBtn = document.getElementById('nextBtn');
        if (isLast) {
            nextBtn.textContent = 'Kumpulkan';
            nextBtn.className = 'et-btn et-btn-success et-press';
            nextBtn.onclick = openSubmitModal;
        } else {
            nextBtn.textContent = 'Berikutnya →';
            nextBtn.className = 'et-btn et-btn-primary et-press';
            nextBtn.onclick = function () { navigateQuestion(1); };
        }

        const nextMobile = document.getElementById('nextBtnMobile');
        if (nextMobile) {
            nextMobile.textContent = isLast ? 'Akhir' : 'Lanjut →';
            nextMobile.disabled = isLast;
        }

        updateDots();
        document.getElementById('progressBar').style.width = (questionNum / totalQuestions) * 100 + '%';

        // Scroll question into view on mobile
        if (targetSlide && window.innerWidth < 1024) {
            window.scrollTo({ top: Math.max(0, targetSlide.getBoundingClientRect().top + window.scrollY - 150), behavior: 'smooth' });
        }
    }

    function saveAnswer(questionId, answer) {
        flashSave('saving');
        fetch('/employee/quizzes/take/' + attemptId + '/save', {
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
            flashSave('saved');
        }).catch(() => flashSave('saved'));
    }

    function updateDotAnswered(questionId) {
        const slide = document.querySelector('.question-slide[data-qid="' + questionId + '"]');
        if (slide) {
            slide.dataset.answered = '1';
        }
        updateDots();
    }

    function getUnansweredCount() {
        let count = 0;
        for (let i = 1; i <= totalQuestions; i++) {
            const slide = document.querySelector('.question-slide[data-question="' + i + '"]');
            if (!slide || slide.dataset.answered !== '1') {
                count++;
            }
        }
        return count;
    }

    function openSubmitModal() {
        if (isAutoSubmitting) {
            document.getElementById('quizForm').submit();
            return;
        }
        const unanswered = getUnansweredCount();
        const answered = totalQuestions - unanswered;

        document.getElementById('modalAnswered').textContent = answered;
        document.getElementById('modalUnanswered').textContent = unanswered;
        document.getElementById('modalTime').textContent =
            Math.floor(timeLeft / 60) + ':' + String(timeLeft % 60).padStart(2, '0');
        document.getElementById('modalWarning').classList.toggle('hidden', unanswered === 0);

        document.getElementById('submitModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeSubmitModal() {
        document.getElementById('submitModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    function doSubmit() {
        closeSubmitModal();
        document.getElementById('quizForm').submit();
    }

    // Pre-populate answered state
    document.querySelectorAll('.question-slide').forEach(slide => {
        if (slide.dataset.answered === '1') {
            answeredSet.add(parseInt(slide.dataset.qid));
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSubmitModal();
    });

    // Pre-populate highlight untuk opsi yang sudah terpilih (mis. reload halaman)
    document.querySelectorAll('.mc-option input:checked, .tf-option input:checked').forEach(input => {
        highlightSelectedOption(input);
    });

    showQuestion(1);
    updateTimer();
    setAnsweredCounters();
</script>
@endpush
@endsection
