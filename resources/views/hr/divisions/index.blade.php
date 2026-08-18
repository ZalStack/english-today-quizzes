@extends('layouts.hr')

@section('title', 'Manage Divisions')
@section('header-title', 'Divisions')
@section('header-subtitle', 'Manage company departments and teams')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8 animate-stagger">
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-primary-50">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">Total</span>
        </div>
        <p class="text-sm font-medium text-slate-500">Total Divisions</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $totalDivisions }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-blue-50">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-8 0 3 3 0 018 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">Employees Assigned</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $totalEmployeesInDivisions }}</p>
    </div>
    <div class="hr-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="hr-stat__icon bg-emerald-50">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-medium text-slate-500">Avg per Division</p>
        <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ $averagePerDivision }}</p>
    </div>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h3 class="text-base font-bold text-slate-900 tracking-tight">All Divisions</h3>
    <a href="{{ route('hr.divisions.create') }}" class="hr-btn-primary w-full sm:w-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Division
    </a>
</div>

<div class="hr-card overflow-hidden">
    <div class="table-responsive">
        <table class="hr-table">
            <thead>
                <tr>
                    <th>Division</th>
                    <th class="hidden sm:table-cell">Description</th>
                    <th>Employees</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($divisions as $division)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="hr-avatar w-9 h-9 text-xs">
                                    {{ strtoupper(substr($division->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-900 text-sm">{{ $division->name }}</span>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell text-slate-500">{{ $division->description ?? '-' }}</td>
                        <td>
                            <span class="hr-badge hr-badge--info">{{ $division->users_count }}</span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('hr.divisions.edit', $division) }}" class="px-3 py-1.5 text-xs bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-all duration-200 font-semibold">
                                    Edit
                                </a>
                                <form action="{{ route('hr.divisions.destroy', $division) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all duration-200 font-semibold" onclick="return confirm('Delete this division?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="hr-empty">
                                <div class="hr-empty__icon">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium">No divisions yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($divisions instanceof \Illuminate\Pagination\AbstractPaginator && $divisions->hasPages())
        <div class="px-6 py-4 border-t border-slate-100/80">
            {{ $divisions->links() }}
        </div>
    @endif
</div>
@endsection
