<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index(): JsonResponse{
         $testimonials = Testimonial::query()
            ->select('id', 'name', 'description', 'institution', 'created_at', 'updated_at')
            ->with(['media' => function ($query) {
                $query->select('id', 'model_type', 'model_id', 'uuid', 'collection_name', 'name', 'file_name', 'disk');
            }])
            ->latest()
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $testimonials,
        ]);
    }
}
