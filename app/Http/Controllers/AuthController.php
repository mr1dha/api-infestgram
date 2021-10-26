<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed'
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid Credentials',
                'token' => $token
            ], 401);
        }

        $token = auth()->user()->createToken('token')->plainTextToken;
        $cookie = cookie('token', $token, 60 * 24);

        return response([
            'message' => 'Login Success',
            'token' => $token,
        ])->withCookie($cookie);
    }

    public function logout()
    {
        $cookie = \Cookie::forget('token');
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'Logged Out'
        ])->withCookie($cookie);
    }

    public function user()
    {
        return auth()->user();
    }
}
