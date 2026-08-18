@extends('layouts.hr')

@section('title', 'Manage Employees')
@section('header-title', 'Employees')
@section('header-subtitle', 'Manage employee accounts and access')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-stagger">
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-primary-50">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">Total</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Employees</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $totalEmployees }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-emerald-50">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">Active</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Active</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $activeEmployees }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-blue-50">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">In Division</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $withDivision }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-amber-50">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            @if($withoutDivision > 0)
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">Needs assign</span>
            @endif
        </div>
        <p class="text-sm font-medium text-slate-500">No Division</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $withoutDivision }}</p>
    </div>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h3 class="text-base font-bold text-slate-900 tracking-tight">All Employees</h3>
    <div class="flex flex-wrap gap-3 w-full sm:w-auto">
        <a href="{{ route('hr.employees.bulk.division') }}" class="hr-btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-8 0 3 3 0 018 0z"/>
            </svg>
            Atur Divisi
        </a>
        <a href="{{ route('hr.employees.create') }}" class="hr-btn-primary w-full sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Add Employee
        </a>
    </div>
</div>

<div class="hr-card overflow-hidden">
    <div class="table-responsive">
        <table class="hr-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th class="hidden sm:table-cell">Email</th>
                    <th class="hidden md:table-cell">Division</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="hr-avatar w-9 h-9 text-xs">
                                    {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-900 text-sm truncate max-w-[120px] sm:max-w-xs">{{ $employee->full_name }}</span>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell text-slate-500">{{ $employee->email }}</td>
                        <td class="hidden md:table-cell text-slate-500">{{ $employee->division->name ?? '-' }}</td>
                        <td>
                            <span class="hr-badge {{ $employee->status === 'active' ? 'hr-badge--success' : 'hr-badge--danger' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('hr.employees.edit', $employee) }}" class="px-3 py-1.5 text-xs bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-all duration-200 font-semibold">
                                    Edit
                                </a>
                                <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all duration-200 font-semibold" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="hr-empty">
                                <div class="hr-empty__icon">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium">No employees yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($employees instanceof \Illuminate\Pagination\AbstractPaginator && $employees->hasPages())
        <div class="px-6 py-4 border-t border-slate-100/80">
            {{ $employees->links() }}
        </div>
    @endif
</div>
@endsection
