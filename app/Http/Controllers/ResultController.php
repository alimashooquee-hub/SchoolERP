<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResultController extends Controller
{
    public function index(): JsonResponse
    {
        $results = Result::with([
            'exam',
            'student',
            'subject',
        ])
            ->latest()
            ->paginate(20);

        return response()->json($results);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_id' => [
                'required',
                'integer',
                'exists:exams,id',
            ],

            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],

            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],

            'total_marks' => [
                'required',
                'numeric',
                'min:1',
            ],

            'obtained_marks' => [
                'required',
                'numeric',
                'min:0',
                'lte:total_marks',
            ],

            'grade' => [
                'nullable',
                'string',
                'max:5',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $result = Result::create($validated);

        return response()->json([
            'message' => 'Result created successfully.',
            'data' => $result->load([
                'exam',
                'student',
                'subject',
            ]),
        ], 201);
    }

    public function show(Result $result): JsonResponse
    {
        return response()->json([
            'data' => $result->load([
                'exam',
                'student',
                'subject',
            ]),
        ]);
    }

    public function update(
        Request $request,
        Result $result
    ): JsonResponse {
        $validated = $request->validate([
            'exam_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:exams,id',
            ],

            'student_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:students,id',
            ],

            'subject_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:subjects,id',
            ],

            'total_marks' => [
                'sometimes',
                'required',
                'numeric',
                'min:1',
            ],

            'obtained_marks' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],

            'grade' => [
                'nullable',
                'string',
                'max:5',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        if (
            isset($validated['obtained_marks']) &&
            isset($validated['total_marks']) &&
            $validated['obtained_marks'] > $validated['total_marks']
        ) {
            throw ValidationException::withMessages([
                'obtained_marks' => [
                    'Obtained marks cannot be greater than total marks.',
                ],
            ]);
        }

        $result->update($validated);

        return response()->json([
            'message' => 'Result updated successfully.',
            'data' => $result->fresh()->load([
                'exam',
                'student',
                'subject',
            ]),
        ]);
    }

    public function destroy(Result $result): JsonResponse
    {
        $result->delete();

        return response()->json([
            'message' => 'Result deleted successfully.',
        ]);
    }
}