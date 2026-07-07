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
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('employee.quizzes.enroll') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="enroll_key" class="block text-sm font-semibold text-gray-700 mb-2">Enrollment Key</label>
                        <div class="relative">
                            <input type="text" name="enroll_key" id="enroll_key" required
                                class="w-full px-4 py-4 text-lg rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Enter your enrollment key">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('enroll_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-blue-50 rounded-xl p-6 mb-6">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-blue-800 mb-1">How to get the enrollment key?</p>
                                <p class="text-sm text-blue-600">Contact your HR department to get the enrollment key for the quiz you need to take.</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-lg rounded-xl hover:shadow-lg transition-all duration-300 font-bold transform hover:scale-105">
                        Join Quiz Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
