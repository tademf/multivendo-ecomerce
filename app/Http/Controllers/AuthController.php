<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AuthController extends Controller
{
    // ============================================
    // REGISTRATION
    // ============================================
    
    // Show registration form
    public function showRegister()
    {
        return Inertia::render('RegisterPage');
    }

    // Register new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'agreeTerms' => 'accepted'
        ], [
            'agreeTerms.accepted' => 'You must agree to the Terms of Service and Privacy Policy.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create the user with 'active' status
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active', // Default status is active
            'security_question' => 'What city were you born in?',
            'security_answer_hash' => Hash::make('default'),
            'is_verified' => false,
        ]);

        // Redirect to login page
        return redirect()->route('login')->with('success', 'Registration successful! Please login to continue.');
    }

    // ============================================
    // LOGIN WITH STATUS CHECK
    // ============================================
    
    // Show login form
    public function showLogin()
    {
        return Inertia::render('LoginPage');
    }

    // Login user with status check
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // First check if user exists
        $user = User::where('email', $credentials['email'])->first();

        // If user not found
        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Check user status before attempting login
        if ($user->status === 'banned') {
            return back()->withErrors([
                'email' => 'Your account has been banned. Please contact support.',
                'status' => 'banned'
            ])->onlyInput('email');
        }

        if ($user->status === 'inactive') {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact support.',
                'status' => 'inactive'
            ])->onlyInput('email');
        }

        // Only active users can attempt login
        if ($user->status === 'active') {
            if (Auth::attempt($credentials, $request->remember)) {
                $request->session()->regenerate();
                
                // Redirect to homepage
                return redirect('/')->with('success', 'Login successful! Welcome back.');
            }

            // If credentials don't match
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Fallback for any other status
        return back()->withErrors([
            'email' => 'Your account is not active. Please contact support.',
        ])->onlyInput('email');
    }

    // ============================================
    // LOGOUT
    // ============================================
    
    // Logout user
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    // ============================================
    // SESSION MANAGEMENT
    // ============================================
    
    public function checkSession(Request $request)
    {
        return response()->json([
            'logged_in' => Auth::check(),
            'user' => Auth::check() ? [
                'id' => Auth::user()->id,
                'email' => Auth::user()->email,
                'full_name' => Auth::user()->full_name,
                'status' => Auth::user()->status,
            ] : null,
        ]);
    }
}