<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Registration;
use App\Models\TeamMember;
use App\Models\CompetitionType;
use App\Models\Competition;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Notifications\RegistrationSubmitted;

class RegistrationController extends Controller
{
    public function getDraft(Request $request, string $competition_id)
    {
        $draft = $request->user()->registrations()
            ->with(['team.media', 'team.teamMembers.participant.media'])
            ->where('competition_id', $competition_id)
            ->where('status', 'like', 'draft_%')
            ->first();

          if (!$draft) {
            return response()->json([
                'message' => 'Draft tidak ditemukan',
                'data' => null
            ]);
        }
            
        return response()->json($draft);
    }

    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'competition_id' => 'required|exists:competitions,id',
            'name'           => 'required|string|max:255',
            'school_name'    => 'required|string',
            'school_email'   => 'required|email',
            'npsn'           => 'required|numeric|digits:8',
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
                    return $query->where('user_id', $request->user()->id)
                                 ->whereIn('status', ['draft_step_1', 'draft_step_2', 'draft_step_3', 'draft_step_4']);
                }),
            ],
    
            'leader' => 'required|array',
            'leader.full_name' => 'required|string|max:255',
            'leader.email' => 'required|email',
            'leader.place_of_birth' => 'required|string|max:255',
            'leader.date_of_birth' => 'required|date',
            'leader.address' => 'required|string',
            'leader.nisn' => 'required|string',
            'leader.phone_number' => 'required|string',
            'leader.student_proof' => 'sometimes|required|file|image|max:2048', 
            'leader.twibbon_proof' => 'sometimes|required|file|image|max:2048',
           

            'members' => 'nullable|array',
            'members.*.full_name' => 'sometimes|required|string',
            'members.*.email' => 'sometimes|required|email|different:leader.email',
            'members.*.nisn' => 'sometimes|required|string|different:leader.nisn',
            'members.*.phone_number' => 'sometimes|required|string|different:leader.phone_number',
            'members.*.place_of_birth' => 'sometimes|required|string',
            'members.*.date_of_birth' => 'sometimes|required|date',
            'members.*.address' => 'sometimes|required|string',
            'members.*.student_proof' => 'sometimes|required|file|image|max:2048',
            'members.*.twibbon_proof' => 'sometimes|required|file|image|max:2048',
        ]);
        
        $allNisns = collect([$request->input('leader.nisn')])->merge($request->input('members.*.nisn'));
        if ($allNisns->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages(['nisn' => 'NISN tidak boleh sama antar peserta dalam satu tim.']);
        }
        $allPhones = collect([$request->input('leader.phone_number')])->merge($request->input('members.*.phone_number'));
        if ($allPhones->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages(['phone_number' => 'Nomor telepon tidak boleh sama antar peserta dalam satu tim.']);
        }
        $allEmails = collect([$request->input('leader.email')])->merge($request->input('members.*.email'));
        if ($allEmails->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages(['email' => 'Alamat email tidak boleh sama antar peserta dalam satu tim.']);
        }

        $registration = Registration::find($validated['registration_id']);

        DB::transaction(function () use ($request, $registration) {
            $registration->team->teamMembers()->each(function ($teamMember) {
                if ($teamMember->participant) {
                    $teamMember->participant->delete(); 
                }
            });
            $leader = Participant::create($request->input('leader'));

            if ($request->hasFile('leader.student_proof')) {
                $leader->addMedia($request->file('leader.student_proof'))->toMediaCollection('student-proofs');
            }
            if ($request->hasFile('leader.twibbon_proof')) {
                $leader->addMedia($request->file('leader.twibbon_proof'))->toMediaCollection('twibbon-proofs');
            }
            
            TeamMember::create(['team_id' => $registration->team_id, 'participant_id' => $leader->id, 'role' => 'leader']);
            
            if ($request->has('members')) {
                foreach ($request->input('members') as $index => $memberData) {
                    $member = Participant::create($memberData);
                    if ($request->hasFile("members.{$index}.student_proof")) {
                        $member->addMedia($request->file("members.{$index}.student_proof"))->toMediaCollection('student-proofs');
                    }
                    if ($request->hasFile("members.{$index}.twibbon_proof")) {
                        $member->addMedia($request->file("members.{$index}.twibbon_proof"))->toMediaCollection('twibbon-proofs');
                    }
                   
                    TeamMember::create(['team_id' => $registration->team_id, 'participant_id' => $member->id, 'role' => 'member']);
                }
            }
            $registration->update([
                'participant_id' => $leader->id,
                'status' => 'draft_step_2'
            ]);
        });
        return response()->json(['message' => 'Langkah 2 berhasil disimpan.', 'registration_id' => $registration->id]);
    }
    
    public function storeStep3(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => [
                'required',
                Rule::exists('registrations', 'id')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id)
                                 ->whereIn('status', ['draft_step_2', 'draft_step_3', 'draft_step_4']); 
                }),
            ],
            'companion_teacher_name' => 'required|string',
            'companion_teacher_contact' => 'required|string',
            'companion_teacher_email' => 'required|email',
            'companion_teacher_nip' => 'required|string',
        ]);
        
        $registration = Registration::with('competition.competitionType')->find($validated['registration_id']);

        try {
            DB::transaction(function () use ($request, $registration) {
                
                if ($registration->status === 'draft_step_2') {
                    $competitionType = CompetitionType::where('id', $registration->competition->competition_type_id)
                                            ->lockForUpdate() 
                                            ->first();

                    if ($competitionType->current_batch !== 'regular') {
                        if ($competitionType->slot_remaining <= 0) {
                            throw ValidationException::withMessages(['kuota' => 'Mohon maaf, kuota untuk sesi ini telah habis.']);
                        }
                        $competitionType->decrement('slot_remaining');
                    }
                    $registration->update(['status' => 'draft_step_3']);
                }
                $registration->team()->update($request->except('registration_id'));
            });
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        }
        return response()->json(['message' => 'Langkah 3 berhasil disimpan dan slot Anda telah diamankan sementara.', 'registration_id' => $registration->id]);
    }

    public function getPaymentCode(Request $request, string $registration_id)
    {
        $registration = $request->user()->registrations()
                                ->with('team') 
                                ->where('id', $registration_id)
                                ->whereIn('status', ['draft_step_3', 'draft_step_4'])  
                                ->firstOrFail();

        if ($registration->payment_unique_code && $registration->payment_code_expires_at > now()) {
            return response()->json([
                'unique_code' => $registration->payment_unique_code,
                'expires_at' => $registration->payment_code_expires_at->toIso8601String(),
            ]);
        }

        $randomNumbers = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $schoolName = $registration->team->school_name;
        $sanitizedSchoolName = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $schoolName));
        $newCode = 'PSN' . $randomNumbers . '_' . $sanitizedSchoolName;

        $expiresAt = now()->addMinutes(30); 

        $registration->update([
            'payment_unique_code' => $newCode,
            'payment_code_expires_at' => $expiresAt,
        ]);

        return response()->json([
            'unique_code' => $newCode,
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    }


    public function storeStep4(Request $request)
    {
        $registration = Registration::with('team')->find($request->input('registration_id'));
        if (!$registration || $registration->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Pendaftaran tidak ditemukan.'], 404);
        }
        if (!in_array($registration->status, ['draft_step_3', 'draft_step_4'])) {
            return response()->json(['message' => 'Aksi tidak diizinkan untuk status pendaftaran saat ini.'], 403);
        }

        $team = $registration->team;
        $validated = $request->validate([
            'registration_id' => 'required',
            'bukti_pembayaran' => [Rule::requiredIf(!$team->hasMedia('payment-proofs')),'file', 'image','max:2048' ],
        ]);
        
        if ($registration->payment_code_expires_at < now()) {
            throw ValidationException::withMessages([
                'payment_code' => 'Kode pembayaran Anda telah kedaluwarsa. Silakan muat ulang halaman untuk mendapatkan kode baru.',
            ]);
        }

        if ($request->hasFile('bukti_pembayaran')) {
            $team->clearMediaCollection('payment-proofs');
            $team->addMediaFromRequest('bukti_pembayaran')->toMediaCollection('payment-proofs');
        }
        
        $registration->update(['status' => 'draft_step_4']);
        return response()->json(['message' => 'Langkah 4 berhasil disimpan.', 'registration_id' => $registration->id]);
    }

    
    public function finalize(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => [
                'required',
                Rule::exists('registrations', 'id')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id)->where('status', 'draft_step_4');
                }),
            ],
        ]);
        
        DB::transaction(function () use ($validated) {
            $registration = Registration::with(['user', 'team'])->find($validated['registration_id']);
            if (!$registration) {
                throw ValidationException::withMessages(['registration_id' => 'Pendaftaran tidak ditemukan.']);
            }

            $registration->update(['status' => 'pending']);
            $registration->user->notify(new RegistrationSubmitted($registration));
        });
        
        return response()->json(['message' => 'Pendaftaran Anda berhasil dikirim dan akan segera diverifikasi!']);
    }
}