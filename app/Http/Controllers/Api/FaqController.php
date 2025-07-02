<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    /**
     * Get a list of FAQs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $faqs = Faq::query()
                    ->orderBy('created_at', 'asc')
                    ->get(['id', 'question', 'answer']);

        return response()->json([
            'message' => 'FAQs retrieved successfully',
            'data' => $faqs,
        ], 200);
    }

    /**
     * Get a single FAQ by ID.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json([
                'message' => 'FAQ not found',
            ], 404);
        }

        return response()->json([
            'message' => 'FAQ retrieved successfully',
            'data' => $faq->only(['id', 'question', 'answer']),
        ], 200);
    }
}