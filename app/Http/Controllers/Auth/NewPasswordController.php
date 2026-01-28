<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class NewPasswordController extends Controller
{
    /**
     * Display reset password form (web only)
     */
    public function create(Request $request)
    {
        // Kalau web, return view
        return view('auth.reset-password', [
            'request' => $request
        ]);
    }

    /**
     * Handle reset password (web or API)
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($request->wantsJson()) {
            return $status == Password::PASSWORD_RESET
                ? response()->json(['success' => true, 'message' => __($status)])
                : response()->json(['success' => false, 'message' => __($status)], 422);
        }

        // Web redirect
        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                  ->withErrors(['email' => __($status)]);
    }
}