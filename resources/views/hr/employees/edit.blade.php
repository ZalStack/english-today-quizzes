@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 sm:px-8 py-4 sm:py-6 bg-gradient-to-r from-indigo-600 to-purple-600">
                <h2 class="text-xl sm:text-2xl font-bold text-white">Edit Employee</h2>
                <p class="text-indigo-100 text-sm mt-1">Update employee information</p>
            </div>

            <form action="{{ route('hr.employees.update', $employee) }}" method="POST" class="p-4 sm:p-8 space-y-4 sm:space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div class="md:col-span-2">
                        <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Full Name</label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $employee->full_name) }}" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base">
                        @error('full_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="division_id" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Division</label>
                        <select name="division_id" id="division_id"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base">
                            <option value="">Select Division</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id', $employee->division_id) == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Status</label>
                        <select name="status" id="status" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base">
                            <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">New Password (optional)</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base"
                            placeholder="Leave blank to keep current">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base"
                            placeholder="Confirm new password">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Address</label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm sm:text-base">{{ old('address', $employee->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4 pt-3 sm:pt-4">
                    <a href="{{ route('hr.employees.index') }}"
                       class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium text-center text-sm sm:text-base">
                        Cancel
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium text-sm sm:text-base">
                        Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
