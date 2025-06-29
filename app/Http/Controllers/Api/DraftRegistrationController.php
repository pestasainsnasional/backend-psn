<?php
// app/Http/Controllers/Api/DraftController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DraftRegistrationController extends Controller
{
    public function save(Request $request)
    {
        $validated = $request->validate(['competition_id' => 'required|exists:competitions,id']);

        $request->user()->drafts()->updateOrCreate(
            ['competition_id' => $validated['competition_id']],
            ['data' => $request->except(['_token', 'competition_id'])]
        );
        return response()->json(['message' => 'Draft saved.']);
    }

    public function get(Request $request, string $competition_id)
    {
        $draft = $request->user()->drafts()->where('competition_id', $competition_id)->first();
        return $draft ? response()->json($draft->data) : response()->json(null);
    }
}