@extends('layouts.hr')

@section('title', $videoChallenge->title . ' - Progress')
@section('header-title', $videoChallenge->title)
@section('header-subtitle', 'Video challenge progress')

@section('content')
<div class="mb-6">
    <a href="{{ route('hr.video-challenges.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

{{-- Challenge Info --}}
<div class="hr-card p-6 mb-8">
    <div class="flex flex-col sm:flex-row items-start gap-4">
        <div class="hr-avatar w-14 h-14 text-xl flex-shrink-0">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-3 mb-1">
                <h2 class="text-xl font-bold text-slate-900 break-words">{{ $videoChallenge->title }}</h2>
                <span class="hr-badge {{ $videoChallenge->is_active ? 'hr-badge--success' : 'hr-badge--neutral' }}">
                    {{ $videoChallenge->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            @if($videoChallenge->description)
                <p class="text-slate-500 text-sm mt-1">{{ $videoChallenge->description }}</p>
            @endif
            <p class="text-xs text-slate-400 mt-2">Dibuat {{ $videoChallenge->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>

    {{-- Material and Kisi-Kisi Section --}}
    @if($materialData || $kisiKisiData)
        <div class="mt-5 pt-5 border-t border-slate-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if($materialData)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-slate-700 mb-2">
                            <svg class="inline w-4 h-4 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Materi
                        </h4>
                        <p class="text-sm text-slate-600 truncate">{{ $materialData['title'] }}</p>
                        <div class="flex gap-2 mt-2">
                            <a href="{{ $materialData['link'] }}" target="_blank" rel="noopener" class="hr-btn-primary text-xs px-3 py-1.5">Lihat Materi</a>
                            @if($materialData['embed'])
                                <button onclick="openDrivePreview('{{ $materialData['embed'] }}', '{{ $materialData['title'] }}')" class="hr-btn-secondary text-xs px-3 py-1.5">Preview</button>
                            @endif
                        </div>
                    </div>
                @endif

                @if($kisiKisiData)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-slate-700 mb-2">
                            <svg class="inline w-4 h-4 mr-1 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Kisi-Kisi
                        </h4>
                        <p class="text-sm text-slate-600 truncate">{{ $kisiKisiData['title'] }}</p>
                        <div class="flex gap-2 mt-2">
                            <a href="{{ $kisiKisiData['link'] }}" target="_blank" rel="noopener" class="hr-btn-primary text-xs px-3 py-1.5" style="background:linear-gradient(135deg,#7c3aed,#a855f7)">Lihat Kisi-Kisi</a>
                            @if($kisiKisiData['embed'])
                                <button onclick="openDrivePreview('{{ $kisiKisiData['embed'] }}', '{{ $kisiKisiData['title'] }}')" class="hr-btn-secondary text-xs px-3 py-1.5">Preview</button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="hr-stat text-center">
        <p class="text-3xl font-bold text-slate-900">{{ $stats['total_employees'] }}</p>
        <p class="text-sm text-slate-500 mt-1">Total Pegawai</p>
    </div>
    <div class="hr-stat text-center">
        <p class="text-3xl font-bold text-emerald-600">{{ $stats['submitted'] }}</p>
        <p class="text-sm text-slate-500 mt-1">Sudah Mengumpulkan</p>
    </div>
    <div class="hr-stat text-center">
        <p class="text-3xl font-bold text-red-500">{{ $stats['pending'] }}</p>
        <p class="text-sm text-slate-500 mt-1">Belum Mengumpulkan</p>
    </div>
</div>

@if($stats['total_employees'] > 0)
    <div class="w-full bg-slate-200 rounded-full h-3 mb-8">
        <div class="bg-gradient-to-r from-emerald-500 to-green-500 h-3 rounded-full transition-all duration-500"
             style="width: {{ round(($stats['submitted'] / $stats['total_employees']) * 100) }}%"></div>
    </div>
@endif

{{-- Division cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($divisionData as $index => $data)
        @if($data['division_name'] === 'ALL')
            <div class="bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl overflow-hidden card-hover">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-white text-sm truncate">{{ $data['division_name'] }}</h3>
                        <span class="text-xs text-indigo-200">{{ $data['total'] }} org</span>
                    </div>
                    <div class="w-full bg-indigo-400/30 rounded-full h-2 mb-2">
                        <div class="bg-white h-2 rounded-full transition-all"
                             style="width: {{ $data['total'] > 0 ? round(($data['submitted_count'] / $data['total']) * 100) : 0 }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs mb-3">
                        <span class="text-indigo-100 font-semibold">{{ $data['submitted_count'] }} submitted</span>
                        <span class="text-indigo-200 font-semibold">{{ $data['pending_count'] }} pending</span>
                    </div>
                    <button onclick="showDivision({{ $index }})" class="w-full px-3 py-2 bg-white/20 text-white text-xs font-semibold rounded-lg hover:bg-white/30 transition">
                        Detail
                    </button>
                </div>
            </div>
        @else
            <div class="hr-card overflow-hidden card-hover">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-slate-900 text-sm truncate" title="{{ $data['division_name'] }}">{{ $data['division_name'] }}</h3>
                        <span class="text-xs text-slate-400">{{ $data['total'] }} org</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2 mb-2">
                        <div class="bg-gradient-to-r from-emerald-500 to-green-500 h-2 rounded-full transition-all"
                             style="width: {{ $data['total'] > 0 ? round(($data['submitted_count'] / $data['total']) * 100) : 0 }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs mb-3">
                        <span class="text-emerald-600 font-semibold">{{ $data['submitted_count'] }} submitted</span>
                        <span class="text-red-500 font-semibold">{{ $data['pending_count'] }} pending</span>
                    </div>
                    <button onclick="showDivision({{ $index }})" class="hr-btn-primary w-full text-xs py-2">
                        Detail
                    </button>
                </div>
            </div>
        @endif
    @empty
        <div class="col-span-full text-center py-12 hr-card">
            <p class="text-slate-500">Tidak ada pegawai aktif.</p>
        </div>
    @endforelse
</div>

{{-- Employee list modal --}}
<div id="employeeModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEmployeeModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <h3 class="font-bold text-slate-900" id="modalDivisionName">Daftar Pegawai</h3>
            <button onclick="closeEmployeeModal()" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto p-6" id="modalBody"></div>
    </div>
</div>

{{-- Video player modal --}}
<div id="videoModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeVideoModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900" id="videoModalTitle">Putar Video</h3>
            <button onclick="closeVideoModal()" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="aspect-video bg-black" id="videoPlayer"></div>
    </div>
</div>

{{-- Drive Preview Modal --}}
<div id="drivePreviewModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeDrivePreview()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900" id="drivePreviewTitle">Preview File</h3>
            <button onclick="closeDrivePreview()" class="text-slate-400 hover:text-slate-600 transition">
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
        statsDiv.className = 'flex gap-4 mb-4 pb-3 border-b border-slate-100';
        statsDiv.innerHTML = `
            <div><span class="text-emerald-600 font-bold">${submitted.length}</span> <span class="text-slate-500 text-sm">Sudah</span></div>
            <div><span class="text-red-500 font-bold">${pending.length}</span> <span class="text-slate-500 text-sm">Belum</span></div>
            <div><span class="text-slate-900 font-bold">${data.employees.length}</span> <span class="text-slate-500 text-sm">Total</span></div>
        `;
        body.appendChild(statsDiv);

        const list = document.createElement('div');
        list.className = 'space-y-2';

        [...submitted, ...pending].forEach(function(emp) {
            const isSubmitted = emp.submitted;
            const initial = emp.name.charAt(0).toUpperCase();
            const bg = isSubmitted ? 'from-emerald-500 to-green-600' : 'from-slate-400 to-slate-500';

            const div = document.createElement('div');
            div.className = 'flex flex-col sm:flex-row sm:items-center justify-between px-4 py-3 rounded-xl ' + (isSubmitted ? 'bg-emerald-50/50' : 'bg-slate-50');

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
                    link.className = 'text-xs text-indigo-600 hover:underline truncate max-w-[200px]';
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
