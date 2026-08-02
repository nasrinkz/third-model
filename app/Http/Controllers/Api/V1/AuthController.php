<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function loginHome()
    {
        return response()->json(['message' => 'do login first']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);
        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['username' => ['The supplied credentials are incorrect.']]);
        }
        if (! $user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Administrator access is required.'], 403);
        }

        return response()->json([
            'success' => true,
            'token' => $user->createToken($credentials['device_name'] ?? 'admin-api')->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user->only(['id', 'name', 'username', 'email']),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['success' => true]);
    }
}
