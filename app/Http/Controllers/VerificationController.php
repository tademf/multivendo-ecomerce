<?php

namespace App\Http\Controllers;

use App\Models\VerificationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    /**
     * Show the verification request form.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user is already verified
        if ($user->is_verified || $user->verified_at) {
            return redirect()->route('dashboard')->with('info', 'Your account is already verified.');
        }
        
        // Check for pending request
        $pendingRequest = VerificationRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
        
        return Inertia::render('Verification/Request', [
            'user' => $user,
            'pendingRequest' => $pendingRequest,
        ]);
    }

    /**
     * Store a verification request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validation
        $request->validate([
            'national_id' => 'required|string|max:20|unique:verification_requests,national_id',
            'national_id_image' => 'required|string', // This should be the file path from upload
            'agree_terms' => 'required|accepted',
        ]);
        
        // Check if user already has a pending request
        $existingRequest = VerificationRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
            
        if ($existingRequest) {
            return redirect()->back()->withErrors([
                'pending' => 'You already have a pending verification request.'
            ]);
        }
        
        // Create verification request
        $verificationRequest = VerificationRequest::create([
            'user_id' => $user->id,
            'national_id' => $request->national_id,
            'national_id_image' => $request->national_id_image,
            'status' => 'pending',
        ]);
        
        return redirect()->route('dashboard')->with('success', 'Verification request submitted successfully!');
    }

    /**
     * Update user's account number.
     */
    public function updateAccountNumber(Request $request)
    {
        $request->validate([
            'account_number' => 'required|string|max:50|unique:users,account_number,' . Auth::id(),
        ]);
        
        User::where('id', Auth::id())->update([
            'account_number' => $request->account_number
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Account number updated successfully.'
        ]);
    }

    /**
     * Upload ID image - FIXED VERSION.
     * Use this if you're calling from JavaScript/Inertia
     */
    public function uploadIdImage(Request $request)
    {
        $request->validate([
            'national_id_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);
        
        $user = Auth::user();
        
        // Store the image
        $path = $request->file('national_id_image')->store(
            'verification-ids/' . $user->id,
            'public'
        );
        
        // Return as JSON for JavaScript/Inertia
        return response()->json([
            'success' => true,
            'file_path' => $path,
            'full_url' => Storage::url($path),
        ]);
    }

    /**
     * ALTERNATIVE: Upload ID image - Inertia version
     * Use this if you want to return Inertia response
     */
    public function uploadIdImageInertia(Request $request)
    {
        $request->validate([
            'national_id_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);
        
        $user = Auth::user();
        
        // Store the image
        $path = $request->file('national_id_image')->store(
            'verification-ids/' . $user->id,
            'public'
        );
        
        // Return to the same page with the file path
        return back()->with('uploaded_image', [
            'path' => $path,
            'url' => Storage::url($path),
        ]);
    }
}