<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\CompetitionType;
use App\Models\Competition;


class CompetitionController extends Controller
{
    public function index(): JsonResponse{
        $competitions = Competition::with('competitionType')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $competitions,
        ]);
    }

    public function show(Competition $competition): JsonResponse{
        $competition->load('competitionType');

        return response()->json([
            'success' => true,
            'data' => $competition,
        ]);
    }

    public function getCompetitionTypes(): JsonResponse
    {
        $competitionTypes = CompetitionType::orderBy('type')->get();

        return response()->json([
            'success' => true,
            'data' => $competitionTypes,
        ]);
    }
}
