<?php

namespace App\Http\Controllers;

use App\Models\GradingScheme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradingSchemeController extends Controller
{
    public function index(): JsonResponse
    {
        $schemes = GradingScheme::with('gradingRules')
            ->latest()
            ->paginate(20);

        return response()->json($schemes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'base_marks' => [
                'required',
                'numeric',
                'min:1',
                'max:10000',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $scheme = GradingScheme::create($validated);

        return response()->json([
            'message' => 'Grading scheme created successfully.',
            'data' => $scheme->load('gradingRules'),
        ], 201);
    }

    public function show(GradingScheme $gradingScheme): JsonResponse
    {
        return response()->json([
            'data' => $gradingScheme->load('gradingRules'),
        ]);
    }

    public function update(
        Request $request,
        GradingScheme $gradingScheme
    ): JsonResponse {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'base_marks' => [
                'sometimes',
                'required',
                'numeric',
                'min:1',
                'max:10000',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $gradingScheme->update($validated);

        return response()->json([
            'message' => 'Grading scheme updated successfully.',
            'data' => $gradingScheme->fresh()->load('gradingRules'),
        ]);
    }

    public function destroy(GradingScheme $gradingScheme): JsonResponse
    {
        $gradingScheme->delete();

        return response()->json([
            'message' => 'Grading scheme deleted successfully.',
        ]);
    }
}