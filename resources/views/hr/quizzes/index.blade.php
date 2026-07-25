@extends('layouts.app')

@section('title', 'Manage Quizzes')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3 sm:gap-0">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Quizzes</h1>
                <p class="text-gray-500 text-sm sm:text-base mt-1">Create and manage assessment quizzes</p>
            </div>
            <a href="{{ route('hr.quizzes.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition font-medium text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Quiz
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="table-responsive">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Quiz</th>
                            <th class="hidden sm:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Attempts</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-right text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($quizzes as $quiz)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <div class="flex items-center space-x-2 sm:space-x-3">
                                        @if($quiz->thumbnail)
                                            <img src="{{ asset('storage/'.$quiz->thumbnail) }}" class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-7 h-7 sm:w-10 sm:h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-xs sm:text-sm flex-shrink-0">
                                                {{ strtoupper(substr($quiz->title, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="font-semibold text-gray-900 text-sm truncate max-w-[100px] sm:max-w-xs">{{ $quiz->title }}</span>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-3 sm:px-6 py-3 sm:py-4 text-gray-600 text-sm">{{ $quiz->category->name ?? '-' }}</td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-xs font-semibold rounded-full {{ $quiz->status === 'active' ? 'bg-green-100 text-green-700' : ($quiz->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ ucfirst($quiz->status) }}
                                    </span>
                                </td>
                                <td class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 text-gray-600 text-sm">{{ $quiz->duration }} min</td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-sm font-bold text-gray-900">{{ $quiz->attempts_count }}</td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-right">
                                    <div class="flex items-center justify-end space-x-1 sm:space-x-2">
                                        <a href="{{ route('hr.quizzes.show', $quiz) }}"
                                           class="px-2 py-1 sm:px-3 sm:py-1.5 text-[10px] sm:text-xs bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition font-medium">
                                            View
                                        </a>
                                        <a href="{{ route('hr.quizzes.questions.index', $quiz) }}"
                                           class="px-2 py-1 sm:px-3 sm:py-1.5 text-[10px] sm:text-xs bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition font-medium">
                                            Q
                                        </a>
                                        <a href="{{ route('hr.quizzes.edit', $quiz) }}"
                                           class="px-2 py-1 sm:px-3 sm:py-1.5 text-[10px] sm:text-xs bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium">
                                            Edit
                                        </a>
                                        <form action="{{ route('hr.quizzes.destroy', $quiz) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2 py-1 sm:px-3 sm:py-1.5 text-[10px] sm:text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium"
                                                    onclick="return confirm('Delete this quiz?')">
                                                Del
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 sm:px-6 py-8 sm:py-12 text-center">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium text-sm sm:text-base">No quizzes yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($quizzes instanceof \Illuminate\Pagination\AbstractPaginator && $quizzes->hasPages())
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100">
                    {{ $quizzes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
