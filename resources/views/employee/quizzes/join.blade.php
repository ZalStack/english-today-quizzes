@extends('layouts.app')

@section('title', 'Join Quiz')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Join a Quiz</h2>

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('employee.quizzes.enroll') }}" method="POST" class="max-w-md">
                    @csrf

                    <div class="mb-4">
                        <label for="enroll_key" class="block text-sm font-medium text-gray-700">Enrollment Key</label>
                        <input type="text" name="enroll_key" id="enroll_key" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Enter the enrollment key provided by HR">
                        @error('enroll_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <p class="text-sm text-blue-700">
                            <strong>Note:</strong> The enrollment key is provided by your HR department.
                            If you don't have a key, please contact your HR representative.
                        </p>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                        Join Quiz
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
