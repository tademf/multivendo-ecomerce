<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\User;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check if password was just verified
        $passwordVerified = $request->session()->get('password_verified', false);
        
        return Inertia::render('SettingsPage', [
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name, // Use full_name from your model
                'email' => $user->email,
                'account_number' => $user->account_number ?? null,
                'phone' => $user->phone ?? '',
                'profile_picture' => $user->profile_picture_url, // Use the accessor
                'created_at' => $user->created_at ? $user->created_at->format('d M, Y') : 'N/A',
                
                // Security questions
                'security_question' => $user->security_question ?? null,
                
                // Verification status
                'is_verified' => $user->is_verified,
                'verified_at' => $user->verified_at ? $user->verified_at->format('d M, Y, H:i') : null,
                'verification_status' => $this->getVerificationStatus($user),
                'user_type' => $user->user_type ?? 'Customer',
            ],
            'password_verified' => $passwordVerified
        ]);
    }
    
    /**
     * Get verification status for debugging
     */
    private function getVerificationStatus(User $user): string
    {
        if ($user->is_verified && $user->verified_at) {
            return 'Verified (' . $user->verified_at->format('Y-m-d H:i:s') . ')';
        }
        
        if (!$user->is_verified && $user->verified_at) {
            return 'Inconsistent (verified_at set but is_verified=false)';
        }
        
        if ($user->is_verified && !$user->verified_at) {
            return 'Inconsistent (is_verified=true but verified_at null)';
        }
        
        return 'Not verified';
    }
    
    /**
     * Verify current password before allowing profile edits
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);
        
        // Store verification in session (expires in 5 minutes)
        $request->session()->put('password_verified', true);
        $request->session()->put('password_verified_time', now()->timestamp);
        
        return redirect()->route('settings.page')->with([
            'message' => 'Password verified successfully. You can now edit your profile.',
            'show_profile_modal' => true
        ]);
    }
    
    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
        
        $user->full_name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
                Storage::delete('public/' . $user->profile_picture);
            }
            
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $path;
        }
        
        $user->save();
        
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Update account number (only for verified users)
     */
    public function updateAccountNumber(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check if user is verified
        if (!$user->is_verified) {
            return redirect()->back()->withErrors([
                'account_number' => 'You must be verified to update your account number.'
            ]);
        }
        
        $request->validate([
            'account_number' => ['required', 'string', 'max:50', 'unique:users,account_number,' . $user->id],
        ]);
        
        $user->account_number = $request->account_number;
        $user->save();
        
        return redirect()->back()->with('success', 'Account number updated successfully!');
    }
    
    /**
     * Update profile picture only
     */
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Delete old profile picture if exists
        if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
            Storage::delete('public/' . $user->profile_picture);
        }
        
        // Store the new image
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        
        // Update profile picture
        $user->profile_picture = $path;
        $user->save();
        
        return redirect()->back()->with('success', 'Profile picture updated successfully!');
    }
    
    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
        
        return redirect()->back()->with('success', 'Password updated successfully!');
    }
    
    /**
     * Update security questions and answers
     */
    public function updateSecurityQuestions(Request $request)
    {
        $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'min:4', 'max:100'],
            'answer_confirmation' => ['required', 'same:answer'],
            'current_password' => ['required', 'current_password'],
        ], [
            'answer_confirmation.same' => 'The security answers do not match.',
        ]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Update security question and hash the answer
        $user->security_question = $request->question;
        $user->security_answer_hash = Hash::make(strtolower(trim($request->answer)));
        $user->save();
        
        return redirect()->back()->with('success', 'Security questions updated successfully!');
    }
    
    /**
     * Delete account
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Delete profile picture if exists
        if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
            Storage::delete('public/' . $user->profile_picture);
        }
        
        // Logout the user
        Auth::logout();
        
        // Delete the user
        $user->delete();
        
        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Your account has been deleted successfully.');
}
    }