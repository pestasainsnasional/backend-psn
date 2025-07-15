<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::with('media')->get();

        $sponsors = $sponsors->map(function ($sponsor) {
            return [
                'id' => $sponsor->id,
                'name' => $sponsor->name,
                'type' => $sponsor->type,
                'description' => $sponsor->description,
                'logo_url' => $sponsor->getFirstMediaUrl('logo sponsor'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $sponsors
        ]);
    }
}
