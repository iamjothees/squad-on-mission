<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
        ]);

        $user = \App\Models\User::where('username', $request->username)->first();

        if ($user && is_null($user->password)) {
            \Illuminate\Support\Facades\Password::sendResetLink(['email' => $user->email]);
            return back()->withErrors([
                'username' => 'Your account does not have a password yet. An email with a secure link to create your password and verify your email has been sent.',
            ])->onlyInput('username');
        }

        $credentials = $request->validate([
            'password' => ['required'],
        ]);

        $credentials['username'] = $request->username;

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
