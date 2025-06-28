<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Registration;
use App\Models\TeamMember;
use App\Models\User;

class RegistrationController extends Controller
{

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'competition_id' => 'required|exists:competitions,id',


            'team.name' => 'required|string|max:255',
            'team.school_name' => 'required|string|max:255',
            'team.school_email' => 'required|email',
            'team.npsn' => 'required|string',

            'pendamping.companion_teacher_name' => 'required|string',
            'pendamping.companion_teacher_contact' => 'required|string',
            'pendamping.companion_teacher_nip' => 'required|string',


            'personil.leader.full_name' => 'required|string',
            'personil.leader.nisn' => 'required|string|unique:participants,nisn',
            'personil.leader.identity_card' => 'required|file|image|max:2048',
            'personil.members' => 'nullable|array',
            'personil.members.*.full_name' => 'sometimes|required|string',
            'personil.members.*.nisn' => 'sometimes|required|string|unique:participants,nisn',
            'personil.members.*.identity_card' => 'sometimes|required|file|image|max:2048',


            'payment_proof' => 'required|file|image|max:2048',
        ]);

        try {

            DB::transaction(function () use ($request) {


                $teamData = array_merge(
                    $request->input('team'),
                    $request->input('pendamping')
                );
                $team = Team::create($teamData);


                $team->addMediaFromRequest('payment_proof')->toMediaCollection('payment-proofs');
                $leader = Participant::create($request->input('personil.leader'));
                $leader->addMedia($request->file('personil.leader.identity_card'))
                    ->toMediaCollection('identity-cards');
                TeamMember::create(['team_id' => $team->id, 'participant_id' => $leader->id, 'role' => 'leader']);


                if ($request->has('personil.members')) {
                    foreach ($request->personil['members'] as $index => $memberData) {
                        $member = Participant::create($memberData);
                        $member->addMedia($request->file("personil.members.{$index}.identity_card"))
                            ->toMediaCollection('identity-cards');
                        TeamMember::create(['team_id' => $team->id, 'participant_id' => $member->id, 'role' => 'member']);
                    }
                }

                Registration::create([
                    'participant_id' => $leader->id,
                    'competition_id' => $request->competition_id,
                    'team_id'        => $team->id,
                    'status'         => 'pending',
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Pendaftaran gagal.', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Pendaftaran Anda berhasil dikirim!'], 201);
    }
}
