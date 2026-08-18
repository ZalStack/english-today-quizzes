@extends('layouts.hr')

@section('title', 'Bulk Assign Division')
@section('header-title', 'Atur Divisi Pegawai')
@section('header-subtitle', 'Tetapkan divisi untuk setiap pegawai')

@section('content')
<div class="mb-4">
    <a href="{{ route('hr.employees.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="hr-card overflow-hidden">
    <form action="{{ route('hr.employees.bulk.division.update') }}" method="POST">
        @csrf

        <div class="table-responsive">
            <table class="hr-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th class="hidden sm:table-cell">Email</th>
                        <th>Divisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($employees as $employee)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="hr-avatar w-9 h-9 text-xs">
                                        {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-slate-900 text-sm">{{ $employee->full_name }}</span>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell text-slate-500 text-sm">{{ $employee->email }}</td>
                            <td>
                                <select name="divisions[{{ $employee->id }}]" class="hr-input text-sm">
                                    <option value="">-- Tanpa Divisi --</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ $employee->division_id == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100/80 flex flex-col sm:flex-row justify-between items-center gap-3">
            <p class="text-sm text-slate-500">{{ $employees->count() }} pegawai</p>
            <button type="submit" class="hr-btn-primary">Simpan Semua Divisi</button>
        </div>
    </form>
</div>
@endsection
