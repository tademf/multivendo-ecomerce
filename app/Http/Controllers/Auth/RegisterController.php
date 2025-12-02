<?php
// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RegisterController extends Controller
{
    // Show registration page
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    // Handle registration
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'agree_terms' => 'required|accepted',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'national_id' => $validated['national_id'] ?? null,
        ]);

        auth()->login($user);

        return redirect()->route('login')->with('success', 'Account created!');
    }
}
