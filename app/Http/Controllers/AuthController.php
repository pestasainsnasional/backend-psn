<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok dengan catatan kami.'],
            ]);
        }

        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {
            Auth::logout();
            return response()->json(['message' => 'Email Anda belum diverifikasi. Silakan periksa kotak masuk email Anda.'], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil!',
            'token' => $token,
            'user' => $user->load('roles'),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('participant');

        event(new Registered($user));

        return response()->json(['message' => 'Registrasi berhasil! Silakan verifikasi email Anda untuk melanjutkan.'], 201);
    }

    public function user(Request $request)
    {
        return response()->json($request->user()->load('roles'));
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout berhasil.']);
    }
    
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'email_verified_at' => now(),
                    'password' => Hash::make(rand(100000, 999999)),
                ]);
                $user->assignRole('participant');
                event(new Registered($user));
            }

            // Hapus token yang ada dan buat token Sanctum baru
            $user->tokens()->delete();
            $token = $user->create->token('auth_token')->plainTextToken;

            // Redirect kembali ke frontend dengan token di URL
            return redirect(env('FRONTEND_URL') . '/auth/callback?token=' . $token . '&user_id=' . $user->id);

        } catch (\Exception $e) {
            // Tangani error, log error, dan redirect kembali ke halaman login frontend
            Log::error('Google OAuth failed: ' . $e->getMessage());
            return redirect(env('FRONTEND_URL') . '/login?error=oauth_failed&message=' . urlencode($e->getMessage()));
        }
    }
}