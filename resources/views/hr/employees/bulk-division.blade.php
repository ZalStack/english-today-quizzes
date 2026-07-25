@extends('layouts.app')

@section('title', 'Bulk Assign Division')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4 sm:mb-6">
            <a href="{{ route('hr.employees.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">&larr; Kembali</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 sm:py-5 bg-gradient-to-r from-indigo-600 to-purple-600">
                <h2 class="text-lg sm:text-xl font-bold text-white">Atur Divisi Pegawai</h2>
                <p class="text-indigo-100 text-xs sm:text-sm mt-1">Tetapkan divisi untuk setiap pegawai</p>
            </div>

            <form action="{{ route('hr.employees.bulk.division.update') }}" method="POST">
                @csrf

                @if(session('success'))
                    <div class="mx-4 sm:mx-6 mt-3 sm:mt-4 p-3 sm:p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm sm:text-base">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left px-3 sm:px-6 py-2 sm:py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="hidden sm:table-cell text-left px-3 sm:px-6 py-2 sm:py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="text-left px-3 sm:px-6 py-2 sm:py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Divisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($employees as $employee)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-3 sm:px-6 py-2 sm:py-3">
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            <div class="w-7 h-7 sm:w-9 sm:h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-xs sm:text-sm shrink-0">
                                                {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                                            </div>
                                            <span class="font-semibold text-gray-900 text-xs sm:text-sm">{{ $employee->full_name }}</span>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-3 sm:px-6 py-2 sm:py-3 text-xs sm:text-sm text-gray-500">{{ $employee->email }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-3">
                                        <select name="divisions[{{ $employee->id }}]"
                                                class="w-full px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-xs sm:text-sm">
                                            <option value="">-- Tanpa Divisi --</option>
                                            @foreach($divisions as $division)
                                                <option value="{{ $division->id }}" {{ $employee->division_id == $division->id ? 'selected' : '' }}>
                                                    {{ $division->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-0">
                    <p class="text-xs sm:text-sm text-gray-500">{{ $employees->count() }} pegawai</p>
                    <button type="submit"
                            class="w-full sm:w-auto px-4 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold text-sm sm:text-base">
                        Simpan Semua Divisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
