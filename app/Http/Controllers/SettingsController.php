<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // UNCOMMENTED THIS LINE
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
                'name' => $user->name ?? $user->full_name, // FIXED: Check both name and full_name
                'email' => $user->email,
                'account_number' => $user->account_number ?? null,
                'phone' => $user->phone ?? '',
                'profile_picture' => $user->profile_picture_url ?? asset('images/default-avatar.png'),
                'created_at' => $user->created_at ? $user->created_at->format('d M, Y') : 'N/A',
                'is_verified' => !is_null($user->verified_at),
            ],
            'password_verified' => $passwordVerified
        ]);
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
        
        // Return to settings page
        return redirect()->route('settings.page')->with([
            'message' => 'Password verified successfully. You can now edit your profile.',
            'show_profile_modal' => true
        ]);
    }
    
    /**
     * Update profile information with password verification
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check if password was verified recently (within 5 minutes)
        $verifiedTime = $request->session()->get('password_verified_time', 0);
        if (now()->timestamp - $verifiedTime > 300) { // 5 minutes
            return redirect()->back()->withErrors([
                'current_password' => 'Password verification expired. Please verify again.'
            ]);
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'string', 'confirmed', 'min:8'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
        
        // Update user data - check which field your database has
        if (isset($user->full_name)) {
            $user->full_name = $request->name;
        } else {
            $user->name = $request->name;
        }
        
        $user->email = $request->email;
        $user->phone = $request->phone ?? null;
        
        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
                Storage::delete('public/' . $user->profile_picture);
            }
            
            // Store in public disk
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $path;
        }
        
        // Save the user - FIXED: Use update() if save() has issues
        try {
            $user->update(); // Use update() instead of save()
        } catch (\Exception $e) {
            // Fallback: Direct database update
            DB::table('users')->where('id', $user->id)->update([ // Changed \DB:: to DB::
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
                'profile_picture' => $user->profile_picture,
                'updated_at' => now(),
            ]);
        }
        
        // Clear verification after successful update
        $request->session()->forget(['password_verified', 'password_verified_time']);
        
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Update account number (only for verified users)
     */
    public function updateAccountNumber(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check if user is verified (verified_at is not null)
        if (is_null($user->verified_at)) {
            return redirect()->back()->withErrors(['account_number' => 'You must be verified to update your account number.']);
        }
        
        $request->validate([
            'account_number' => ['required', 'string', 'max:50', 'unique:users,account_number,' . $user->id],
        ]);
        
        // Update account number
        $user->account_number = $request->account_number;
        
        try {
            $user->update(); // Use update() instead of save()
        } catch (\Exception $e) {
            // Fallback: Direct database update
            DB::table('users')->where('id', $user->id)->update([ // Changed \DB:: to DB::
                'account_number' => $request->account_number,
                'updated_at' => now(),
            ]);
        }
        
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
        
        try {
            $user->update(); // Use update() instead of save()
        } catch (\Exception $e) {
            // Fallback: Direct database update
            DB::table('users')->where('id', $user->id)->update([ // Changed \DB:: to DB::
                'profile_picture' => $path,
                'updated_at' => now(),
            ]);
        }
        
        return redirect()->back()->with('success', 'Profile picture updated successfully!');
    }
    
    /**
     * Update password (separate from profile update)
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Update password
        $user->password = Hash::make($request->new_password);
        
        try {
            $user->update(); // Use update() instead of save()
        } catch (\Exception $e) {
            // Fallback: Direct database update
            DB::table('users')->where('id', $user->id)->update([ // Changed \DB:: to DB::
                'password' => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);
        }
        
        return redirect()->back()->with('success', 'Password updated successfully!');
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