@extends('layouts.app')

@section('title', $videoChallenge->title . ' — Progress')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4 sm:mb-6">
            <a href="{{ route('hr.video-challenges.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">&larr; Kembali</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 break-words">{{ $videoChallenge->title }}</h1>
                    @if($videoChallenge->description)
                        <p class="text-gray-500 text-sm sm:text-base mt-1">{{ $videoChallenge->description }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1 sm:mt-2">Dibuat {{ $videoChallenge->created_at->format('d M Y H:i') }}</p>
                </div>
                <span class="px-3 py-1.5 {{ $videoChallenge->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} text-xs font-bold rounded-full whitespace-nowrap">
                    {{ $videoChallenge->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-5 text-center">
                <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $stats['total_employees'] }}</p>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Total Pegawai</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-5 text-center">
                <p class="text-2xl sm:text-3xl font-extrabold text-green-600">{{ $stats['submitted'] }}</p>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Sudah Mengumpulkan</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-5 text-center">
                <p class="text-2xl sm:text-3xl font-extrabold text-red-500">{{ $stats['pending'] }}</p>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Belum Mengumpulkan</p>
            </div>
        </div>

        @if($stats['total_employees'] > 0)
            <div class="w-full bg-gray-200 rounded-full h-3 mb-6 sm:mb-8">
                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full transition-all duration-500"
                     style="width: {{ round(($stats['submitted'] / $stats['total_employees']) * 100) }}%"></div>
            </div>
        @endif

        {{-- Division cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
            @forelse($divisionData as $index => $data)
                @if($data['division_name'] === 'ALL')
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-sm border border-indigo-400 overflow-hidden card-hover">
                        <div class="p-3 sm:p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-bold text-white text-xs sm:text-sm truncate">{{ $data['division_name'] }}</h3>
                                <span class="text-[10px] sm:text-xs text-indigo-200 shrink-0">{{ $data['total'] }} org</span>
                            </div>
                            <div class="w-full bg-indigo-400/30 rounded-full h-2 mb-2">
                                <div class="bg-white h-2 rounded-full transition-all"
                                     style="width: {{ $data['total'] > 0 ? round(($data['submitted_count'] / $data['total']) * 100) : 0 }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] sm:text-xs mb-2 sm:mb-3">
                                <span class="text-indigo-100 font-semibold">{{ $data['submitted_count'] }} submitted</span>
                                <span class="text-indigo-200 font-semibold">{{ $data['pending_count'] }} pending</span>
                            </div>
                            <button onclick="showDivision({{ $index }})"
                                     class="w-full px-2 py-1.5 sm:px-3 sm:py-2 bg-white/20 text-white text-[10px] sm:text-xs font-semibold rounded-lg hover:bg-white/30 transition">
                                Detail
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
                        <div class="p-3 sm:p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-bold text-gray-900 text-xs sm:text-sm truncate" title="{{ $data['division_name'] }}">{{ $data['division_name'] }}</h3>
                                <span class="text-[10px] sm:text-xs text-gray-400 shrink-0">{{ $data['total'] }} org</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all"
                                     style="width: {{ $data['total'] > 0 ? round(($data['submitted_count'] / $data['total']) * 100) : 0 }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] sm:text-xs mb-2 sm:mb-3">
                                <span class="text-green-600 font-semibold">{{ $data['submitted_count'] }} submitted</span>
                                <span class="text-red-500 font-semibold">{{ $data['pending_count'] }} pending</span>
                            </div>
                            <button onclick="showDivision({{ $index }})"
                                     class="w-full px-2 py-1.5 sm:px-3 sm:py-2 bg-indigo-600 text-white text-[10px] sm:text-xs font-semibold rounded-lg hover:bg-indigo-700 transition">
                                Detail
                            </button>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-span-full text-center py-8 sm:py-12 bg-white rounded-2xl border border-gray-100">
                    <p class="text-gray-500 text-sm sm:text-base">Tidak ada pegawai aktif.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Employee list modal --}}
<div id="employeeModal" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
            <h3 class="font-bold text-gray-900 text-base sm:text-lg" id="modalDivisionName">Daftar Pegawai</h3>
            <button onclick="closeEmployeeModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto p-4 sm:p-6" id="modalBody"></div>
    </div>
</div>

{{-- Video player modal --}}
<div id="videoModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-base sm:text-lg" id="videoModalTitle">Putar Video</h3>
            <button onclick="closeVideoModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="aspect-video bg-black" id="videoPlayer"></div>
    </div>
</div>

@push('scripts')
<script>
    // ==========================================
    // FIXED: Use pre-processed data from controller
    // ==========================================
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
        statsDiv.className = 'flex gap-4 mb-4 pb-3 border-b border-gray-100';
        statsDiv.innerHTML = `
            <div><span class="text-green-600 font-bold">${submitted.length}</span> <span class="text-gray-500 text-sm">Sudah</span></div>
            <div><span class="text-red-500 font-bold">${pending.length}</span> <span class="text-gray-500 text-sm">Belum</span></div>
            <div><span class="text-gray-900 font-bold">${data.employees.length}</span> <span class="text-gray-500 text-sm">Total</span></div>
        `;
        body.appendChild(statsDiv);

        const list = document.createElement('div');
        list.className = 'space-y-2';

        [...submitted, ...pending].forEach(function(emp) {
            const isSubmitted = emp.submitted;
            const initial = emp.name.charAt(0).toUpperCase();
            const bg = isSubmitted ? 'from-green-500 to-emerald-600' : 'from-gray-400 to-gray-500';

            const div = document.createElement('div');
            div.className = 'flex flex-col sm:flex-row sm:items-center justify-between px-3 sm:px-5 py-2 sm:py-3 rounded-lg ' + (isSubmitted ? 'bg-green-50/50' : 'bg-gray-50/50');

            const left = document.createElement('div');
            left.className = 'flex items-center gap-2 sm:gap-3 min-w-0';
            left.innerHTML = `
                <div class="w-7 h-7 sm:w-9 sm:h-9 bg-gradient-to-br ${bg} rounded-lg flex items-center justify-center text-white font-bold text-xs sm:text-sm shrink-0">${initial}</div>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900 text-xs sm:text-sm truncate">${escapeHtml(emp.name)}</p>
                    <p class="text-[10px] sm:text-xs text-gray-500 truncate">${escapeHtml(emp.email)}</p>
                </div>
            `;
            div.appendChild(left);

            const right = document.createElement('div');
            right.className = 'flex items-center gap-2 mt-1 sm:mt-0';

            if (isSubmitted) {
                const badge = document.createElement('span');
                badge.className = 'px-2 py-0.5 sm:px-2.5 sm:py-0.5 bg-green-100 text-green-700 text-[10px] sm:text-xs font-semibold rounded-full';
                badge.textContent = 'Sudah';
                right.appendChild(badge);

                if (emp.embed) {
                    const btn = document.createElement('button');
                    btn.className = 'px-2 py-1 sm:px-3 sm:py-1.5 bg-indigo-600 text-white text-[10px] sm:text-xs font-semibold rounded-lg hover:bg-indigo-700 transition';
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
                    link.className = 'text-[10px] sm:text-xs text-indigo-600 hover:underline truncate max-w-[120px] sm:max-w-[200px]';
                    link.textContent = 'Link';
                    right.appendChild(link);
                }
            } else {
                const badge = document.createElement('span');
                badge.className = 'px-2 py-0.5 sm:px-2.5 sm:py-0.5 bg-red-100 text-red-600 text-[10px] sm:text-xs font-semibold rounded-full';
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

    // Close modals on backdrop click
    document.getElementById('employeeModal').addEventListener('click', function(e) {
        if (e.target === this) closeEmployeeModal();
    });
    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) closeVideoModal();
    });
</script>
@endpush
@endsection
