<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * Display all teachers.
     */
    public function index(): JsonResponse
    {
        $teachers = Teacher::latest()->paginate(20);

        return response()->json($teachers);
    }

    /**
     * Store a new teacher.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teacher_id' => ['required', 'string', 'max:50', 'unique:teachers,teacher_id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:teachers,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'qualification' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive', 'on_leave', 'resigned'])
            ],
        ]);

        $teacher = Teacher::create($validated);

        return response()->json([
            'message' => 'Teacher created successfully.',
            'data' => $teacher,
        ], 201);
    }

    /**
     * Display one teacher.
     */
    public function show(Teacher $teacher): JsonResponse
    {
        return response()->json([
            'data' => $teacher,
        ]);
    }

    /**
     * Update a teacher.
     */
    public function update(Request $request, Teacher $teacher): JsonResponse
    {
        $validated = $request->validate([
            'teacher_id' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'teacher_id')->ignore($teacher->id),
            ],
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('teachers', 'email')->ignore($teacher->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'qualification' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive', 'on_leave', 'resigned'])
            ],
        ]);

        $teacher->update($validated);

        return response()->json([
            'message' => 'Teacher updated successfully.',
            'data' => $teacher->fresh(),
        ]);
    }

    /**
     * Delete a teacher using soft delete.
     */
    public function destroy(Teacher $teacher): JsonResponse
    {
        $teacher->delete();

        return response()->json([
            'message' => 'Teacher deleted successfully.',
        ]);
    }
}