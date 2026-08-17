@extends('layouts.hr')

@section('title', 'Manage Employees')
@section('header-title', 'Employees')
@section('header-subtitle', 'Manage employee accounts and access')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div></div>
    <div class="flex flex-wrap gap-3 w-full sm:w-auto">
        <a href="{{ route('hr.employees.bulk.division') }}" class="hr-btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-8 0 3 3 0 018 0z"/>
            </svg>
            Atur Divisi
        </a>
        <a href="{{ route('hr.employees.create') }}" class="hr-btn-primary">
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
                                <a href="{{ route('hr.employees.edit', $employee) }}" class="px-3 py-1.5 text-xs bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition font-semibold">
                                    Edit
                                </a>
                                <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-semibold" onclick="return confirm('Are you sure?')">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-8 0 3 3 0 018 0z"/>
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
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $employees->links() }}
        </div>
    @endif
</div>
@endsection
