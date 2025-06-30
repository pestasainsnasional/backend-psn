<?php
// File: app/Http/Controllers/Api/MultiStepRegistrationController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Registration;
use App\Models\TeamMember;

class RegistrationController extends Controller
{

    public function getDraft(Request $request, string $competition_id)
    {
        $draft = $request->user()->registrations()
            ->with(['team', 'team.teamMembers.participant.media']) 
            ->where('competition_id', $competition_id)
            ->where('status', 'like', 'draft_%')
            ->first();
            
        return response()->json($draft);
    }

    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'competition_id' => 'required|exists:competitions,id',
            'name'           => 'required|string|max:255',
            'school_name'    => 'required|string',
            'school_email'   => 'required|email',
            'npsn'           => 'required|string',
        ]);

        $user = $request->user();

        
        $registration = DB::transaction(function () use ($user, $validated, $request) {
            $team = Team::updateOrCreate(
                ['name' => $validated['name'], 'school_name' => $validated['school_name']],
                $validated
            );
            
            $registration = Registration::updateOrCreate(
                ['user_id' => $user->id, 'competition_id' => $validated['competition_id']],
                ['team_id' => $team->id, 'status' => 'draft_step_1']
            );
            return $registration;
        });

        return response()->json(['message' => 'Langkah 1 berhasil disimpan.', 'registration_id' => $registration->id]);
    }


    public function storeStep2(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => 'required|exists:registrations,id,user_id,'.$request->user()->id,
            'leader'          => 'required|array',
            'leader.full_name'=> 'required|string',
            'leader.nisn'     => 'required|string|unique:participants,nisn',
            'leader.identity_card' => 'required|file|image|max:2048',
            'members'         => 'nullable|array',
            'members.*.full_name' => 'sometimes|required|string',
            'members.*.nisn'      => 'sometimes|required|string|unique:participants,nisn',
            'members.*.identity_card' => 'sometimes|required|file|image|max:2048',
        ]);

        $registration = Registration::find($validated['registration_id']);

        DB::transaction(function () use ($request, $registration) {
        
            $registration->team->teamMembers()->delete();
            $registration->team->teamMembers()->whereHas('participant', function($q) use($request) {
                $q->where('nisn', '!=', $request->input('leader.nisn'));
            })->delete();


            $leader = Participant::create($request->input('leader'));
            $leader->addMedia($request->file('leader.identity_card'))->toMediaCollection('identity-cards');
            TeamMember::create(['team_id' => $registration->team_id, 'participant_id' => $leader->id, 'role' => 'leader']);
            $registration->update(['participant_id' => $leader->id]);
            
            if ($request->has('members')) {
                foreach ($request->file('members') as $index => $memberData) {
                    $participantData = $request->input("members.{$index}");
                    $member = Participant::create($participantData);
                    $member->addMedia($memberData['identity_card'])->toMediaCollection('identity-cards');
                    TeamMember::create(['team_id' => $registration->team_id, 'participant_id' => $member->id, 'role' => 'member']);
                }
            }

            $registration->update(['status' => 'draft_step_2']);
        });

        return response()->json(['message' => 'Data personil berhasil disimpan.']);
    }

    public function storeStep3(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => 'required|exists:registrations,id,user_id,'.$request->user()->id,
            'companion_teacher_name' => 'required|string',
            'companion_teacher_contact' => 'required|string',
            'companion_teacher_nip' => 'required|string',
        ]);
        
        $registration = Registration::find($validated['registration_id']);
        $registration->team()->update($request->except('registration_id'));
        $registration->update(['status' => 'draft_step_3']);

        return response()->json(['message' => 'Data pendamping berhasil disimpan.']);
    }

    public function storeStep4(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => 'required|exists:registrations,id,user_id,'.$request->user()->id,
            'bukti_pembayaran' => 'required|file|image|max:2048',
        ]);
        
        $registration = Registration::find($validated['registration_id']);
        $team = $registration->team;
        
        $team->addMediaFromRequest('bukti_pembayaran')->toMediaCollection('payment-proofs');

        $registration->update(['status' => 'draft_step_4']);
        return response()->json(['message' => 'Dokumen berhasil diunggah.']);
    }

    public function finalize(Request $request)
    {
        $validated = $request->validate(['registration_id' => 'required|exists:registrations,id,user_id,'.$request->user()->id]);
        
        $registration = Registration::find($validated['registration_id']);
        $registration->update(['status' => 'pending']);
        

        return response()->json(['message' => 'Pendaftaran Anda berhasil dikirim dan akan segera diverifikasi!']);
    }
}
