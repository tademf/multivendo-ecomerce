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
    // Show registration form
    public function showRegister()
    {
        return Inertia::render('RegisterPage');
    }

    // Register new user (for Inertia)
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

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Log in the user WITHOUT "remember me"
        Auth::login($user, false); // false = don't remember

        return redirect()->route('dashboard')->with([
            'success' => 'Registration successful! Welcome to E-Shop.'
        ]);
    }

    // Show login form
    public function showLogin()
    {
        return Inertia::render('LoginPage');
    }

    // Login user (for Inertia) - UPDATED: Force no "remember me"
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // IGNORE the "remember" checkbox - ALWAYS use false
        // This ensures session expires when browser closes
        if (Auth::attempt($credentials, false)) { // false = never remember
            $request->session()->regenerate();
            
            // Force session configuration for browser-close expiry
            // This overrides any configuration
            config(['session.expire_on_close' => true]);

            // TEMPORARY: Redirect to home instead of dashboard for testing
            return redirect('/')->with([
                'success' => 'Login successful!'
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Logout user
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    // Get authenticated user (for API - if you still need it)
    public function me(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json($request->user());
        }

        return Inertia::render('Profile/Index', [
            'auth' => [
                'user' => $request->user()
            ]
        ]);
    }

    // API-only method for token-based authentication
    public function apiRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    // API-only method for token-based login
    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    // API-only logout
    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    // Update verification data (for admin)
    public function verifyUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'national_id' => 'required|string|unique:users',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->national_id = $request->national_id;
        $user->verified_at = now();
        $user->save();

        return response()->json([
            'message' => 'User verified successfully',
            'user' => $user
        ]);
    }
    
    // Force logout middleware check (optional)
    public function checkSession(Request $request)
    {
        // This can be called from your frontend to check session status
        return response()->json([
            'logged_in' => Auth::check(),
            'session_active' => $request->session()->isStarted(),
            'user' => Auth::user() ? [
                'id' => Auth::user()->id,
                'email' => Auth::user()->email,
                'full_name' => Auth::user()->full_name,
            ] : null,
        ]);
    }
}