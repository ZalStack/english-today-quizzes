@extends('layouts.app')

@section('title', 'Join Quiz')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-indigo-600 to-purple-600">
                <h2 class="text-2xl font-bold text-white">Join a Quiz</h2>
                <p class="text-indigo-100 mt-1">Enter the enrollment key to start your assessment</p>
            </div>

            <div class="p-8">
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ route('employee.quizzes.enroll') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="enroll_key" class="block text-sm font-semibold text-gray-700 mb-2">Enrollment Key</label>
                        <input type="text" name="enroll_key" id="enroll_key"
                            value="{{ old('enroll_key') }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-center text-2xl font-bold tracking-widest uppercase"
                            placeholder="e.g., ABC123">
                        <p class="mt-2 text-sm text-gray-500">Enter the key provided by your HR administrator.</p>
                        @error('enroll_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                            Join Quiz
                        </button>
                    </div>
                </form>

                {{-- Informasi tambahan --}}
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">💡 How to get your enrollment key:</h4>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Ask your HR administrator for the quiz enrollment key</li>
                        <li>The key is a unique code for each quiz</li>
                        <li>You can only join active quizzes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
