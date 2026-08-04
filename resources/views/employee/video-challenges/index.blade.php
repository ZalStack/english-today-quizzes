@extends('layouts.app')

@section('title', 'Video Challenge')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Video Challenge English Today</h1>
            <p class="text-gray-500 text-sm sm:text-base mt-1">Kumpulkan link video Bahasa Inggris sesuai tema yang tersedia</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 sm:px-5 py-3 rounded-xl mb-4 sm:mb-6 text-sm sm:text-base">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 sm:px-5 py-3 rounded-xl mb-4 sm:mb-6 text-sm sm:text-base">
                {{ session('error') }}
            </div>
        @endif

        @if($challenges->count() > 0)
            <div class="space-y-6 sm:space-y-8">
                @foreach($challenges as $index => $challenge)
                    @php
                        $challengeData = $challengesData[$index] ?? null;
                        $mySubmission = $challengeData['my_submission'] ?? null;
                        $thumb = $mySubmission ? \App\Helpers\VideoLinkHelper::thumbnail($mySubmission->link) : null;
                        $divisionStats = $challengeData['division_stats'] ?? [];
                    @endphp

                    <!-- Challenge Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-start sm:items-center gap-3 sm:gap-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base sm:text-lg font-bold text-gray-900">{{ $challenge->title }}</h3>
                                        @if($challenge->description)
                                            <p class="text-gray-500 text-sm mt-0.5">{{ $challenge->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 sm:gap-3">
                                    @if($mySubmission)
                                        <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Terkumpul</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Belum</span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $challengeData['total_submitted'] ?? 0 }}/{{ $challengeData['total_employees'] ?? 0 }} terkumpul</span>
                                </div>
                            </div>
                        </div>

                        <!-- Division Stats Cards -->
                        @if(count($divisionStats) > 0)
                        <div class="p-4 sm:p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                                @foreach($divisionStats as $divIndex => $div)
                                    <div class="bg-gray-50 rounded-xl p-3 sm:p-4 border border-gray-100">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-semibold text-gray-900 text-sm">{{ $div['name'] ?? 'Unknown' }}</h4>
                                            <span class="text-xs text-gray-400">{{ $div['submitted'] ?? 0 }}/{{ $div['total'] ?? 0 }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                            <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all"
                                                 style="width: {{ isset($div['total']) && $div['total'] > 0 ? round((($div['submitted'] ?? 0) / $div['total']) * 100) : 0 }}%"></div>
                                        </div>
                                        <button onclick="openDivisionModal({{ $index }}, {{ $divIndex }})"
                                                class="w-full text-center text-xs text-indigo-600 hover:text-indigo-700 font-semibold py-1 hover:underline">
                                            Lihat Detail ({{ $div['total'] ?? 0 }})
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- My Video Submission -->
                        <div class="px-4 sm:px-6 pb-4 sm:pb-6">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <span class="text-sm font-semibold text-gray-700">Video Saya</span>
                                        @if($mySubmission && $thumb)
                                            <div class="mt-2 flex flex-wrap items-center gap-3">
                                                <button onclick="playVideo('{{ $thumb['embed'] }}', '{{ $challenge->title }}')"
                                                        class="flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M8 5v14l11-7z"/>
                                                    </svg>
                                                    Lihat Video Saya
                                                </button>
                                                <span class="text-xs text-gray-500 truncate max-w-[200px]">{{ $mySubmission->link }}</span>
                                            </div>
                                        @elseif($mySubmission)
                                            <div class="mt-2">
                                                <a href="{{ $mySubmission->link }}" target="_blank" rel="noopener"
                                                   class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold break-all">
                                                    {{ $mySubmission->link }}
                                                </a>
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">Belum mengumpulkan video</p>
                                        @endif
                                    </div>
                                    <button type="button" onclick="openSubmitModal({{ $challenge->id }}, '{{ $mySubmission?->link ?? '' }}')"
                                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                        {{ $mySubmission ? 'Perbarui Link' : 'Kumpulkan Video' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 sm:py-16 bg-white rounded-2xl border border-gray-100">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1">Belum Ada Challenge</h3>
                <p class="text-gray-500 text-sm sm:text-base">Tunggu challenge terbaru dari HRD</p>
            </div>
        @endif
    </div>
</div>

<!-- Division Detail Modal -->
<div id="divisionModal" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
            <h3 class="font-bold text-gray-900 text-base sm:text-lg" id="divisionModalTitle">Daftar Pegawai</h3>
            <button onclick="closeModal('divisionModal')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto p-4 sm:p-6" id="divisionModalBody"></div>
    </div>
</div>

<!-- Video Player Modal -->
<div id="videoModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-base sm:text-lg" id="videoModalTitle">Putar Video</h3>
            <button onclick="closeModal('videoModal')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="aspect-video bg-black" id="videoPlayer"></div>
    </div>
</div>

<!-- Submit Modal -->
<div id="submitModal" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-4 sm:p-6">
        <div class="flex justify-between items-center mb-3 sm:mb-4">
            <h3 class="text-base sm:text-lg font-bold text-gray-900" id="submitModalTitle">Kumpulkan Video</h3>
            <button onclick="closeModal('submitModal')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="submitForm" method="POST">
            @csrf
            <div class="mb-3 sm:mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Link Video</label>
                <input type="url" name="link" id="linkInput" required
                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm sm:text-base"
                       placeholder="https://drive.google.com/... atau https://youtube.com/...">
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <button type="submit"
                        class="flex-1 px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold text-sm sm:text-base">
                    Kirim
                </button>
                <button type="button" onclick="closeModal('submitModal')"
                        class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-semibold text-sm sm:text-base">
                    Batal
                </button>
            </div>
        </form>
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
        statsDiv.className = 'flex gap-4 mb-4 pb-3 border-b border-gray-100';
        statsDiv.innerHTML = `
            <div><span class="text-green-600 font-bold">${submitted.length}</span> <span class="text-gray-500 text-sm">Sudah</span></div>
            <div><span class="text-red-500 font-bold">${pending.length}</span> <span class="text-gray-500 text-sm">Belum</span></div>
            <div><span class="text-gray-900 font-bold">${employees.length}</span> <span class="text-gray-500 text-sm">Total</span></div>
        `;
        body.appendChild(statsDiv);

        // Show employees
        const list = document.createElement('div');
        list.className = 'space-y-2';

        // Show submitted first
        const sortedEmployees = [...submitted, ...pending];
        sortedEmployees.forEach(function(emp) {
            const isSubmitted = emp.submitted;
            const initial = (emp.name || 'U').charAt(0).toUpperCase();
            const bg = isSubmitted ? 'from-green-500 to-emerald-600' : 'from-gray-400 to-gray-500';

            const div = document.createElement('div');
            div.className = 'flex flex-col sm:flex-row sm:items-center justify-between px-3 sm:px-4 py-2 sm:py-3 rounded-lg ' + (isSubmitted ? 'bg-green-50/50' : 'bg-gray-50/50');

            const left = document.createElement('div');
            left.className = 'flex items-center gap-2 sm:gap-3 min-w-0';

            let nameHtml = `<p class="font-semibold text-gray-900 text-xs sm:text-sm truncate">${escapeHtml(emp.name)}`;
            if (emp.is_me) {
                nameHtml += ` <span class="text-[10px] text-indigo-600 font-bold">(saya)</span>`;
            }
            nameHtml += `</p>`;

            left.innerHTML = `
                <div class="w-7 h-7 sm:w-9 sm:h-9 bg-gradient-to-br ${bg} rounded-lg flex items-center justify-center text-white font-bold text-xs sm:text-sm shrink-0">${initial}</div>
                <div class="min-w-0">${nameHtml}<p class="text-[10px] sm:text-xs text-gray-500 truncate">${escapeHtml(emp.email)}</p></div>
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
        document.getElementById('divisionModal').style.display = 'flex';
    }

    function openSubmitModal(challengeId, link) {
        const form = document.getElementById('submitForm');
        form.action = `/employee/video-challenges/${challengeId}/submit`;
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
</script>
@endpush
@endsection
