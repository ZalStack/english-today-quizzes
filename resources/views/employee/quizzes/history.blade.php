@extends('layouts.app')

@section('title', 'Quiz History')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Quiz History</h2>

                @if($attempts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($attempts as $attempt)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $attempt->quiz->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $attempt->quiz->category->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($attempt->score !== null)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $attempt->score >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $attempt->score }}%
                                                </span>
                                            @else
                                                <span class="text-gray-500">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $attempt->status === 'completed' ? 'bg-green-100 text-green-800' :
                                                   ($attempt->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $attempt->completed_at?->format('Y-m-d H:i') ?? $attempt->started_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($attempt->status === 'completed')
                                                <a href="{{ route('employee.quizzes.result', $attempt->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                    View Result
                                                </a>
                                            @elseif($attempt->status === 'in_progress')
                                                <a href="{{ route('employee.quizzes.take', $attempt->id) }}"
                                                    class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                                                    Continue
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $attempts->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 text-lg mb-4">You haven't taken any quizzes yet.</p>
                        <a href="{{ route('employee.quizzes.join') }}"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Join a Quiz
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
