<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function edit(Request $request)
    {
        $user = User::find(auth()->id());
        if (!$user) {
            return $this->error('User not found', 404);
        }
        return $this->success($user);
    }

    public function update(Request $request)
    {
        $user = User::find(auth()->id());
        if (!$user) {
            return $this->error('User not found', 404);
        }
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'avatar' => 'nullable|string',
        ]);
        $user->update($validated);
        return $this->success($user, 'Profile updated');
    }

    public function updatePassword(Request $request)
    {
        $user = User::find(auth()->id());
        if (!$user) {
            return $this->error('User not found', 404);
        }
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return $this->error('Current password is incorrect', 422);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();
        return $this->success(null, 'Password updated');
    }
}
