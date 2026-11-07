<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    function login()
    {
        return view('auth.login');
    }

   

    
public function doLogin(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $key = Str::transliterate(
        Str::lower($request->input('email')) . '|' . $request->ip()
    );

    // Maximum of 5 failed attempts within 60 seconds.
    if (RateLimiter::tooManyAttempts($key, 5)) {
        throw ValidationException::withMessages([
            'email' => 'Too many login attempts. Please try again later.',
        ]);
    }

    // Remember me checkbox.
    $remember = $request->boolean('remember');

    if (Auth::attempt($credentials, $remember)) {

        RateLimiter::clear($key);

        // Regenerate session ID after successful login.
        $request->session()->regenerate();

        $user = Auth::user();
        $role = $user->role?->name;

        if ($role === 'admin') {
            return redirect('/dashboard/admin');
        }

        if ($role === 'personnel') {
            return redirect('/dashboard/personnel');
        }

        if ($role === 'superadmin') {
            return redirect('/dashboard/superadmin');
        }

        if ($role === 'user') {
            return redirect('/dashboard/user');
        }

        // Reject accounts without a recognized role.
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->withErrors([
            'email' => 'Invalid email or password.',
        ]);
    }

    // Record failed login attempt.
    RateLimiter::hit($key, 60);

    return back()
        ->onlyInput('email')
        ->withErrors([
            'email' => 'Invalid email or password.',
        ]);
}


    function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    
}