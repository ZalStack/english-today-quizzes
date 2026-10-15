{{-- resources/views/employee/video-challenges/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Video Challenge')

@section('content')
@php
    $etTotalChallenges = $challenges->count();
    $etSubmittedByMe = collect($challengesData)->filter(fn ($d) => !empty($d['my_submission']))->count();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-14 sm:pb-16">

    {{-- ================= PAGE HEADER ================= --}}
    <header class="et-reveal mb-6 sm:mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <p class="et-eyebrow">🎬 Video Challenge</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Video Challenge English Today
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kumpulkan link video Bahasa Inggris sesuai tema yang tersedia.
            </p>
        </div>
        @if($etTotalChallenges > 0)
            <div class="flex flex-wrap gap-2 shrink-0">
                <span class="et-chip">📹 {{ $etTotalChallenges }} challenge aktif</span>
                <span class="et-chip">✅ {{ $etSubmittedByMe }} sudah kamu kumpulkan</span>
            </div>
        @endif
    </header>

    @if($etTotalChallenges > 0)
        <div class="space-y-6 sm:space-y-8">
            @foreach($challenges as $index => $challenge)
                @php
                    $challengeData = $challengesData[$index] ?? null;
                    $mySubmission = $challengeData['my_submission'] ?? null;
                    $thumb = $mySubmission ? \App\Helpers\VideoLinkHelper::thumbnail($mySubmission->link) : null;
                    $divisionStats = $challengeData['division_stats'] ?? [];
                    $material = $challengeData['material'] ?? null;
                    $kisiKisi = $challengeData['kisi_kisi'] ?? null;

                    $totalSubmitted = (int) ($challengeData['total_submitted'] ?? 0);
                    $totalEmployees = (int) ($challengeData['total_employees'] ?? 0);
                    $challengeProgress = $totalEmployees > 0
                        ? min(100, (int) round(($totalSubmitted / $totalEmployees) * 100))
                        : 0;
                @endphp

                {{-- ================= MISSION CARD ================= --}}
                <article class="et-panel et-reveal et-delay-{{ min(4, $index + 1) }}">

                    {{-- Header: judul + status + progres --}}
                    <div class="p-4 sm:p-6 border-b border-slate-200/70 dark:border-slate-800">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex items-start gap-3 sm:gap-4 min-w-0">
                                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-lime-500 to-emerald-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/25 et-hover-pop">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-base sm:text-lg font-extrabold tracking-tight text-slate-900 dark:text-white leading-snug">
                                            {{ $challenge->title }}
                                        </h2>
                                        @if($mySubmission)
                                            <span class="et-badge et-badge-success">✔ Terkumpul</span>
                                        @else
                                            <span class="et-badge et-badge-warning">⏳ Belum</span>
                                        @endif
                                    </div>

                                    @if($challenge->description)
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $challenge->description }}</p>
                                    @endif

                                    <div class="mt-2.5 flex flex-wrap gap-2 text-xs text-slate-500 dark:text-slate-400">
                                        <span class="et-chip">🗓️ Dibuat {{ $challenge->created_at->diffForHumans() }}</span>
                                        <span class="et-chip">👥 {{ $totalEmployees }} karyawan</span>
                                        <span class="et-chip">📤 {{ $totalSubmitted }} terkumpul</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Progres pengumpulan --}}
                            <div class="w-full lg:w-72 shrink-0 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 p-3.5">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                        Progres Pengumpulan
                                    </span>
                                    <span class="text-xs font-extrabold text-lime-600 dark:text-lime-400">
                                        {{ $totalSubmitted }}/{{ $totalEmployees }}
                                    </span>
                                </div>
                                <div class="et-progress">
                                    <div class="et-progress__bar" style="width: {{ $challengeProgress }}%"></div>
                                </div>
                                <p class="mt-2 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                    {{ $challengeProgress }}% dari seluruh karyawan sudah mengumpulkan video
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Materi & Kisi-Kisi --}}
                    @if($material || $kisiKisi)
                        <div class="p-4 sm:p-6 border-b border-slate-200/70 dark:border-slate-800">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-3 sm:mb-4">
                                <div class="min-w-0">
                                    <h3 class="et-section-title">📚 Materi &amp; Kisi-Kisi</h3>
                                    <p class="et-section-sub">Pelajari dahulu sebelum merekam video kamu</p>
                                </div>
                                <span class="et-badge et-badge-info">Pelengkap</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                @if($material)
                                    <div class="et-card et-card-hover p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="w-9 h-9 rounded-xl bg-lime-100 text-lime-600 dark:bg-lime-500/15 dark:text-lime-400 flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                    </svg>
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-[10.5px] font-extrabold uppercase tracking-wider text-lime-600 dark:text-lime-400">Materi</p>
                                                    <p class="text-[13px] font-bold text-slate-800 dark:text-slate-100 truncate">{{ $material['title'] }}</p>
                                                </div>
                                            </div>
                                            <div class="flex gap-1.5 shrink-0">
                                                <a href="{{ $material['link'] }}" target="_blank" rel="noopener"
                                                   class="et-btn et-btn-primary et-btn-sm et-press">Lihat</a>
                                                @if($material['embed'])
                                                    <button type="button" onclick="openDrivePreview('{{ $material['embed'] }}', '{{ $material['title'] }}')"
                                                            class="et-btn et-btn-soft et-btn-sm et-press">Preview</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($kisiKisi)
                                    <div class="et-card et-card-hover p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="w-9 h-9 rounded-xl bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400 flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-[10.5px] font-extrabold uppercase tracking-wider text-sky-600 dark:text-sky-400">Kisi-Kisi</p>
                                                    <p class="text-[13px] font-bold text-slate-800 dark:text-slate-100 truncate">{{ $kisiKisi['title'] }}</p>
                                                </div>
                                            </div>
                                            <div class="flex gap-1.5 shrink-0">
                                                <a href="{{ $kisiKisi['link'] }}" target="_blank" rel="noopener"
                                                   class="et-btn et-btn-primary et-btn-sm et-press">Lihat</a>
                                                @if($kisiKisi['embed'])
                                                    <button type="button" onclick="openDrivePreview('{{ $kisiKisi['embed'] }}', '{{ $kisiKisi['title'] }}')"
                                                            class="et-btn et-btn-soft et-btn-sm et-press">Preview</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Statistik per divisi --}}
                    @if(count($divisionStats) > 0)
                        <div class="p-4 sm:p-6 border-b border-slate-200/70 dark:border-slate-800">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-3 sm:mb-4">
                                <div class="min-w-0">
                                    <h3 class="et-section-title">🏢 Statistik Per Divisi</h3>
                                    <p class="et-section-sub">Rasio pengumpulan tiap divisi — klik detail untuk melihat daftar pegawai</p>
                                </div>
                                <span class="et-badge et-badge-neutral">{{ count($divisionStats) }} divisi</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                                @foreach($divisionStats as $divIndex => $div)
                                    @php
                                        $divTotal = (int) ($div['total'] ?? 0);
                                        $divPct = $divTotal > 0
                                            ? min(100, (int) round((((int) ($div['submitted'] ?? 0)) / $divTotal) * 100))
                                            : 0;
                                    @endphp
                                    <div class="et-card et-card-hover p-4">
                                        <div class="flex items-start justify-between gap-2 mb-2.5">
                                            <h4 class="font-extrabold text-slate-900 dark:text-slate-100 text-sm truncate min-w-0">
                                                {{ $div['name'] ?? 'Unknown' }}
                                            </h4>
                                            <span class="et-badge et-badge-success shrink-0">
                                                {{ $div['submitted'] ?? 0 }}/{{ $div['total'] ?? 0 }}
                                            </span>
                                        </div>

                                        <div class="et-progress">
                                            <div class="et-progress__bar" style="width: {{ $divPct }}%"></div>
                                        </div>

                                        <div class="mt-2.5 flex items-center justify-between gap-2">
                                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ $divPct }}% selesai</span>
                                            <button type="button" onclick="openDivisionModal({{ $index }}, {{ $divIndex }})"
                                                    class="et-press text-[11px] font-extrabold text-sky-600 dark:text-sky-400 hover:underline">
                                                Lihat Detail ({{ $div['total'] ?? 0 }}) →
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Video Saya --}}
                    <div class="p-4 sm:p-6 bg-slate-50 dark:bg-slate-800/40">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="w-8 h-8 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-sm shrink-0">🎬</span>
                                    <span class="text-sm font-extrabold text-slate-800 dark:text-slate-100">Video Saya</span>
                                    @if($mySubmission)
                                        <span class="et-badge et-badge-success">Terkumpul</span>
                                    @else
                                        <span class="et-badge et-badge-neutral">Belum dikirim</span>
                                    @endif
                                </div>

                                @if($mySubmission && $thumb)
                                    <div class="mt-2.5 flex flex-wrap items-center gap-2.5">
                                        <button type="button" onclick="playVideo('{{ $thumb['embed'] }}', '{{ $challenge->title }}')"
                                                class="et-btn et-btn-primary et-btn-sm et-press">
                                            ▶ Lihat Video Saya
                                        </button>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-[220px] sm:max-w-[380px]">{{ $mySubmission->link }}</span>
                                    </div>
                                @elseif($mySubmission)
                                    <a href="{{ $mySubmission->link }}" target="_blank" rel="noopener"
                                       class="mt-2 inline-block text-xs font-semibold text-sky-600 dark:text-sky-400 hover:underline break-all">
                                        {{ $mySubmission->link }}
                                    </a>
                                @else
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 font-medium">
                                        Belum mengumpulkan video — tempel link video kamu sekarang.
                                    </p>
                                @endif
                            </div>

                            @if($mySubmission)
                                <button type="button"
                                        onclick="openSubmitModal({{ $challenge->id }}, '{{ $mySubmission?->link ?? '' }}', this.dataset.submitUrl)"
                                        data-submit-url="{{ route('employee.video-challenges.submit', $challenge) }}"
                                        class="et-btn et-btn-warning et-press shrink-0 self-start sm:self-auto">
                                    ✏️ Perbarui Link
                                </button>
                            @else
                                <button type="button"
                                        onclick="openSubmitModal({{ $challenge->id }}, '{{ $mySubmission?->link ?? '' }}', this.dataset.submitUrl)"
                                        data-submit-url="{{ route('employee.video-challenges.submit', $challenge) }}"
                                        class="et-btn et-btn-success et-wiggle et-press shrink-0 self-start sm:self-auto">
                                    ⬆️ Kumpulkan Video
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="et-panel et-reveal">
            <div class="et-empty">
                <div class="et-empty__icon et-mascot et-mascot--bounce">🎬</div>
                <p class="et-empty__title">Belum Ada Challenge</p>
                <p class="et-empty__text">Tunggu challenge terbaru dari HRD — kamu akan menerima notifikasi begitu challenge baru diterbitkan.</p>
                <a href="{{ route('employee.dashboard') }}" class="et-btn et-btn-primary mt-5 et-press">🏠 Ke Dashboard</a>
            </div>
        </div>
    @endif
</div>

{{-- ================= DIVISION DETAIL MODAL ================= --}}
<div id="divisionModal" class="fixed inset-0 z-[60] hidden bg-slate-950/70 dark:bg-black/70 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0 bg-slate-50 dark:bg-slate-800/60">
            <div class="min-w-0">
                <p class="et-eyebrow">Detail Divisi</p>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-base sm:text-lg truncate" id="divisionModalTitle">Daftar Pegawai</h3>
            </div>
            <button type="button" onclick="closeModal('divisionModal')" class="et-icon-btn et-press shrink-0" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto et-scroll-y p-4 sm:p-6 bg-slate-50/60 dark:bg-slate-950/40" id="divisionModalBody"></div>
    </div>
</div>

{{-- ================= VIDEO PLAYER MODAL ================= --}}
<div id="videoModal" class="fixed inset-0 z-[60] hidden bg-slate-950/80 dark:bg-black/80 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-3xl overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
            <h3 class="font-extrabold text-slate-900 dark:text-white text-base sm:text-lg truncate" id="videoModalTitle">Putar Video</h3>
            <button type="button" onclick="closeModal('videoModal')" class="et-icon-btn et-press shrink-0" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="aspect-video bg-black relative" id="videoPlayer"></div>
    </div>
</div>

{{-- ================= SUBMIT MODAL ================= --}}
<div id="submitModal" class="fixed inset-0 z-[60] hidden bg-slate-950/70 dark:bg-black/70 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-lg overflow-hidden max-h-[85vh] flex flex-col">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0">
            <div class="min-w-0">
                <p class="et-eyebrow">Kirim Video</p>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-base sm:text-lg truncate" id="submitModalTitle">Kumpulkan Video</h3>
            </div>
            <button type="button" onclick="closeModal('submitModal')" class="et-icon-btn et-press shrink-0" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto et-scroll-y p-4 sm:p-6">
            <form id="submitForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="linkInput" class="et-label">Link Video</label>
                    <input type="url" name="link" id="linkInput" required
                           class="et-input"
                           placeholder="https://drive.google.com/... atau https://youtube.com/...">
                    <p class="et-hint">Gunakan link Google Drive / YouTube yang bisa diakses oleh HRD.</p>
                </div>

                <div class="flex flex-col-reverse sm:flex-row gap-2.5 sm:justify-end">
                    <button type="button" onclick="closeModal('submitModal')"
                            class="et-btn et-btn-outline et-press">Batal</button>
                    <button type="submit"
                            class="et-btn et-btn-primary et-press flex-1 sm:flex-none">Kirim Video</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= DRIVE PREVIEW MODAL ================= --}}
<div id="drivePreviewModal" class="fixed inset-0 z-[60] hidden bg-slate-950/80 dark:bg-black/80 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-4xl overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
            <h3 class="font-extrabold text-slate-900 dark:text-white text-base sm:text-lg truncate" id="drivePreviewTitle">Preview File</h3>
            <button type="button" onclick="closeDrivePreview()" class="et-icon-btn et-press shrink-0" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="relative bg-slate-100 dark:bg-slate-950" style="padding-bottom: 75%;">
            <iframe id="drivePreviewFrame" src="" width="100%" height="100%" style="position:absolute;top:0;left:0;width:100%;height:100%" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Store division data for modals from PHP
    const challengesData = @json($challengesData);

    function openDivisionModal(challengeIndex, divisionIndex) {
        const data = challengesData[challengeIndex];
        if (!data) return;

        const div = data.division_stats[divisionIndex];
        if (!div) return;

        document.getElementById('divisionModalTitle').textContent = div.name + ' - ' + data.title;

        const body = document.getElementById('divisionModalBody');
        body.innerHTML = '';

        const employees = div.employees || [];
        const submitted = employees.filter(e => e.submitted);
        const pending = employees.filter(e => !e.submitted);

        // Show stats
        const statsDiv = document.createElement('div');
        statsDiv.className = 'grid grid-cols-3 gap-2 sm:gap-3 mb-4 pb-4 border-b border-slate-200 dark:border-slate-800';
        statsDiv.innerHTML = `
            <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/70 dark:border-emerald-500/25 px-3 py-2.5 text-center">
                <p class="text-lg leading-none font-extrabold text-emerald-600 dark:text-emerald-400">${submitted.length}</p>
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-1">Sudah</p>
            </div>
            <div class="rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200/70 dark:border-rose-500/25 px-3 py-2.5 text-center">
                <p class="text-lg leading-none font-extrabold text-rose-600 dark:text-rose-400">${pending.length}</p>
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-1">Belum</p>
            </div>
            <div class="rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2.5 text-center">
                <p class="text-lg leading-none font-extrabold text-slate-900 dark:text-white">${employees.length}</p>
                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-1">Total</p>
            </div>
        `;
        body.appendChild(statsDiv);

        // Show employees
        const list = document.createElement('div');
        list.className = 'space-y-2';

        // Show submitted first
        const sortedEmployees = [...submitted, ...pending];

        if (sortedEmployees.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'text-center py-8';
            empty.innerHTML = `
                <p class="text-4xl mb-2">🧑‍💼</p>
                <p class="text-sm font-bold text-slate-800 dark:text-slate-100">Belum ada pegawai</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daftar pegawai pada divisi ini belum tersedia.</p>
            `;
            body.appendChild(empty);
            document.getElementById('divisionModal').style.display = 'flex';
            return;
        }

        sortedEmployees.forEach(function(emp) {
            const isSubmitted = emp.submitted;
            const initial = (emp.name || 'U').charAt(0).toUpperCase();
            const bg = isSubmitted ? 'from-emerald-500 to-teal-600' : 'from-slate-400 to-slate-500 dark:from-slate-500 dark:to-slate-600';

            const div = document.createElement('div');
            div.className = 'flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-3 sm:px-4 py-3 rounded-2xl border ' +
                (isSubmitted
                    ? 'bg-emerald-50/70 border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-500/25'
                    : 'bg-white border-slate-200 dark:bg-slate-800/60 dark:border-slate-700');

            const left = document.createElement('div');
            left.className = 'flex items-center gap-2.5 sm:gap-3 min-w-0';

            let nameHtml = `<p class="font-bold text-slate-900 dark:text-slate-100 text-xs sm:text-sm truncate">${escapeHtml(emp.name)}`;
            if (emp.is_me) {
                nameHtml += ` <span class="text-[10px] font-extrabold text-lime-600 dark:text-lime-400">(saya)</span>`;
            }
            nameHtml += `</p>`;

            left.innerHTML = `
                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-br ${bg} rounded-xl flex items-center justify-center text-white font-bold text-xs sm:text-sm shrink-0">${initial}</div>
                <div class="min-w-0">${nameHtml}<p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">${escapeHtml(emp.email)}</p></div>
            `;
            div.appendChild(left);

            const right = document.createElement('div');
            right.className = 'flex items-center gap-2 shrink-0 sm:self-auto';

            if (isSubmitted) {
                const badge = document.createElement('span');
                badge.className = 'et-badge et-badge-success';
                badge.textContent = 'Sudah';
                right.appendChild(badge);

                if (emp.embed) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'et-btn et-btn-primary et-btn-sm';
                    btn.textContent = 'Lihat Video';
                    btn.dataset.embed = emp.embed;
                    btn.dataset.name = emp.name;
                    btn.addEventListener('click', function() {
                        playVideo(this.dataset.embed, this.dataset.name);
                    });
                    right.appendChild(btn);
                } else if (emp.link) {
                    const link = document.createElement('a');
                    link.href = emp.link;
                    link.target = '_blank';
                    link.rel = 'noopener';
                    link.className = 'text-[11px] sm:text-xs font-bold text-sky-600 dark:text-sky-400 hover:underline truncate max-w-[120px] sm:max-w-[200px]';
                    link.textContent = 'Link';
                    right.appendChild(link);
                }
            } else {
                const badge = document.createElement('span');
                badge.className = 'et-badge et-badge-danger';
                badge.textContent = 'Belum';
                right.appendChild(badge);
            }
            div.appendChild(right);

            list.appendChild(div);
        });

        body.appendChild(list);
        document.getElementById('divisionModal').style.display = 'flex';
    }

    function openSubmitModal(challengeId, link, actionUrl) {
        const form = document.getElementById('submitForm');
        form.action = actionUrl || `/employee/video-challenges/${challengeId}/submit`;
        document.getElementById('linkInput').value = link || '';
        document.getElementById('submitModal').style.display = 'flex';
    }

    function playVideo(embedUrl, name) {
        if (!embedUrl) return;
        document.getElementById('videoModalTitle').textContent = 'Video \u2014 ' + (name || '');
        document.getElementById('videoPlayer').innerHTML = '<iframe src="' + embedUrl + '" width="100%" height="100%" style="position:absolute;top:0;left:0;width:100%;height:100%" frameborder="0" allowfullscreen allow="autoplay"></iframe>';
        document.getElementById('videoPlayer').classList.add('relative');
        document.getElementById('videoModal').style.display = 'flex';
    }

    function openDrivePreview(embedUrl, title) {
        if (!embedUrl) return;
        document.getElementById('drivePreviewTitle').textContent = 'Preview \u2014 ' + (title || 'File');
        document.getElementById('drivePreviewFrame').src = embedUrl;
        document.getElementById('drivePreviewModal').style.display = 'flex';
    }

    function closeDrivePreview() {
        document.getElementById('drivePreviewModal').style.display = 'none';
        document.getElementById('drivePreviewFrame').src = '';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        if (id === 'videoModal') {
            document.getElementById('videoPlayer').innerHTML = '';
            document.getElementById('videoPlayer').classList.remove('relative');
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // Close modals on backdrop click
    document.getElementById('divisionModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal('divisionModal');
    });
    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal('videoModal');
    });
    document.getElementById('submitModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal('submitModal');
    });
    document.getElementById('drivePreviewModal').addEventListener('click', function(e) {
        if (e.target === this) closeDrivePreview();
    });
</script>
@endpush
@endsection
