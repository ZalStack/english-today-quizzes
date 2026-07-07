@extends('layouts.app')

@section('title', 'Report - ' . $quiz->title)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Quiz Report: {{ $quiz->title }}</h2>
                    <a href="{{ route('hr.reports.export', $quiz) }}"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Export to CSV
                    </a>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <p class="text-sm text-blue-600 font-medium">Total Attempts</p>
                        <p class="text-3xl font-bold text-blue-900">{{ $statistics['total_attempts'] }}</p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg">
                        <p class="text-sm text-green-600 font-medium">Average Score</p>
                        <p class="text-3xl font-bold text-green-900">{{ number_format($statistics['average_score'], 1) }}%</p>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <p class="text-sm text-purple-600 font-medium">Highest Score</p>
                        <p class="text-3xl font-bold text-purple-900">{{ $statistics['highest_score'] }}%</p>
                    </div>
                    <div class="bg-yellow-50 p-6 rounded-lg">
                        <p class="text-sm text-yellow-600 font-medium">Passing Rate (≥70%)</p>
                        <p class="text-3xl font-bold text-yellow-900">{{ $statistics['passing_rate'] }}</p>
                    </div>
                </div>

                <!-- Attempts Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Division</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correct</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wrong</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attempts as $attempt)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $attempt->user->full_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $attempt->user->division?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $attempt->score >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $attempt->score }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-green-600">{{ $attempt->total_correct }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-red-600">{{ $attempt->total_wrong }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attempt->completed_at?->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $attempts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
