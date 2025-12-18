<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Inertia\Inertia; // Add this

class ProfileController extends Controller
{
    /**
     * Show the profile settings page (for old route)
     */
    public function settings()
    {
        /** @var User $user */
        $user = Auth::user();
        
        return Inertia::render('SettingsPage', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name ?? $user->full_name ?? 'User',
                'email' => $user->email,
                'account_number' => $user->account_number,
                'phone' => $user->phone,
                'created_at' => $user->created_at->format('d M, Y'),
            ]
        ]);
    }
    
    /**
     * Update settings (for old route)
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'account_number' => 'nullable|string|max:50|unique:users,account_number,' . Auth::id(),
        ]);
        
        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'account_number' => $request->account_number,
        ]);
        
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
    
    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        /** @var User $user */
        $user = Auth::user();
        
        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::delete('public/' . $user->profile_picture);
        }

        // Store the new image
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');

        // Update user profile picture
        $user->profile_picture = $path;
        $user->save();

        return response()->json([
            'message' => 'Profile picture uploaded successfully',
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'profile_picture' => Storage::url($path),
            ]
        ]);
    }
}