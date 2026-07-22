@extends('layouts.app')

@section('title', 'Video Challenges')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Video Challenges</h1>
                <p class="text-gray-500 mt-1">Kelola challenge video Bahasa Inggris untuk pegawai</p>
            </div>
            <a href="{{ route('hr.video-challenges.create') }}"
               class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold">
                + Challenge Baru
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-5 py-3 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($challenges->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($challenges as $challenge)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @if($challenge->is_active)
                                    <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full">Nonaktif</span>
                                @endif
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $challenge->title }}</h3>
                            @if($challenge->description)
                                <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $challenge->description }}</p>
                            @else
                                <p class="text-sm text-gray-400 mb-4 italic">Tidak ada deskripsi</p>
                            @endif

                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span>{{ $challenge->submissions_count }} pengumpulan</span>
                                <span>{{ $challenge->created_at->format('d M Y') }}</span>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('hr.video-challenges.show', $challenge) }}"
                                   class="flex-1 px-3 py-2 bg-indigo-600 text-white text-center text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                                    Lihat Progress
                                </a>
                                <a href="{{ route('hr.video-challenges.edit', $challenge) }}"
                                   class="px-3 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $challenges->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum Ada Challenge</h3>
                <p class="text-gray-500 mb-6">Buat challenge video pertama untuk pegawai</p>
                <a href="{{ route('hr.video-challenges.create') }}"
                   class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold inline-block">
                    + Challenge Baru
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
