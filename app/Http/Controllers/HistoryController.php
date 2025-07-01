<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class HistoryController extends Controller
{
    public function listSeasons(): JsonResponse{
        $seasons = History::orderBy('season_year', 'desc')->select('id', 'season_year')->get();

        return response()->json([
            'success' => true,
            'data' => $seasons,
        ]);
    }
}
