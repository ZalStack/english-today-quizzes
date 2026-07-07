@extends('layouts.app')

@section('title', 'Questions - ' . $quiz->title)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Questions</h2>
                        <p class="text-gray-600">{{ $quiz->title }}</p>
                    </div>
                    <a href="{{ route('hr.quizzes.questions.create', $quiz) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Add Question
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if($questions->count() > 0)
                    <div class="space-y-4">
                        @foreach($questions as $index => $question)
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold mb-2">
                                            Question {{ $index + 1 }}
                                            <span class="text-sm text-gray-500">({{ ucfirst(str_replace('_', ' ', $question->question_type)) }})</span>
                                        </h3>
                                        <p class="text-gray-800 mb-3">{{ $question->question_text }}</p>

                                        @if($question->options && is_array($question->options))
                                            <div class="ml-4 space-y-1">
                                                @foreach($question->options as $key => $option)
                                                    <p class="text-sm {{ $option === $question->correct_answer ? 'text-green-600 font-semibold' : 'text-gray-600' }}">
                                                        {{ chr(65 + $key) }}. {{ $option }}
                                                        @if($option === $question->correct_answer)
                                                            ✓
                                                        @endif
                                                    </p>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-green-600 font-semibold">Answer: {{ $question->correct_answer }}</p>
                                        @endif

                                        @if($question->explanation)
                                            <p class="text-sm text-blue-600 mt-2">Explanation: {{ $question->explanation }}</p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2 ml-4">
                                        <a href="{{ route('hr.quizzes.questions.edit', [$quiz, $question]) }}"
                                            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('hr.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm"
                                                onclick="return confirm('Are you sure?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $questions->links() }}
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No questions added yet. Click "Add Question" to get started.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
