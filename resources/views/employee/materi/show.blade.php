@extends('layouts.app')

@section('title', $materi->title . ' - Materi')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('employee.materi.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Materi
            </a>
        </div>

        @php
            $isPdf = strtolower($materi->file_type) === 'pdf';
            $isPpt = in_array(strtolower($materi->file_type), ['ppt', 'pptx']);
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="h-3 bg-gradient-to-r {{ $isPdf ? 'from-red-500 to-orange-400' : 'from-orange-500 to-amber-400' }}"></div>

            <div class="p-5 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-5 mb-6">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 {{ $isPdf ? 'bg-red-50' : 'bg-orange-50' }} rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 {{ $isPdf ? 'text-red-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-tight">{{ $materi->title }}</h1>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $isPdf ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ strtoupper($materi->file_type) }}
                            </span>
                            <span class="text-sm text-gray-400">{{ $materi->file_size_formatted }}</span>
                            <span class="text-gray-300">&middot;</span>
                            <span class="text-sm text-gray-400">{{ $materi->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                @if($materi->description)
                    <div class="bg-gray-50 rounded-xl p-4 sm:p-5 mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $materi->description }}</p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 mb-6">
                    <button onclick="togglePreview()" id="previewToggleBtn"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span id="previewToggleText">Lihat File</span>
                    </button>
                    <a href="{{ route('employee.materi.download', $materi) }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download
                    </a>
                </div>

                {{-- Inline File Preview --}}
                <div id="filePreviewSection" class="hidden">
                    <div class="border border-gray-200 rounded-xl overflow-hidden bg-gray-100">
                        @if($isPdf)
                            <iframe src="{{ $fileUrl }}"
                                    class="w-full"
                                    style="height: 80vh; min-height: 500px;"
                                    frameborder="0">
                            </iframe>
                        @elseif($isPpt)
                            <iframe src="https://docs.google.com/gview?url={{ urlencode(asset('storage/' . $materi->file_path)) }}&embedded=true"
                                    class="w-full"
                                    style="height: 80vh; min-height: 500px;"
                                    frameborder="0">
                            </iframe>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-2 text-center">
                        Gunakan scroll untuk menavigasi halaman.
                        @if($isPdf)
                            Klik ikon download di pojok kanan atas PDF untuk menyimpan.
                        @endif
                    </p>
                </div>

                {{-- Info --}}
                <div class="text-sm text-gray-500 pt-4 border-t border-gray-100">
                    <span class="font-medium text-gray-700">Diupload oleh:</span> {{ $materi->uploader->full_name ?? $materi->uploader->name }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let previewVisible = false;

    function togglePreview() {
        const section = document.getElementById('filePreviewSection');
        const btn = document.getElementById('previewToggleText');

        previewVisible = !previewVisible;

        if (previewVisible) {
            section.classList.remove('hidden');
            btn.textContent = 'Sembunyikan File';
        } else {
            section.classList.add('hidden');
            btn.textContent = 'Lihat File';
        }
    }
</script>
@endpush
@endsection
