<?php

namespace App\Http\Controllers;

use App\Models\CompetitionHistory;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class HistoryController extends Controller
{
    public function listSeasons(): JsonResponse
    {
        $seasons = History::orderBy('season_year', 'desc')->select('id', 'season_year')->get();

        return response()->json([
            'success' => true,
            'data' => $seasons,
        ]);
    }

    public function latestSeason(): JsonResponse
    {
        $latestSeason = History::query()->select('id', 'theme', 'description', 'season_year', 'total_participants', 'total_competitions', "created_at", "updated_at")->with([
            'media' => function ($query) {
                $query->select('id', 'model_type', 'model_id', 'uuid', 'collection_name', 'name', 'file_name', 'disk');
            },
            'competitionHistory' => function ($query) {
                $query->select('id', 'history_id', 'competition_name', 'competition_type', 'winner_name', "winner_school", "rank", "created_at", "updated_at");
            },
            'competitionHistory.media' => function ($query) {
                $query->select('id', 'model_type', 'model_id', 'uuid', 'collection_name', 'name', 'file_name', 'disk');
            }
        ])->orderBy('season_year', 'desc')->first();

        return response()->json([
            'success' => true,
            'data' => $latestSeason,
        ]);
    }

    public function spesificSeason(int $year): JsonResponse
    {
        $spesificSeason = History::query()->select('id', 'theme', 'description', 'season_year', 'total_participants', 'total_competitions', "created_at", "updated_at")->with([
            'media' => function ($query) {
                $query->select('id', 'model_type', 'model_id', 'uuid', 'collection_name', 'name', 'file_name', 'disk');
            },
            'competitionHistory' => function ($query) {
                $query->select('id', 'history_id', 'competition_name', 'competition_type', 'winner_name', "winner_school", "rank", "created_at", "updated_at");
            },
            'competitionHistory.media' => function ($query) {
                $query->select('id', 'model_type', 'model_id', 'uuid', 'collection_name', 'name', 'file_name', 'disk');
            }
        ])->where('season_year', $year)->get();


        return response()->json([
            'success' => true,
            'data' => $spesificSeason,
        ]);
    }
}
