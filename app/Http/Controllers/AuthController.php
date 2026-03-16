<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('ngo-token')->plainTextToken;

        return response()->json([
            'message' => 'NGO registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (
            !$user ||
            !$user->is_admin ||
            !Hash::check($data['password'], $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials or admin access required'],
            ]);
        }

        $token = $user->createToken('ngo-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function ngoLogin(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|string',
        ]);

        $user = User::where('id', $data['user_id'])
            ->where('approved', true)
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'user_id' => ['Invalid credentials or NGO not approved'],
            ]);
        }

        $token = $user->createToken('ngo-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $token = $request->bearerToken();

        return response()->json(['message' => 'ME', 'data' => [
            'user' => $user,
            'token' => $token
        ]]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function approve(Request $request, User $user)
    {
        $data = $request->validate([
            'approved' => ['required', 'boolean'],
        ]);

        $user->update([
            'approved' => $data['approved'],
        ]);

        return response()->json([
            'message' => 'User approved successfully',
            'data' => $user,
        ]);
    }

    public function getApprovedNgos()
    {
        $ngo = User::where('approved', true)->get();
        return response()->json(['message' => 'all ngos fetched', 'data' => $ngo]);
    }

    public function ngos()
    {
        $ngo = User::all();
        return response()->json(['message' => 'all ngos fetched', 'data' => $ngo]);
    }
}
