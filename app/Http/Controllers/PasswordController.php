<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Show Change Password Form
     */
    public function showChangeForm()
    {
        return view('auth.change-password');
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        if ($user->role->name === 'Admin') {
            return redirect()->route('dashboard')
                ->with('success', 'Password changed successfully.');
        }

        if ($user->role->name === 'Manager') {
            return redirect()->route('manager.dashboard')
                ->with('success', 'Password changed successfully.');
        }

        if ($user->role->name === 'Employee') {
            return redirect()->route('employee.dashboard')
                ->with('success', 'Password changed successfully.');
        }

        abort(403);
    }
}