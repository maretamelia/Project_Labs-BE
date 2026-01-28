<?php

Route::get('/debug-reset-detailed', function () {
    $email = 'loli@gmail.com';
    $password = 'Laku_123';
    $token = \DB::table('password_reset_tokens')->where('email', $email)->first();
    
    if (!$token) {
        echo "❌ Token tidak ada di database!<br>";
        echo "Jalankan forgot-password dulu!<br>";
        return;
    }
    
    echo "✅ Email: " . $email . "<br>";
    echo "✅ Token: " . substr($token->token, 0, 20) . "...<br>";
    echo "✅ Created: " . $token->created_at . "<br>";
    
    // Test validasi
    try {
        \Illuminate\Support\Facades\Validator::make([
            'token' => $token->token,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ], [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ])->validate();
        
        echo "✅ Validasi PASSED<br>";
    } catch (\Exception $e) {
        echo "❌ Validasi FAILED: " . $e->getMessage() . "<br>";
    }
    
    // Test Password::reset
    $status = \Illuminate\Support\Facades\Password::reset(
        [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'token' => $token->token
        ],
        function ($user, $password) {
            $user->update(['password' => \Illuminate\Support\Facades\Hash::make($password)]);
        }
    );
    
    echo "Status: " . $status . "<br>";
    if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
        echo "✅ PASSWORD RESET BERHASIL!";
    } else {
        echo "❌ PASSWORD RESET GAGAL!";
    }
});