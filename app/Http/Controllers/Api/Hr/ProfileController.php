<?php

namespace App\Http\Controllers\Api\Hr;

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
        $userId = $request->input('user_id');
        if (!$userId) {
            return $this->error('user_id required', 400);
        }
        $user = User::find($userId);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        return $this->success($user);
    }

    public function update(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return $this->error('user_id required', 400);
        }
        $user = User::find($userId);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'avatar' => 'nullable|string',
        ]);
        $user->update($validated);
        return $this->success($user, 'Profile updated');
    }

    public function updatePassword(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return $this->error('user_id required', 400);
        }
        $user = User::find($userId);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user->password = Hash::make($validated['password']);
        $user->save();
        return $this->success(null, 'Password updated');
    }
}
