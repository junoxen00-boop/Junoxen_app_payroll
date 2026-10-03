<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

/*
|--------------------------------------------------------------------------
| Force Password Change
|--------------------------------------------------------------------------
*/
if ($user->must_change_password) {
    return redirect()->route('password.change');
}

/*
|--------------------------------------------------------------------------
| Role Based Dashboard
|--------------------------------------------------------------------------
*/
if ($user->role->name === 'Admin') {
    return redirect()->route('dashboard');
}

if ($user->role->name === 'Manager') {
    return redirect()->route('manager.dashboard');
}

if ($user->role->name === 'Employee') {
    return redirect()->route('employee.dashboard');
}

abort(403);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}