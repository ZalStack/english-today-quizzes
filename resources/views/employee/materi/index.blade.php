@extends('layouts.app')

@section('title', 'Materi')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Materi</h1>
            <p class="text-gray-500 text-sm sm:text-base mt-1">Akses materi yang sudah di-share oleh HRD</p>
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

        @if($materi->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @foreach($materi as $item)
                    @php
                        $isPdf = strtolower($item->file_type) === 'pdf';
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover group">
                        <div class="h-2 bg-gradient-to-r {{ $isPdf ? 'from-red-500 to-orange-400' : 'from-orange-500 to-amber-400' }}"></div>
                        <div class="p-5">
                            <div class="flex items-start gap-3 mb-3">
                                <div class="w-11 h-11 {{ $isPdf ? 'bg-red-50' : 'bg-orange-50' }} rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-5 h-5 {{ $isPdf ? 'text-red-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-gray-900 text-sm leading-tight">{{ $item->title }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $isPdf ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ strtoupper($item->file_type) }}
                                        </span>
                                        <span class="text-[11px] text-gray-400">{{ $item->file_size_formatted }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($item->description)
                                <p class="text-gray-500 text-xs leading-relaxed mb-4 line-clamp-2">{{ $item->description }}</p>
                            @else
                                <div class="mb-4"></div>
                            @endif

                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <div class="text-[11px] text-gray-400">
                                    {{ $item->created_at->format('d M Y') }}
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('employee.materi.show', $item) }}"
                                       class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition-all duration-200">
                                        Detail
                                    </a>
                                    <a href="{{ route('employee.materi.download', $item) }}"
                                       class="px-3 py-1.5 bg-gradient-to-r from-blue-600 to-blue-500 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-300">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($materi->hasPages())
                <div class="mt-6">
                    {{ $materi->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-10 sm:py-16 bg-white rounded-2xl border border-gray-100">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1">Belum Ada Materi</h3>
                <p class="text-gray-500 text-sm sm:text-base">Tunggu materi terbaru dari HRD</p>
            </div>
        @endif
    </div>
</div>
@endsection
