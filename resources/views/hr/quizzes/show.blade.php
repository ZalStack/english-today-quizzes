@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('hr.quizzes.index') }}" class="text-gray-400 hover:text-gray-600 transition inline-flex items-center mb-4">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Quizzes
            </a>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <h1 class="text-3xl font-extrabold text-gray-900">{{ $quiz->title }}</h1>
                <span class="mt-2 sm:mt-0 px-4 py-2 text-sm font-bold rounded-full {{ $quiz->status === 'active' ? 'bg-green-100 text-green-700' : ($quiz->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                    {{ ucfirst($quiz->status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Quiz Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quiz Information</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-indigo-50 rounded-xl p-4">
                            <p class="text-xs text-indigo-600 font-medium">Category</p>
                            <p class="font-bold text-gray-900 mt-1">{{ $quiz->category->name }}</p>
                        </div>
                        <div class="bg-green-50 rounded-xl p-4">
                            <p class="text-xs text-green-600 font-medium">Duration</p>
                            <p class="font-bold text-gray-900 mt-1">{{ $quiz->duration }} min</p>
                        </div>
                        <div class="bg-purple-50 rounded-xl p-4">
                            <p class="text-xs text-purple-600 font-medium">Questions</p>
                            <p class="font-bold text-gray-900 mt-1">{{ $quiz->questions->count() }}</p>
                        </div>
                        <div class="bg-yellow-50 rounded-xl p-4">
                            <p class="text-xs text-yellow-600 font-medium">Enroll Key</p>
                            <p class="font-bold text-gray-900 mt-1">{{ $quiz->enroll_key ?? 'None' }}</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4">
                            <p class="text-xs text-blue-600 font-medium">Start Date</p>
                            <p class="font-bold text-gray-900 mt-1 text-sm">{{ $quiz->start_date?->format('M d, Y H:i') ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-4">
                            <p class="text-xs text-red-600 font-medium">End Date</p>
                            <p class="font-bold text-gray-900 mt-1 text-sm">{{ $quiz->end_date?->format('M d, Y H:i') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Questions Preview -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Questions ({{ $quiz->questions->count() }})</h3>
                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">Manage Questions →</a>
                    </div>

                    @if($quiz->questions->count() > 0)
                        <div class="space-y-3">
                            @foreach($quiz->questions->take(5) as $index => $question)
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="font-semibold text-gray-900">{{ $index + 1 }}. {{ Str::limit($question->question_text, 100) }}</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }} - {{ $question->points }} pts</p>
                                </div>
                            @endforeach
                            @if($quiz->questions->count() > 5)
                                <p class="text-center text-sm text-gray-500 mt-3">+{{ $quiz->questions->count() - 5 }} more questions</p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No questions added yet</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}"
                           class="block w-full px-4 py-3 bg-indigo-600 text-white text-center rounded-xl hover:bg-indigo-700 transition font-medium">
                            Manage Questions
                        </a>
                        <a href="{{ route('hr.quizzes.edit', $quiz) }}"
                           class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded-xl hover:bg-blue-700 transition font-medium">
                            Edit Quiz
                        </a>
                        <a href="{{ route('hr.reports.show', $quiz) }}"
                           class="block w-full px-4 py-3 bg-purple-600 text-white text-center rounded-xl hover:bg-purple-700 transition font-medium">
                            View Report
                        </a>
                    </div>
                </div>

                <!-- Result Settings -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Result Settings</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Show Score</span>
                            <span class="font-semibold {{ $quiz->show_score ? 'text-green-600' : 'text-red-600' }}">{{ $quiz->show_score ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Show Correct Answers</span>
                            <span class="font-semibold {{ $quiz->show_correct_answer ? 'text-green-600' : 'text-red-600' }}">{{ $quiz->show_correct_answer ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Show Wrong Answers</span>
                            <span class="font-semibold {{ $quiz->show_wrong_answer ? 'text-green-600' : 'text-red-600' }}">{{ $quiz->show_wrong_answer ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Show Explanations</span>
                            <span class="font-semibold {{ $quiz->show_explanation ? 'text-green-600' : 'text-red-600' }}">{{ $quiz->show_explanation ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Attempts Summary -->
                @if($quiz->attempts->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Attempts</h3>
                        <div class="space-y-3">
                            @foreach($quiz->attempts->take(5) as $attempt)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $attempt->user->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $attempt->completed_at?->diffForHumans() ?? 'In Progress' }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $attempt->score >= 70 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $attempt->score ?? 'N/A' }}%
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

