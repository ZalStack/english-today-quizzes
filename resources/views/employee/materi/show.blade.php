@extends('layouts.app')

@section('title', $materi->title . ' - Materi')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('employee.materi.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Materi
            </a>
        </div>

        @php
            $isPdf = strtolower($materi->file_type) === 'pdf';
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
                        </div>
                    </div>
                </div>

                @if($materi->description)
                    <div class="bg-gray-50 rounded-xl p-4 sm:p-5 mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $materi->description }}</p>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 p-4 bg-gray-50 rounded-xl">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium text-gray-700">Diupload oleh:</span> {{ $materi->uploader->full_name ?? $materi->uploader->name }}
                        <br>
                        <span class="text-gray-400">{{ $materi->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <a href="{{ route('employee.materi.download', $materi) }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download File
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
