<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 🔐 Login & regenerate session
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'status'  => true,
            'message' => 'Registered successfully'
        ]);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {

            // 🔥 VERY IMPORTANT
            $request->session()->regenerate();

            return response()->json([
                'status'  => true,
                'message' => 'Login successful'
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // 🔥 Clear session completely
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
