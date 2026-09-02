<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    /**
     * Display all enrollments.
     */
    public function index(): JsonResponse
    {
        $enrollments = Enrollment::with([
            'student',
            'classRoom',
        ])->latest()->paginate(20);

        return response()->json($enrollments);
    }

    /**
     * Store a new enrollment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enrollment_id' => [
                'required',
                'string',
                'max:50',
                'unique:enrollments,enrollment_id',
            ],

            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],

            'class_room_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],

            'enrollment_date' => [
                'required',
                'date',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'active',
                    'inactive',
                    'completed',
                ]),
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $enrollment = Enrollment::create($validated);

        return response()->json([
            'message' => 'Enrollment created successfully.',
            'data' => $enrollment->load([
                'student',
                'classRoom',
            ]),
        ], 201);
    }

    /**
     * Display one enrollment.
     */
    public function show(Enrollment $enrollment): JsonResponse
    {
        return response()->json([
            'data' => $enrollment->load([
                'student',
                'classRoom',
            ]),
        ]);
    }

    /**
     * Update an enrollment.
     */
    public function update(
        Request $request,
        Enrollment $enrollment
    ): JsonResponse {
        $validated = $request->validate([
            'enrollment_id' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('enrollments', 'enrollment_id')
                    ->ignore($enrollment->id),
            ],

            'student_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:students,id',
            ],

            'class_room_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:class_rooms,id',
            ],

            'enrollment_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'active',
                    'inactive',
                    'completed',
                ]),
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $enrollment->update($validated);

        return response()->json([
            'message' => 'Enrollment updated successfully.',
            'data' => $enrollment->fresh()->load([
                'student',
                'classRoom',
            ]),
        ]);
    }

    /**
     * Delete an enrollment using soft delete.
     */
    public function destroy(Enrollment $enrollment): JsonResponse
    {
        $enrollment->delete();

        return response()->json([
            'message' => 'Enrollment deleted successfully.',
        ]);
    }
}