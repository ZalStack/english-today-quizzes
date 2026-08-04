@extends('layouts.app')

@section('title', 'Video Challenges')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3 sm:gap-0">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Video Challenges</h1>
                <p class="text-gray-500 text-sm sm:text-base mt-1">Kelola video challenge untuk karyawan</p>
            </div>
            <a href="{{ route('hr.video-challenges.create') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition font-semibold text-sm sm:text-base">
                + Challenge Baru
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl text-sm sm:text-base">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-100 border border-red-200 text-red-700 rounded-xl text-sm sm:text-base">
                {{ session('error') }}
            </div>
        @endif

        @if($challenges->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($challenges as $challenge)
                    @php
                        $total = $challenge->total_employees_snapshot;
                        $submitted = $challenge->total_submitted_snapshot;
                        $percent = $total > 0 ? round(($submitted / $total) * 100) : 0;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover flex flex-col">
                        <div class="p-4 sm:p-5 flex flex-col h-full">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-gray-900 text-sm sm:text-base truncate" title="{{ $challenge->title }}">{{ $challenge->title }}</h3>
                                </div>
                                <span class="shrink-0 px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-semibold rounded-full {{ $challenge->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $challenge->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            @if($challenge->description)
                                <p class="text-gray-500 text-xs sm:text-sm mb-3 line-clamp-2">{{ $challenge->description }}</p>
                            @endif

                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all"
                                     style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] sm:text-xs mb-3">
                                <span class="text-green-600 font-semibold">{{ $submitted }} / {{ $total }} sudah kumpul</span>
                                <span class="text-gray-400 font-semibold">{{ $percent }}%</span>
                            </div>

                            {{-- Per-division breakdown chips --}}
                            @if($challenge->division_breakdown->count() > 0)
                                <div class="flex flex-wrap gap-1.5 sm:gap-2 mb-4">
                                    @foreach($challenge->division_breakdown as $div)
                                        @php $divDone = $div['total'] > 0 && $div['submitted'] >= $div['total']; @endphp
                                        <span class="px-2 py-1 rounded-lg text-[10px] sm:text-xs font-medium border {{ $divDone ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                            {{ $div['name'] }}: {{ $div['submitted'] }}/{{ $div['total'] }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-auto flex flex-col sm:flex-row gap-2">
                                <a href="{{ route('hr.video-challenges.show', $challenge) }}"
                                   class="flex-1 text-center px-3 sm:px-4 py-2 sm:py-2.5 bg-indigo-600 text-white text-xs sm:text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                                    Lihat Progress
                                </a>
                                <a href="{{ route('hr.video-challenges.edit', $challenge) }}"
                                   class="flex-1 text-center px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl hover:bg-gray-300 transition">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($challenges instanceof \Illuminate\Pagination\AbstractPaginator && $challenges->hasPages())
                <div class="mt-6 sm:mt-8">
                    {{ $challenges->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-10 sm:py-16 bg-white rounded-2xl border border-gray-100">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1">Belum ada challenge</h3>
                <p class="text-gray-500 text-sm sm:text-base mb-4">Buat video challenge pertama untuk karyawan</p>
                <a href="{{ route('hr.video-challenges.create') }}"
                   class="inline-flex items-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition font-semibold text-sm sm:text-base">
                    + Challenge Baru
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
