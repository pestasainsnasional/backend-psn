<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = Auth::user();

        $user->tokens()->delete();
        $token = $user->createToken('api-token-' . $user->name)->plainTextToken;

        if (is_null($user->email_verified_at)) {
            return response()->json([
                'token' => $token,
                'user' => $user,
                'message' => 'Email belum diverifikasi. Silakan cek email kamu untuk verifikasi.',
            ], 202);
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }


    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Berhasil'
        ]);
    }
}
