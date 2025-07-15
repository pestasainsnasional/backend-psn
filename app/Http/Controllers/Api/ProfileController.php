<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\User;

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

    public function myRegistrations(Request $request)
    {
        $user = $request->user();

        $registrations = $user->registrations()
            ->with([
                'competition.competitionType',
                'team.media',
                'team.teamMembers.participant.media'
            ])
            ->latest()
            ->get();

        return response()->json($registrations);
    }


    public function showRegistrationDetail(Request $request, Registration $registration)
    {
        if ($registration->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Data pendaftaran tidak ditemukan.'], 404);
        }

        $registration->load(['competition', 'team.media', 'team.teamMembers.participant.media']);
        return response()->json($registration);
    }

    public function checkRegistrationStatus(Request $request)
    {
        $user = $request->user();
        $isRegistered = Registration::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'verified'])
            ->exists();

        return response()->json([
            'is_registered' => $isRegistered,
        ]);
    }
}
