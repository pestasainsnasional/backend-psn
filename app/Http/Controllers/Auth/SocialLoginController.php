<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                $token = $user->createToken('auth-token-google')->plainTextToken;
                return redirect(env('FRONTEND_URL') . '/auth/callback?token=' . $token);
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    
                ]);

                $user->assignRole('user');
            }

            $token = $user->createToken('auth-token-google')->plainTextToken;
            return redirect(env('FRONTEND_URL') . '/auth/callback?token=' . $token);
        } catch (\Exception $e) {
            dd($e);
            return redirect(env('FRONTEND_URL') . '/login?error=google_auth_failed');
        }
    }
}
