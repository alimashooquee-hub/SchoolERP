<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Display all subjects.
     */
    public function index(): JsonResponse
    {
        $subjects = Subject::latest()->paginate(20);

        return response()->json($subjects);
    }

    /**
     * Store a new subject.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_code' => [
                'required',
                'string',
                'max:50',
                'unique:subjects,subject_code',
            ],
            'subject_name' => [
                'required',
                'string',
                'max:150',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'credit_hours' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $subject = Subject::create($validated);

        return response()->json([
            'message' => 'Subject created successfully.',
            'data' => $subject,
        ], 201);
    }

    /**
     * Display one subject.
     */
    public function show(Subject $subject): JsonResponse
    {
        return response()->json([
            'data' => $subject,
        ]);
    }

    /**
     * Update a subject.
     */
    public function update(
        Request $request,
        Subject $subject
    ): JsonResponse {
        $validated = $request->validate([
            'subject_code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('subjects', 'subject_code')
                    ->ignore($subject->id),
            ],
            'subject_name' => [
                'sometimes',
                'required',
                'string',
                'max:150',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'credit_hours' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $subject->update($validated);

        return response()->json([
            'message' => 'Subject updated successfully.',
            'data' => $subject->fresh(),
        ]);
    }

    /**
     * Delete a subject using soft delete.
     */
    public function destroy(Subject $subject): JsonResponse
    {
        $subject->delete();

        return response()->json([
            'message' => 'Subject deleted successfully.',
        ]);
    }
}