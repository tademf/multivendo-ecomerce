<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VerificationController extends Controller
{
    /**
     * Show verification request page
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get the latest verification request
        $latestRequest = VerificationRequest::where('user_id', $user->id)
            ->latest()
            ->first();

        // Check if user is already verified AND has an approved request
        $isFullyVerified = $user->is_verified && 
                          $latestRequest && 
                          $latestRequest->status === 'approved';

        return Inertia::render('Verification/Request', [
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'user_type' => $user->user_type ?? 'Customer',
                'is_verified' => $user->is_verified,
                'verified_at' => $user->verified_at,
            ],
            'verificationRequest' => $latestRequest,
            'isFullyVerified' => $isFullyVerified,
        ]);
    }

    /**
     * Handle verification submission
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is already fully verified (approved) - CANNOT resubmit
        $latestRequest = VerificationRequest::where('user_id', $user->id)
            ->latest()
            ->first();
            
        if ($user->is_verified && $latestRequest && $latestRequest->status === 'approved') {
            return redirect()->route('home')
                ->with('error', 'Your account is already verified and cannot submit new requests.');
        }

        // Check for existing pending request
        $existingPendingRequest = VerificationRequest::where('user_id', $user->id)
            ->where('status', VerificationRequest::STATUS_PENDING)
            ->exists();

        if ($existingPendingRequest) {
            return redirect()->route('verification.request')
                ->with('error', 'You already have a pending verification request.');
        }

        $request->validate([
            'national_id' => 'required|string|max:50',
            'national_id_image' => 'required|string',
            'agree_terms' => 'required|accepted',
        ], [
            'agree_terms.accepted' => 'You must agree to the terms and conditions.',
        ]);

        try {
            // Check if national ID is already used by another user
            $existingNationalId = VerificationRequest::where('national_id', $request->national_id)
                ->where('user_id', '!=', $user->id)
                ->exists();

            if ($existingNationalId) {
                return redirect()->back()
                    ->withErrors(['national_id' => 'This national ID has already been used for verification by another user.'])
                    ->withInput();
            }

            // Create verification request
            VerificationRequest::create([
                'user_id' => $user->id,
                'national_id' => $request->national_id,
                'national_id_image' => $request->national_id_image,
                'status' => VerificationRequest::STATUS_PENDING,
            ]);

            // Redirect to home page with success message
            return redirect()->route('home')->with([
                'success' => 'Verification request submitted successfully!',
                'verification_submitted' => true,
            ]);

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to submit verification request. Please try again.')
                ->withInput();
        }
    }

    /**
     * Upload ID image
     */
    public function uploadIdImage(Request $request)
    {
        $request->validate([
            'national_id_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $user = Auth::user();
            
            // Create directory if it doesn't exist
            $directory = 'public/verification_ids/' . $user->id;
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            // Store the file
            $path = $request->file('national_id_image')->store(
                'verification_ids/' . $user->id,
                'public'
            );

            return response()->json([
                'success' => true,
                'file_path' => $path,
                'url' => Storage::url($path),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image.',
            ], 500);
        }
    }

    /**
     * Admin: Approve verification request
     */
    public function approve(VerificationRequest $verificationRequest)
    {
        // This is for admin panel - you can implement admin check
        $verificationRequest->approve(Auth::id());
        
        return response()->json([
            'success' => true,
            'message' => 'Verification request approved.',
        ]);
    }

    /**
     * Admin: Reject verification request
     */
    public function reject(VerificationRequest $verificationRequest, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $verificationRequest->reject($request->rejection_reason, Auth::id());
        
        return response()->json([
            'success' => true,
            'message' => 'Verification request rejected.',
        ]);
    }
}