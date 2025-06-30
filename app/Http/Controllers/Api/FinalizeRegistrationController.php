<?php
// app/Http/Controllers/Api/FinalizeRegistrationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Registration;
use App\Models\TeamMember;

class FinalizeRegistrationController extends Controller
{
    public function store(Request $request)
    {
 
        try {
            DB::transaction(function () use ($request) {
                // Logika lengkap untuk membuat Team, Participant, Registration, dll.
                // ...
            });

            // Hapus draf setelah berhasil finalisasi
            $request->user()->drafts()->where('competition_id', $request->competition_id)->delete();

        } catch (\Exception $e) {
            return response()->json(['message' => 'Pendaftaran gagal.', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Pendaftaran Anda berhasil dikirim!'], 201);
    }
}
