@extends('layouts.app')

@section('title', 'Quiz History')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Quiz History</h1>
            <p class="text-gray-500 text-sm sm:text-base mt-1">View all your past and ongoing assessments</p>
        </div>

        @if($attempts->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="table-responsive">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Quiz</th>
                                <th class="hidden sm:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($attempts as $attempt)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-3 sm:px-6 py-3 sm:py-4">
                                        <span class="font-medium text-gray-900 text-xs sm:text-sm">{{ $attempt->quiz->title }}</span>
                                    </td>
                                    <td class="hidden sm:table-cell px-3 sm:px-6 py-3 sm:py-4">
                                        <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 bg-blue-100 text-blue-700 text-[10px] sm:text-xs rounded-full">
                                            {{ $attempt->quiz->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-3 sm:py-4">
                                        @if($attempt->score !== null)
                                            <span class="px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-bold {{ $attempt->score >= 70 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $attempt->score }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs sm:text-sm">N/A</span>
                                        @endif
                                    </td>
                                    <td class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4">
                                        <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-semibold rounded-full
                                            {{ $attempt->status === 'completed' ? 'bg-green-100 text-green-700' :
                                               ($attempt->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                            {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                        </span>
                                    </td>
                                    <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500">
                                        {{ $attempt->completed_at?->format('M d, Y H:i') ?? $attempt->started_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-3 sm:py-4">
                                        @if($attempt->status === 'completed')
                                            <a href="{{ route('employee.quizzes.result', $attempt->id) }}"
                                               class="text-blue-600 hover:text-blue-800 font-medium text-xs sm:text-sm">
                                                View Result →
                                            </a>
                                        @elseif($attempt->status === 'in_progress')
                                            <a href="{{ route('employee.quizzes.take', $attempt->id) }}"
                                               class="text-yellow-600 hover:text-yellow-800 font-medium text-xs sm:text-sm">
                                                Continue →
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($attempts instanceof \Illuminate\Pagination\AbstractPaginator && $attempts->hasPages())
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100">
                        {{ $attempts->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-16 text-center">
                <div class="w-16 h-16 sm:w-24 sm:h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                    <svg class="w-8 h-8 sm:w-12 sm:h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">No Quiz History</h3>
                <p class="text-gray-500 text-sm sm:text-base mb-4 sm:mb-6">You haven't taken any quizzes yet. Join a quiz to get started!</p>
                <a href="{{ route('employee.quizzes.join') }}"
                   class="inline-flex items-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-bold text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Join a Quiz
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
