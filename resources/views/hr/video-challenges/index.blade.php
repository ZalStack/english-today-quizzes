@extends('layouts.app')

@section('title', 'Video Challenges')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3 sm:gap-0">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Video Challenges</h1>
                <p class="text-gray-500 text-sm sm:text-base mt-1">Manage video challenges for employees</p>
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

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="table-responsive">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Challenge</th>
                            <th class="hidden sm:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Submissions</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-right text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($challenges as $challenge)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <div class="flex items-center space-x-2 sm:space-x-3">
                                        <div class="w-7 h-7 sm:w-10 sm:h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-xs sm:text-sm flex-shrink-0">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <span class="font-semibold text-gray-900 text-sm truncate max-w-[100px] sm:max-w-xs">{{ $challenge->title }}</span>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-3 sm:px-6 py-3 sm:py-4 text-gray-600 text-sm truncate max-w-[150px]">{{ $challenge->description ?? '-' }}</td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-semibold rounded-full {{ $challenge->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $challenge->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-sm font-bold text-gray-900">{{ $challenge->submissions_count }}</td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-right">
                                    <div class="flex items-center justify-end space-x-1 sm:space-x-2">
                                        <a href="{{ route('hr.video-challenges.show', $challenge) }}"
                                           class="px-2 py-1 sm:px-3 sm:py-1.5 text-[10px] sm:text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                                            Progress
                                        </a>
                                        <a href="{{ route('hr.video-challenges.edit', $challenge) }}"
                                           class="px-2 py-1 sm:px-3 sm:py-1.5 text-[10px] sm:text-xs bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 sm:px-6 py-8 sm:py-12 text-center">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium text-sm sm:text-base">Belum ada challenge</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($challenges instanceof \Illuminate\Pagination\AbstractPaginator && $challenges->hasPages())
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100">
                    {{ $challenges->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
