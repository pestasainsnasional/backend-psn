<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
        public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = $request->user();
        $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');


        return response()->json([
            'message' => 'Foto profil berhasil diperbarui.',
            'avatar_url' => $user->avatar_url, 
        ]);
    }
}
