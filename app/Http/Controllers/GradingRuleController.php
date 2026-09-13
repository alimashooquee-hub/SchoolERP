<?php

namespace App\Http\Controllers;

use App\Models\GradingRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GradingRuleController extends Controller
{
    public function index(): JsonResponse
    {
        $rules = GradingRule::with('gradingScheme')
            ->latest()
            ->paginate(20);

        return response()->json($rules);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'grading_scheme_id' => [
                'required',
                'integer',
                'exists:grading_schemes,id',
            ],

            'grade' => [
                'required',
                'string',
                'max:5',
            ],

            'min_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'max_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                'gte:min_percentage',
            ],

            'grade_point' => [
                'nullable',
                'numeric',
                'min:0',
                'max:10',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $rule = GradingRule::create($validated);

        return response()->json([
            'message' => 'Grading rule created successfully.',
            'data' => $rule->load('gradingScheme'),
        ], 201);
    }

    public function show(GradingRule $gradingRule): JsonResponse
    {
        return response()->json([
            'data' => $gradingRule->load('gradingScheme'),
        ]);
    }

    public function update(
        Request $request,
        GradingRule $gradingRule
    ): JsonResponse {
        $validated = $request->validate([
            'grading_scheme_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:grading_schemes,id',
            ],

            'grade' => [
                'sometimes',
                'required',
                'string',
                'max:5',
            ],

            'min_percentage' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'max_percentage' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'grade_point' => [
                'nullable',
                'numeric',
                'min:0',
                'max:10',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $minPercentage = $validated['min_percentage']
            ?? $gradingRule->min_percentage;

        $maxPercentage = $validated['max_percentage']
            ?? $gradingRule->max_percentage;

        if ($maxPercentage < $minPercentage) {
            throw ValidationException::withMessages([
                'max_percentage' => [
                    'Maximum percentage cannot be less than minimum percentage.',
                ],
            ]);
        }

        $gradingRule->update($validated);

        return response()->json([
            'message' => 'Grading rule updated successfully.',
            'data' => $gradingRule->fresh()->load('gradingScheme'),
        ]);
    }

    public function destroy(GradingRule $gradingRule): JsonResponse
    {
        $gradingRule->delete();

        return response()->json([
            'message' => 'Grading rule deleted successfully.',
        ]);
    }
}