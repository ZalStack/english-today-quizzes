<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $employees = User::where('role', 'employee')->with('division')->get();
        return $this->success($employees);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'division_id' => 'nullable|exists:divisions,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'avatar' => 'nullable|string',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';
        $user = new User($validated);
        $user->role = 'employee';
        $user->save();
        return $this->success($user, 'Employee created', 201);
    }

    public function show($id)
    {
        $user = User::where('role', 'employee')->with('division')->find($id);
        if (!$user) {
            return $this->error('Employee not found', 404);
        }
        return $this->success($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', 'employee')->find($id);
        if (!$user) {
            return $this->error('Employee not found', 404);
        }
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'division_id' => 'nullable|exists:divisions,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'avatar' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
        ]);
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        $user->update($validated);
        return $this->success($user, 'Employee updated');
    }

    public function destroy($id)
    {
        $user = User::where('role', 'employee')->find($id);
        if (!$user) {
            return $this->error('Employee not found', 404);
        }
        $user->delete();
        return $this->success(null, 'Employee deleted');
    }

    // Bulk update division
    public function bulkDivision(Request $request)
    {
        $divisions = Division::all();
        return $this->success($divisions);
    }

    public function bulkDivisionUpdate(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        User::whereIn('id', $validated['user_ids'])->where('role', 'employee')
            ->update(['division_id' => $validated['division_id']]);
        return $this->success(null, 'Employees division updated');
    }
}
