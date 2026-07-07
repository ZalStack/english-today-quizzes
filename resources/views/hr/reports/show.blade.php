@extends('layouts.app')

@section('title', 'Report - ' . $quiz->title)

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <a href="{{ route('hr.reports.index') }}" class="text-gray-400 hover:text-gray-600 transition inline-flex items-center mb-2">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Reports
                </a>
                <h1 class="text-3xl font-extrabold text-gray-900">{{ $quiz->title }}</h1>
                <p class="text-gray-500 mt-1">Detailed quiz performance report</p>
            </div>
            <a href="{{ route('hr.reports.export', $quiz) }}"
               class="mt-4 sm:mt-0 inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white">
                <p class="text-blue-100 text-sm font-medium">Total Attempts</p>
                <p class="text-4xl font-bold mt-2">{{ $statistics['total_attempts'] }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white">
                <p class="text-green-100 text-sm font-medium">Average Score</p>
                <p class="text-4xl font-bold mt-2">{{ number_format($statistics['average_score'], 1) }}%</p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white">
                <p class="text-purple-100 text-sm font-medium">Highest Score</p>
                <p class="text-4xl font-bold mt-2">{{ $statistics['highest_score'] }}%</p>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl p-6 text-white">
                <p class="text-yellow-100 text-sm font-medium">Passing Rate (≥70%)</p>
                <p class="text-4xl font-bold mt-2">{{ $statistics['passing_rate'] }}</p>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Participant Results</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Division</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Score</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Correct</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Wrong</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Completed</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($attempts as $attempt)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($attempt->user->full_name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $attempt->user->full_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $attempt->user->division?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $attempt->score >= 70 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $attempt->score }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-green-600 font-semibold">{{ $attempt->total_correct }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-red-600 font-semibold">{{ $attempt->total_wrong }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $attempt->completed_at?->format('M d, Y H:i') ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $attempt->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($attempt->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">No attempts yet</p>
                                    <p class="text-gray-400 text-sm mt-1">Participants results will appear here</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">
                {{ $attempts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
