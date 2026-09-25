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

        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = \App\Models\User::where($loginType, $request->username)->first();

        if ($user && empty($user->password)) {
            \Illuminate\Support\Facades\Password::sendResetLink(['email' => $user->email]);
            return back()->withErrors([
                'username' => 'Your account does not have a password yet. An email with a secure link to create your password and verify your email has been sent.',
            ])->onlyInput('username');
        }

        // If password is not provided but the user has a password, the validate below will catch it
        $credentials = $request->validate([
            'password' => ['required'],
        ]);

        $credentials[$loginType] = $request->username;

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
