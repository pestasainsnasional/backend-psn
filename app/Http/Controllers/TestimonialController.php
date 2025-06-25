<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index(): JsonResponse{
        $testimonials = Testimonial::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $testimonials,
        ]);
    }
}
