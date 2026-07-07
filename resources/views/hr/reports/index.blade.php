@extends('layouts.app')

@section('title', 'Quiz Reports')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Quiz Reports</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($quizzes as $quiz)
                        <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $quiz->title }}</h3>
                            <div class="space-y-2 mb-4">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Total Attempts:</span> {{ $quiz->attempts_count }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Status:</span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $quiz->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($quiz->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('hr.reports.show', $quiz) }}"
                                    class="flex-1 px-4 py-2 bg-indigo-600 text-white text-center rounded-lg hover:bg-indigo-700 transition text-sm">
                                    View Report
                                </a>
                                <a href="{{ route('hr.reports.export', $quiz) }}"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                                    Export CSV
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
