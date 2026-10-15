@extends('layouts.app')

@section('title', 'Riwayat Kuis')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-14 sm:pb-16">

    {{-- Page header --}}
    <header class="et-reveal mb-6 sm:mb-8">
        <p class="et-eyebrow">🕒 Riwayat</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Riwayat Kuis</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Semua kuis yang pernah kamu ikuti — selesai maupun sedang berjalan.</p>
    </header>

    @if($attempts->count() > 0)
        <section class="et-panel et-reveal et-delay-1">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="et-section-title">📚 Daftar Percobaan</h2>
                    <p class="et-section-sub">Klik "Lihat Hasil" untuk review, atau "Lanjutkan" untuk kuis berjalan</p>
                </div>
                <span class="et-badge et-badge-info">{{ $attempts->count() }} data di halaman ini</span>
            </div>

            <div class="p-4 sm:p-5">

                {{-- ================= MOBILE: card list ================= --}}
                <div class="md:hidden space-y-3">
                    @foreach($attempts as $attempt)
                        @php
                            $statusLabel = match ($attempt->status) {
                                'completed' => '✅ Selesai',
                                'in_progress' => '⏳ Berjalan',
                                default => ucfirst(str_replace('_', ' ', $attempt->status)),
                            };
                            $statusClass = match ($attempt->status) {
                                'completed' => 'et-badge-success',
                                'in_progress' => 'et-badge-warning',
                                default => 'et-badge-neutral',
                            };
                            $scoreClass = $attempt->score === null ? 'et-badge-neutral' : ($attempt->score >= 70 ? 'et-badge-success' : 'et-badge-danger');
                        @endphp

                        <article class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 p-4 et-lift">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[14.5px] font-extrabold leading-snug text-slate-900 dark:text-slate-100 break-words">{{ $attempt->quiz->title }}</p>
                                    <p class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-1">
                                        {{ $attempt->completed_at?->format('d M Y, H:i') ?? $attempt->started_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <span class="et-badge {{ $scoreClass }} shrink-0">
                                    {{ $attempt->score !== null ? $attempt->score . '%' : 'N/A' }}
                                </span>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="et-badge et-badge-purple truncate max-w-[60%]">
                                    🗂️ {{ $attempt->quiz->category?->name ?? '—' }}
                                </span>
                                <span class="et-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            </div>

                            <div class="mt-3.5 pt-3 border-t border-dashed border-slate-200 dark:border-slate-800">
                                @if($attempt->status === 'completed')
                                    <a href="{{ route('employee.quizzes.result', $attempt->id) }}"
                                       class="et-btn et-btn-primary et-btn-block et-press">👁️ Lihat Hasil</a>
                                @elseif($attempt->status === 'in_progress')
                                    <a href="{{ route('employee.quizzes.take', $attempt->id) }}"
                                       class="et-btn et-btn-warning et-btn-block et-press">▶️ Lanjutkan</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- ================= DESKTOP: table ================= --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="et-table min-w-full">
                        <thead>
                            <tr>
                                <th>Quiz</th>
                                <th>Category</th>
                                <th>Score</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                                @php
                                    $statusLabel = match ($attempt->status) {
                                        'completed' => '✅ Selesai',
                                        'in_progress' => '⏳ Berjalan',
                                        default => ucfirst(str_replace('_', ' ', $attempt->status)),
                                    };
                                    $statusClass = match ($attempt->status) {
                                        'completed' => 'et-badge-success',
                                        'in_progress' => 'et-badge-warning',
                                        default => 'et-badge-neutral',
                                    };
                                    $scoreClass = $attempt->score === null ? 'et-badge-neutral' : ($attempt->score >= 70 ? 'et-badge-success' : 'et-badge-danger');
                                @endphp
                                <tr>
                                    <td class="min-w-[200px]">
                                        <span class="font-bold text-slate-900 dark:text-slate-100 text-[13.5px]">{{ $attempt->quiz->title }}</span>
                                    </td>
                                    <td>
                                        <span class="et-badge et-badge-purple truncate max-w-[160px]">{{ $attempt->quiz->category?->name ?? '—' }}</span>
                                    </td>
                                    <td>
                                        <span class="et-badge {{ $scoreClass }}">
                                            {{ $attempt->score !== null ? $attempt->score . '%' : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="et-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td class="whitespace-nowrap text-slate-500 dark:text-slate-400">
                                        {{ $attempt->completed_at?->format('d M Y, H:i') ?? $attempt->started_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="whitespace-nowrap">
                                        @if($attempt->status === 'completed')
                                            <a href="{{ route('employee.quizzes.result', $attempt->id) }}"
                                               class="et-btn et-btn-primary et-btn-sm et-press">Lihat Hasil →</a>
                                        @elseif($attempt->status === 'in_progress')
                                            <a href="{{ route('employee.quizzes.take', $attempt->id) }}"
                                               class="et-btn et-btn-warning et-btn-sm et-press">Lanjutkan →</a>
                                        @else
                                            <span class="text-[12.5px] text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($attempts instanceof \Illuminate\Pagination\AbstractPaginator && $attempts->hasPages())
                    <div class="mt-5 pt-4 border-t border-slate-200/70 dark:border-slate-800">
                        {{ $attempts->links() }}
                    </div>
                @endif
            </div>
        </section>
    @else
        <div class="et-panel et-reveal et-delay-1">
            <div class="et-empty">
                <span class="et-mascot et-mascot--bounce w-20 h-20 text-4xl mx-auto mb-4" aria-hidden="true">🦉</span>
                <div class="et-empty__icon">🧭</div>
                <p class="et-empty__title">Belum ada riwayat kuis</p>
                <p class="et-empty__text">Kamu belum pernah mengikuti kuis. Masukkan kode enrollment untuk memulai kuis pertamamu!</p>
                <a href="{{ route('employee.quizzes.join') }}" class="et-btn et-btn-primary et-btn-lg mt-5 et-press et-wiggle">🔑 Gabung Kuis</a>
            </div>
        </div>
    @endif
</div>
@endsection
