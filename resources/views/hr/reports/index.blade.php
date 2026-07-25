@extends('layouts.app')

@section('title', 'Quiz Reports')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Quiz Reports</h1>
            <p class="text-gray-500 text-sm mt-1">View detailed analytics and export quiz results</p>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Quiz</th>
                            <th scope="col" class="hidden sm:table-cell px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Attempts</th>
                            <th scope="col" class="hidden md:table-cell px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg Score</th>
                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($quizzes as $quiz)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm sm:text-base">{{ $quiz->title }}</p>
                                        <p class="sm:hidden text-xs text-gray-500">{{ $quiz->category->name }}</p>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-4 sm:px-6 py-3 sm:py-4 text-sm text-gray-500">
                                    {{ $quiz->category->name }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                        {{ $quiz->attempts_count }}
                                    </span>
                                </td>
                                <td class="hidden md:table-cell px-4 sm:px-6 py-3 sm:py-4 text-sm text-gray-500">
                                    {{ $quiz->attempts_avg_score ? number_format($quiz->attempts_avg_score, 1) . '%' : '—' }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $quiz->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($quiz->status) }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-right">
                                    <div class="flex flex-wrap items-center justify-end gap-1 sm:gap-2">
                                        <a href="{{ route('hr.reports.show', $quiz) }}"
                                           class="px-3 py-1.5 text-xs sm:text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                                            Details
                                        </a>
                                        <a href="{{ route('hr.reports.export', $quiz) }}"
                                           class="px-3 py-1.5 text-xs sm:text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                            Export CSV
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 sm:px-6 py-10 sm:py-16 text-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">No quizzes available</p>
                                    <p class="text-gray-400 text-sm mt-1">Create a quiz to start collecting reports</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @if($quizzes->hasPages())
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100">
                    {{ $quizzes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
