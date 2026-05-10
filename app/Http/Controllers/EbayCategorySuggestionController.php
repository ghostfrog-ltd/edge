<?php

namespace App\Http\Controllers;

use App\Services\Ebay\EbayCategorySuggestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class EbayCategorySuggestionController extends Controller
{
    public function __invoke(Request $request, EbayCategorySuggestionService $service): JsonResponse
    {
        $validated = $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
            'marketplace' => ['required', 'in:ebay-uk,ebay-us,ebay-de'],
        ]);

        try {
            return response()->json([
                'suggestions' => $service->suggestions($validated['keyword'], $validated['marketplace']),
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
