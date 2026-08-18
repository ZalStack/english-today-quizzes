@extends('layouts.hr')

@section('title', $videoChallenge->title . ' - Progress')
@section('header-title', $videoChallenge->title)
@section('header-subtitle', 'Video challenge progress & tracking')

@section('content')
<div class="mb-6">
    <a href="{{ route('hr.video-challenges.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="bg-gradient-to-r from-primary-600 to-primary-500 rounded-2xl p-6 sm:p-8 mb-8 text-white shadow-glow-primary">
    <div class="flex flex-col sm:flex-row items-start gap-5">
        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <h2 class="text-xl sm:text-2xl font-bold break-words tracking-tight">{{ $videoChallenge->title }}</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $videoChallenge->is_active ? 'bg-emerald-400/20 text-emerald-100 border border-emerald-400/30' : 'bg-white/20 text-white/80 border border-white/20' }}">
                    {{ $videoChallenge->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            @if($videoChallenge->description)
                <p class="text-primary-100 text-sm mt-1 max-w-2xl">{{ $videoChallenge->description }}</p>
            @endif
            <p class="text-primary-200/70 text-xs mt-2">Dibuat {{ $videoChallenge->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>

    @if($materialData || $kisiKisiData)
        <div class="mt-6 pt-5 border-t border-white/10">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if($materialData)
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                        <h4 class="text-sm font-semibold text-white mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Materi
                        </h4>
                        <p class="text-sm text-primary-100 truncate">{{ $materialData['title'] }}</p>
                        <div class="flex gap-2 mt-3">
                            <a href="{{ $materialData['link'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold rounded-lg transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Lihat Materi
                            </a>
                            @if($materialData['embed'])
                                <button onclick="openDrivePreview('{{ $materialData['embed'] }}', '{{ $materialData['title'] }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white/90 text-xs font-semibold rounded-lg transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Preview
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                @if($kisiKisiData)
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                        <h4 class="text-sm font-semibold text-white mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Kisi-Kisi
                        </h4>
                        <p class="text-sm text-primary-100 truncate">{{ $kisiKisiData['title'] }}</p>
                        <div class="flex gap-2 mt-3">
                            <a href="{{ $kisiKisiData['link'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold rounded-lg transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Lihat Kisi-Kisi
                            </a>
                            @if($kisiKisiData['embed'])
                                <button onclick="openDrivePreview('{{ $kisiKisiData['embed'] }}', '{{ $kisiKisiData['title'] }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white/90 text-xs font-semibold rounded-lg transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Preview
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 animate-stagger">
    <div class="hr-stat text-center">
        <div class="hr-stat__icon bg-primary-50 mx-auto mb-3">
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-slate-900 tracking-tight">{{ $stats['total_employees'] }}</p>
        <p class="text-sm text-slate-500 mt-1">Total Pegawai</p>
    </div>
    <div class="hr-stat text-center">
        <div class="hr-stat__icon bg-emerald-50 mx-auto mb-3">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-emerald-600 tracking-tight">{{ $stats['submitted'] }}</p>
        <p class="text-sm text-slate-500 mt-1">Sudah Mengumpulkan</p>
    </div>
    <div class="hr-stat text-center">
        <div class="hr-stat__icon bg-red-50 mx-auto mb-3">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-red-500 tracking-tight">{{ $stats['pending'] }}</p>
        <p class="text-sm text-slate-500 mt-1">Belum Mengumpulkan</p>
    </div>
</div>

@if($stats['total_employees'] > 0)
    <div class="hr-card p-5 mb-8">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-bold text-slate-900">Overall Progress</h4>
            <span class="text-sm font-bold text-emerald-600">{{ round(($stats['submitted'] / $stats['total_employees']) * 100) }}%</span>
        </div>
        <div class="w-full bg-slate-200/80 rounded-full h-3">
            <div class="bg-gradient-to-r from-emerald-500 to-green-500 h-3 rounded-full transition-all duration-700 ease-out"
                 style="width: {{ round(($stats['submitted'] / $stats['total_employees']) * 100) }}%"></div>
        </div>
    </div>
@endif

<h3 class="text-base font-bold text-slate-900 tracking-tight mb-4">Progress per Divisi</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($divisionData as $index => $data)
        @php
            $divProgress = $data['total'] > 0 ? round(($data['submitted_count'] / $data['total']) * 100) : 0;
            $isAll = $data['division_name'] === 'ALL';
        @endphp
        <div class="{{ $isAll ? 'bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl shadow-glow-primary' : 'hr-card rounded-2xl' }} overflow-hidden card-hover">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-sm truncate {{ $isAll ? 'text-white' : 'text-slate-900' }}" title="{{ $data['division_name'] }}">
                        @if($isAll)
                            <svg class="inline w-4 h-4 mr-1 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        @endif
                        {{ $data['division_name'] }}
                    </h3>
                    <span class="text-xs {{ $isAll ? 'text-primary-200' : 'text-slate-400' }}">{{ $data['total'] }} org</span>
                </div>

                <div class="w-full {{ $isAll ? 'bg-primary-400/30' : 'bg-slate-200/80' }} rounded-full h-2 mb-2">
                    <div class="bg-gradient-to-r {{ $isAll ? 'from-white/80 to-white/60' : 'from-emerald-500 to-green-500' }} h-2 rounded-full transition-all duration-500"
                         style="width: {{ $divProgress }}%"></div>
                </div>

                <div class="flex justify-between text-xs mb-3">
                    <span class="{{ $isAll ? 'text-primary-100' : 'text-emerald-600' }} font-semibold">{{ $data['submitted_count'] }} submitted</span>
                    <span class="{{ $isAll ? 'text-primary-200' : 'text-red-500' }} font-semibold">{{ $data['pending_count'] }} pending</span>
                </div>

                <button onclick="showDivision({{ $index }})" class="w-full px-3 py-2 {{ $isAll ? 'bg-white/20 hover:bg-white/30 text-white' : 'bg-primary-50 hover:bg-primary-100 text-primary-600' }} text-xs font-semibold rounded-lg transition-all duration-200">
                    Detail
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12 hr-card rounded-2xl">
            <p class="text-slate-500">Tidak ada pegawai aktif.</p>
        </div>
    @endforelse
</div>

<div id="employeeModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEmployeeModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100/80 flex items-center justify-between shrink-0">
            <h3 class="font-bold text-slate-900" id="modalDivisionName">Daftar Pegawai</h3>
            <button onclick="closeEmployeeModal()" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto p-6" id="modalBody"></div>
    </div>
</div>

<div id="videoModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeVideoModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100/80 flex items-center justify-between">
            <h3 class="font-bold text-slate-900" id="videoModalTitle">Putar Video</h3>
            <button onclick="closeVideoModal()" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="aspect-video bg-black" id="videoPlayer"></div>
    </div>
</div>

<div id="drivePreviewModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeDrivePreview()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100/80 flex items-center justify-between">
            <h3 class="font-bold text-slate-900" id="drivePreviewTitle">Preview File</h3>
            <button onclick="closeDrivePreview()" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="relative" style="padding-bottom: 75%;">
            <iframe id="drivePreviewFrame" src="" width="100%" height="100%" style="position:absolute;top:0;left:0;width:100%;height:100%" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const divisionData = @json($divisionDataJson);

    function showDivision(index) {
        const data = divisionData[index];
        if (!data) return;

        document.getElementById('modalDivisionName').textContent = 'Daftar Pegawai \u2014 ' + data.name;

        const body = document.getElementById('modalBody');
        body.innerHTML = '';

        const submitted = data.employees.filter(e => e.submitted);
        const pending = data.employees.filter(e => !e.submitted);

        const statsDiv = document.createElement('div');
        statsDiv.className = 'flex gap-4 mb-4 pb-3 border-b border-slate-100/80';
        statsDiv.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-emerald-600 font-bold">${submitted.length}</span>
                <span class="text-slate-500 text-sm">Sudah</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="text-red-500 font-bold">${pending.length}</span>
                <span class="text-slate-500 text-sm">Belum</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                <span class="text-slate-900 font-bold">${data.employees.length}</span>
                <span class="text-slate-500 text-sm">Total</span>
            </div>
        `;
        body.appendChild(statsDiv);

        const list = document.createElement('div');
        list.className = 'space-y-2';

        [...submitted, ...pending].forEach(function(emp) {
            const isSubmitted = emp.submitted;
            const initial = emp.name.charAt(0).toUpperCase();
            const bg = isSubmitted ? 'from-emerald-500 to-green-600' : 'from-slate-400 to-slate-500';

            const div = document.createElement('div');
            div.className = 'flex flex-col sm:flex-row sm:items-center justify-between px-4 py-3 rounded-xl ' + (isSubmitted ? 'bg-emerald-50/50 border border-emerald-100' : 'bg-slate-50/80 border border-slate-100');

            const left = document.createElement('div');
            left.className = 'flex items-center gap-3 min-w-0';
            left.innerHTML = `
                <div class="w-9 h-9 bg-gradient-to-br ${bg} rounded-lg flex items-center justify-center text-white font-bold text-sm shrink-0">${initial}</div>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900 text-sm truncate">${escapeHtml(emp.name)}</p>
                    <p class="text-xs text-slate-500 truncate">${escapeHtml(emp.email)}</p>
                </div>
            `;
            div.appendChild(left);

            const right = document.createElement('div');
            right.className = 'flex items-center gap-2 mt-2 sm:mt-0';

            if (isSubmitted) {
                const badge = document.createElement('span');
                badge.className = 'hr-badge hr-badge--success';
                badge.textContent = 'Sudah';
                right.appendChild(badge);

                if (emp.embed) {
                    const btn = document.createElement('button');
                    btn.className = 'hr-btn-primary text-xs px-3 py-1.5';
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
                    link.className = 'text-xs text-primary-600 hover:underline truncate max-w-[200px]';
                    link.textContent = 'Link';
                    right.appendChild(link);
                }
            } else {
                const badge = document.createElement('span');
                badge.className = 'hr-badge hr-badge--danger';
                badge.textContent = 'Belum';
                right.appendChild(badge);
            }
            div.appendChild(right);

            list.appendChild(div);
        });

        body.appendChild(list);
        document.getElementById('employeeModal').style.display = 'flex';
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

    function closeEmployeeModal() {
        document.getElementById('employeeModal').style.display = 'none';
    }

    function closeVideoModal() {
        document.getElementById('videoModal').style.display = 'none';
        document.getElementById('videoPlayer').innerHTML = '';
        document.getElementById('videoPlayer').classList.remove('relative');
    }

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    document.getElementById('employeeModal').addEventListener('click', function(e) {
        if (e.target === this) closeEmployeeModal();
    });
    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) closeVideoModal();
    });
    document.getElementById('drivePreviewModal').addEventListener('click', function(e) {
        if (e.target === this) closeDrivePreview();
    });
</script>
@endpush
@endsection
