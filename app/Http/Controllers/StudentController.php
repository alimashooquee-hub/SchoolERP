<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display all students.
     */
    public function index(): JsonResponse
    {
        $students = Student::latest()->paginate(20);

        return response()->json($students);
    }

    /**
     * Store a new student.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:50', 'unique:students,student_id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'address' => ['nullable', 'string', 'max:1000'],
            'guardian_name' => ['required', 'string', 'max:150'],
            'guardian_phone' => ['required', 'string', 'max:30'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'admission_number' => ['required', 'string', 'max:50', 'unique:students,admission_number'],
            'admission_date' => ['nullable', 'date'],
            'class_name' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive', 'graduated', 'transferred'])
            ],
        ]);

        $student = Student::create($validated);

        return response()->json([
            'message' => 'Student created successfully.',
            'data' => $student,
        ], 201);
    }

    /**
     * Display one student.
     */
    public function show(Student $student): JsonResponse
    {
        return response()->json([
            'data' => $student,
        ]);
    }

    /**
     * Update a student.
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'student_id')->ignore($student->id),
            ],
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'address' => ['nullable', 'string', 'max:1000'],
            'guardian_name' => ['sometimes', 'required', 'string', 'max:150'],
            'guardian_phone' => ['sometimes', 'required', 'string', 'max:30'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'admission_number' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'admission_number')->ignore($student->id),
            ],
            'admission_date' => ['nullable', 'date'],
            'class_name' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive', 'graduated', 'transferred'])
            ],
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully.',
            'data' => $student->fresh(),
        ]);
    }

    /**
     * Delete a student safely using soft delete.
     */
    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.',
        ]);
    }
}