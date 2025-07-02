<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Registration;
use App\Models\TeamMember;
use Illuminate\Validation\Rule; 
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function getDraft(Request $request, string $competition_id)
    {
        $draft = $request->user()->registrations()
            ->with(['team.media', 'team.teamMembers.participant.media'])
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
        
        $registration = DB::transaction(function () use ($user, $validated) {
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
            'registration_id' => [
                'required',
                Rule::exists('registrations', 'id')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id)->where('status', 'draft_step_1');                    
                }),
            ],
            
            'leader' => 'required|array',
            'leader.full_name' => 'required|string|max:255',
            'leader.place_of_birth' => 'required|string|max:255',
            'leader.date_of_birth' => 'required|date',
            'leader.address' => 'required|string',
            'leader.nisn' => 'required|string|unique:participants,nisn',
            'leader.phone_number' => 'required|string|unique:participants,phone_number',
            'leader.identity_card' => 'required|file|image|max:2048',

            'members' => 'nullable|array',
            'members.*.full_name' => 'sometimes|required|string|max:255',
            'members.*.place_of_birth' => 'sometimes|required|string|max:255',
            'members.*.date_of_birth' => 'sometimes|required|date',
            'members.*.address' => 'sometimes|required|string',
            'members.*.nisn' => 'sometimes|required|string|unique:participants,nisn,'.($request->input('leader.nisn') ?? 'NULL').',nisn',
            'members.*.phone_number' => 'sometimes|required|string|unique:participants,phone_number',
            'members.*.identity_card' => 'sometimes|required|file|image|max:2048',
        ]);

        $registration = Registration::find($validated['registration_id']);

        DB::transaction(function () use ($request, $registration) {
            $leaderNisn = $request->input('leader.nisn');
            $oldMembers = $registration->team->teamMembers()->with('participant')->get();
            foreach ($oldMembers as $oldMember) {
                if ($oldMember->participant && $oldMember->participant->nisn !== $leaderNisn) {
                    $oldMember->participant->delete();
                } else {
                    $oldMember->delete();
                }
            }

            $leaderData = $request->input('leader');
            $leader = Participant::create($leaderData);
            $leader->addMedia($request->file('leader.identity_card'))->toMediaCollection('identity-cards');
            TeamMember::create(['team_id' => $registration->team_id, 'participant_id' => $leader->id, 'role' => 'leader']);
            
            $registration->update(['participant_id' => $leader->id]);
            if ($request->has('members')) {
                foreach ($request->input('members') as $index => $memberData) {
                    $participantData = $memberData;
                    $memberFile = $request->file("members.{$index}.identity_card");
                    $member = Participant::create($participantData);
                    if ($memberFile) {
                        $member->addMedia($memberFile)->toMediaCollection('identity-cards');
                    }
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
            'registration_id' => [
                'required',
                Rule::exists('registrations', 'id')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id)
                                 ->where('status', 'draft_step_2'); 
                }),
            ],
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
            'registration_id' => [
                'required',
                Rule::exists('registrations', 'id')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id)
                                 ->where('status', 'draft_step_3'); 
                }),
            ],
            'surat_pernyataan' => 'required|file|mimes:pdf|max:2048',
            'bukti_pembayaran' => 'required|file|image|max:2048',
        ]);
        
        $registration = Registration::find($validated['registration_id']);
        $team = $registration->team;
        
        $team->addMediaFromRequest('surat_pernyataan')->toMediaCollection('statement-letters');
        $team->addMediaFromRequest('bukti_pembayaran')->toMediaCollection('payment-proofs');

        $registration->update(['status' => 'draft_step_4']);
        return response()->json(['message' => 'Dokumen berhasil diunggah.']);
    }

    public function finalize(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => 'required|exists:registrations,id,user_id,'.$request->user()->id,
        ]);
        
        try {
            
            DB::transaction(function () use ($validated) {
                $registration = Registration::with('competition.competitionType')->find($validated['registration_id']);
                if (!$registration) {
                    throw ValidationException::withMessages(['registration_id' => 'Pendaftaran tidak ditemukan.']);
                }
                $competitionType = $registration->competition->competitionType;
                $competitionType = $competitionType->lockForUpdate()->first();
                
                if ($competitionType->current_batch !== 'regular') {
                  
                    if ($competitionType->slot_remaining <= 0) {
                        throw ValidationException::withMessages(['kuota' => 'Mohon maaf, kuota untuk batch ini telah habis.']);
                    }
                    $competitionType->decrement('slot_remaining');
                }
                $registration->update(['status' => 'pending']);        
            });

        } catch (ValidationException $e) {
           
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat finalisasi.', 'error' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Pendaftaran Anda berhasil dikirim dan akan segera diverifikasi!']);
    }
    
}
