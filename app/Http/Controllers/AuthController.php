<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    // Show registration form
    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    // Handle user registration
    public function register(Request $request)
    {
        // For now, just redirect to home to test
        return redirect('/');
    }

    // Handle user login
    public function login(Request $request)
    {
        // For now, just redirect to home to test
        return redirect('/');
    }

    // Handle user logout
    public function logout(Request $request)
    {
        return redirect('/');
    }
}
