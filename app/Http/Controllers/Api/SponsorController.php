<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::all();

        $data = $sponsors->map(function ($sponsor) {
            return [
                'id' => $sponsor->id,
                'nama_brand' => $sponsor->name,
                'deskripsi_brand' => $sponsor->description,
                'logo_url' => $sponsor->getFirstMediaUrl('logo sponsor'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
