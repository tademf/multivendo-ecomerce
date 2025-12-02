<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    // LIST ALL USERS
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => \App\Models\User::all()
        ]);
    }

    // CREATE PAGE
    public function create()
    {
        return Inertia::render('Users/Create');
    }

    // STORE USER
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6',
            'phone' => 'nullable',
            'national_id' => 'nullable',
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'national_id' => $request->national_id,
        ]);

        return redirect()->route('users.index');
    }

    // EDIT PAGE
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user
        ]);
    }

    // UPDATE USER
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|unique:users,email,' . $user->id,
            'phone' => 'nullable',
            'national_id' => 'nullable',
        ]);

        $user->update($request->only([
            'username',
            'email',
            'phone',
            'national_id'
        ]));

        return redirect()->route('users.index');
    }

    // DELETE USER
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }
}
