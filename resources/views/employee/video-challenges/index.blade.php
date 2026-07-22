@extends('layouts.app')

@section('title', 'Video Challenge')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">Video Challenge English Today</h1>
            <p class="text-gray-500 mt-1">Kumpulkan link video Bahasa Inggris sesuai tema yang tersedia</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-5 py-3 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-200 text-red-700 px-5 py-3 rounded-xl mb-6">
                {{ session('error') }}
            </div>
        @endif

        @php
            function videoThumbnail($link) {
                if (preg_match('/drive\.google\.com\/file\/d\/([^\/\?]+)/', $link, $m)) {
                    return [
                        'thumb' => 'https://drive.google.com/thumbnail?id=' . $m[1] . '&sz=w400-h300',
                        'embed' => 'https://drive.google.com/file/d/' . $m[1] . '/preview',
                    ];
                }
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\?]+)/', $link, $m)) {
                    return [
                        'thumb' => 'https://img.youtube.com/vi/' . $m[1] . '/maxresdefault.jpg',
                        'embed' => 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1',
                    ];
                }
                return null;
            }
        @endphp

        @if($challenges->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($challenges as $challenge)
                    @php
                        $mySubmission = $mySubmissions->get($challenge->id);
                        $thumb = $mySubmission ? videoThumbnail($mySubmission->link) : null;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover flex flex-col">
                        <div class="p-5 flex flex-col h-full">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @if($mySubmission)
                                    <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Terkumpul</span>
                                @else
                                    <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Belum</span>
                                @endif
                            </div>

                            <h3 class="text-base font-bold text-gray-900 mb-1">{{ $challenge->title }}</h3>

                            @if($mySubmission && $thumb)
                                <div class="relative rounded-xl overflow-hidden bg-gray-200 mb-3 video-preview"
                                     style="aspect-ratio: 16/9;"
                                     data-embed="{{ $thumb['embed'] }}"
                                     data-title="{{ $challenge->title }}">
                                    <img src="{{ $thumb['thumb'] }}" alt="Video thumbnail"
                                         class="absolute inset-0 w-full h-full object-cover cursor-pointer"
                                         onclick="playVideo(this)"
                                         onerror="this.style.display='none'">
                                    <div onclick="playVideo(this)"
                                         class="absolute inset-0 flex items-center justify-center cursor-pointer bg-black/0 hover:bg-black/20 transition-colors">
                                        <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
                                            <svg class="w-5 h-5 text-gray-900 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @elseif($mySubmission)
                                <div class="bg-gray-50 rounded-xl p-3 mb-3 text-center">
                                    <a href="{{ $mySubmission->link }}" target="_blank"
                                       class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold break-all">
                                        {{ $mySubmission->link }}
                                    </a>
                                </div>
                            @endif

                            <div class="mt-auto">
                                @if($mySubmission)
                                    <button type="button" onclick="openModal({{ $challenge->id }}, '{{ $mySubmission->link }}')"
                                            class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                                        Perbarui Link
                                    </button>
                                @else
                                    <button type="button" onclick="openModal({{ $challenge->id }})"
                                            class="w-full px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                        Kumpulkan Video
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum Ada Challenge</h3>
                <p class="text-gray-500">Tunggu challenge terbaru dari HRD</p>
            </div>
        @endif
    </div>
</div>

{{-- Video player modal --}}
<div id="videoModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-lg" id="videoModalTitle">Putar Video</h3>
            <button onclick="closeVideoModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="aspect-video bg-black" id="videoPlayer"></div>
    </div>
</div>

{{-- Submit modal --}}
<div id="submitModal" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Kumpulkan Video</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="submitForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Link Video</label>
                <input type="url" name="link" id="linkInput" required
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                       placeholder="https://drive.google.com/... atau https://youtube.com/...">
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold">
                    Kirim
                </button>
                <button type="button" onclick="closeModal()"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-semibold">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(challengeId, link) {
        const form = document.getElementById('submitForm');
        form.action = `/employee/video-challenges/${challengeId}/submit`;
        document.getElementById('linkInput').value = link || '';
        document.getElementById('submitModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('submitModal').style.display = 'none';
    }

    document.getElementById('submitModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    function playVideo(el) {
        const preview = el.closest('.video-preview');
        if (!preview) return;
        const embedUrl = preview.dataset.embed;
        if (!embedUrl) return;
        const title = preview.dataset.title || '';
        document.getElementById('videoModalTitle').textContent = 'Video \u2014 ' + title;
        document.getElementById('videoPlayer').innerHTML = '<iframe src="' + embedUrl + '" width="100%" height="100%" style="position:absolute;top:0;left:0;width:100%;height:100%" frameborder="0" allowfullscreen allow="autoplay"></iframe>';
        document.getElementById('videoPlayer').classList.add('relative');
        document.getElementById('videoModal').style.display = 'flex';
    }

    function closeVideoModal() {
        document.getElementById('videoModal').style.display = 'none';
        document.getElementById('videoPlayer').innerHTML = '';
        document.getElementById('videoPlayer').classList.remove('relative');
    }

    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) closeVideoModal();
    });
</script>
@endpush
@endsection
