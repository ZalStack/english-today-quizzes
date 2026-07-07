@extends('layouts.app')

@section('title', 'Manage Quizzes')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Quizzes</h1>
                <p class="text-gray-500 mt-1">Create and manage assessment quizzes</p>
            </div>
            <a href="{{ route('hr.quizzes.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Quiz
            </a>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Quiz Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($quizzes as $quiz)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
                    <!-- Thumbnail -->
                    <div class="relative h-48 bg-gradient-to-br from-indigo-500 to-purple-600">
                        @if($quiz->thumbnail)
                            <img src="{{ asset('storage/' . $quiz->thumbnail) }}" alt="{{ $quiz->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $quiz->status === 'active' ? 'bg-green-500 text-white' : ($quiz->status === 'draft' ? 'bg-yellow-500 text-white' : 'bg-gray-500 text-white') }}">
                                {{ ucfirst($quiz->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $quiz->title }}</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $quiz->description ?? 'No description' }}</p>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $quiz->questions_count }} Q
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $quiz->duration }} min
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ $quiz->attempts_count }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('hr.quizzes.show', $quiz) }}"
                               class="flex-1 px-3 py-2 text-sm bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition text-center font-medium">
                                View
                            </a>
                            <a href="{{ route('hr.quizzes.questions.index', $quiz) }}"
                               class="flex-1 px-3 py-2 text-sm bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-center font-medium">
                                Questions
                            </a>
                            <a href="{{ route('hr.quizzes.edit', $quiz) }}"
                               class="flex-1 px-3 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-center font-medium">
                                Edit
                            </a>
                            <form action="{{ route('hr.quizzes.destroy', $quiz) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full px-3 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium"
                                        onclick="return confirm('Delete this quiz?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
                        <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-lg font-medium">No quizzes yet</p>
                        <p class="text-gray-400 mt-1">Create your first quiz to get started</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $quizzes->links() }}
        </div>
    </div>
</div>
@endsection
