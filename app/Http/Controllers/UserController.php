<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('role')
            ->orderBy('name')
            ->get();

        return view('users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

      if (!empty($validated['password'])) {

    $user->password = Hash::make($validated['password']);

    // Force user to change password after admin reset
    $user->must_change_password = true;
}

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }
}