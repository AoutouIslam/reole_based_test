<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //  Register
    public function register(Request $request)
    {
         // Validate input
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role' => 'required|string|in:admin,manager,user', 
    ]);

   
    $role = Roles::where('name', $data['role'])->first();

    if (!$role) {
        return response()->json([
            'message' => 'Invalid role selected. Role must exist in roles table.'
        ], 422);
    }
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role_id' => $role->id,
    ]);

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user,
    ]);
}

    //  Login
    public function login(Request $request)
    {
        

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
