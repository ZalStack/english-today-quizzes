@extends('layouts.app')

@section('title', 'Quiz History')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">Quiz History</h1>
            <p class="text-gray-500 mt-1">View all your past and ongoing assessments</p>
        </div>

        @if($attempts->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Quiz</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($attempts as $attempt)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $attempt->quiz->title }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">
                                            {{ $attempt->quiz->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($attempt->score !== null)
                                            <span class="px-3 py-1 rounded-full text-sm font-bold {{ $attempt->score >= 70 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $attempt->score }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $attempt->status === 'completed' ? 'bg-green-100 text-green-700' :
                                               ($attempt->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                            {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $attempt->completed_at?->format('M d, Y H:i') ?? $attempt->started_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($attempt->status === 'completed')
                                            <a href="{{ route('employee.quizzes.result', $attempt->id) }}"
                                               class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                                View Result →
                                            </a>
                                        @elseif($attempt->status === 'in_progress')
                                            <a href="{{ route('employee.quizzes.take', $attempt->id) }}"
                                               class="text-yellow-600 hover:text-yellow-800 font-medium text-sm">
                                                Continue →
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">
                    {{ $attempts->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Quiz History</h3>
                <p class="text-gray-500 mb-6">You haven't taken any quizzes yet. Join a quiz to get started!</p>
                <a href="{{ route('employee.quizzes.join') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-bold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Join a Quiz
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
