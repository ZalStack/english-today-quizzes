@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $quiz->title }}</h2>
                        <p class="text-gray-600 mt-2">{{ $quiz->description }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                        {{ $quiz->status === 'active' ? 'bg-green-100 text-green-800' :
                           ($quiz->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($quiz->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Category</p>
                        <p class="font-semibold">{{ $quiz->category->name }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Duration</p>
                        <p class="font-semibold">{{ $quiz->duration }} minutes</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Total Questions</p>
                        <p class="font-semibold">{{ $quiz->questions->count() }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Enrollment Key</p>
                        <p class="font-semibold">{{ $quiz->enroll_key ?? 'Not Set' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Start Date</p>
                        <p class="font-semibold">{{ $quiz->start_date ? $quiz->start_date->format('Y-m-d H:i') : 'Not Set' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">End Date</p>
                        <p class="font-semibold">{{ $quiz->end_date ? $quiz->end_date->format('Y-m-d H:i') : 'Not Set' }}</p>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Questions</h3>
                    @if($quiz->questions->count() > 0)
                        <div class="space-y-4">
                            @foreach($quiz->questions as $index => $question)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between">
                                        <p class="font-medium">{{ $index + 1 }}. {{ $question->question_text }}</p>
                                        <span class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                    </div>
                                    @if($question->options && is_array($question->options))
                                        <div class="mt-2 ml-4">
                                            @foreach($question->options as $key => $option)
                                                <p class="text-sm {{ $option === $question->correct_answer ? 'text-green-600 font-semibold' : 'text-gray-600' }}">
                                                    {{ chr(65 + $key) }}. {{ $option }}
                                                    @if($option === $question->correct_answer)
                                                        ✓
                                                    @endif
                                                </p>
                                            @endforeach
                                        </div>
                                    @elseif($question->correct_answer)
                                        <p class="text-sm text-green-600 mt-2">Answer: {{ $question->correct_answer }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No questions added yet.</p>
                    @endif
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Attempts ({{ $quiz->attempts->count() }})</h3>
                    @if($quiz->attempts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($quiz->attempts as $attempt)
                                        <tr>
                                            <td class="px-4 py-2">{{ $attempt->user->full_name }}</td>
                                            <td class="px-4 py-2">{{ $attempt->score }}%</td>
                                            <td class="px-4 py-2">{{ ucfirst($attempt->status) }}</td>
                                            <td class="px-4 py-2">{{ $attempt->completed_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No attempts yet.</p>
                    @endif
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Manage Questions
                    </a>
                    <a href="{{ route('hr.quizzes.edit', $quiz) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Edit Quiz
                    </a>
                    <a href="{{ route('hr.reports.show', $quiz) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        View Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
