<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($request->wantsJson()) {
            // Untuk testing, ambil token dari database
            $resetToken = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            return $status == Password::RESET_LINK_SENT
                ? response()->json([
                    'success' => true,
                    'message' => __($status),
                    'token' => $resetToken ? $resetToken->token : null
                ])
                : response()->json(['success' => false, 'message' => __($status)], 422);
        }
        
        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                  ->withErrors(['email' => __($status)]);
    }
}