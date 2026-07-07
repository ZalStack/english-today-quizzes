<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')
            ->with('division')
            ->paginate(10);
        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $divisions = Division::all();
        return view('hr.employees.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'division_id' => 'nullable|exists:divisions,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $validated['name'] = $validated['full_name'];
        $validated['role'] = 'employee';
        $validated['status'] = 'active';
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function edit(User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $divisions = Division::all();
        return view('hr.employees.edit', compact('employee', 'divisions'));
    }

    public function update(Request $request, User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'division_id' => 'nullable|exists:divisions,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['name'] = $validated['full_name'];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $employee->update($validated);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $employee->delete();

        return redirect()->route('hr.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    public function resetPassword(Request $request, User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $newPassword = Str::random(10);
        $employee->update([
            'password' => Hash::make($newPassword)
        ]);

        return back()->with('success', "Password has been reset. New password: $newPassword");
    }
}
