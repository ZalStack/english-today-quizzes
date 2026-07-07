@extends('layouts.app')

@section('title', 'Manage Quizzes')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Quizzes</h2>
                    <a href="{{ route('hr.quizzes.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Create Quiz
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($quizzes as $quiz)
                        <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition">
                            @if($quiz->thumbnail)
                                <img src="{{ asset('storage/' . $quiz->thumbnail) }}" alt="{{ $quiz->title }}" class="w-full h-48 object-cover rounded-t-lg">
                            @else
                                <div class="w-full h-48 bg-gradient-to-r from-blue-500 to-purple-600 rounded-t-lg flex items-center justify-center">
                                    <span class="text-4xl text-white font-bold">{{ substr($quiz->title, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $quiz->title }}</h3>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $quiz->status === 'active' ? 'bg-green-100 text-green-800' :
                                           ($quiz->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($quiz->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($quiz->description, 100) }}</p>
                                <div class="flex justify-between text-sm text-gray-500 mb-3">
                                    <span>{{ $quiz->questions_count }} Questions</span>
                                    <span>{{ $quiz->duration }} min</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('hr.quizzes.show', $quiz) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View Details</a>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}" class="text-green-600 hover:text-green-900 text-sm">Questions</a>
                                        <a href="{{ route('hr.quizzes.edit', $quiz) }}" class="text-blue-600 hover:text-blue-900 text-sm">Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $quizzes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
