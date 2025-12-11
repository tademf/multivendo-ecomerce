<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
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