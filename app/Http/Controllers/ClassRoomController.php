<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassRoomController extends Controller
{
    /**
     * Display all classes.
     */
    public function index(): JsonResponse
    {
        $classes = ClassRoom::latest()->paginate(20);

        return response()->json($classes);
    }

    /**
     * Store a new class.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_name' => ['required', 'string', 'max:100'],
            'section' => ['nullable', 'string', 'max:50'],
            'class_code' => ['required', 'string', 'max:50', 'unique:class_rooms,class_code'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'description' => ['nullable', 'string'],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $classRoom = ClassRoom::create($validated);

        return response()->json([
            'message' => 'Class created successfully.',
            'data' => $classRoom,
        ], 201);
    }

    /**
     * Display one class.
     */
    public function show(ClassRoom $classRoom): JsonResponse
    {
        return response()->json([
            'data' => $classRoom,
        ]);
    }

    /**
     * Update a class.
     */
    public function update(Request $request, ClassRoom $classRoom): JsonResponse
    {
        $validated = $request->validate([
            'class_name' => ['sometimes', 'required', 'string', 'max:100'],
            'section' => ['nullable', 'string', 'max:50'],
            'class_code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('class_rooms', 'class_code')->ignore($classRoom->id),
            ],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'description' => ['nullable', 'string'],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $classRoom->update($validated);

        return response()->json([
            'message' => 'Class updated successfully.',
            'data' => $classRoom->fresh(),
        ]);
    }

    /**
     * Delete a class using soft delete.
     */
    public function destroy(ClassRoom $classRoom): JsonResponse
    {
        $classRoom->delete();

        return response()->json([
            'message' => 'Class deleted successfully.',
        ]);
    }
}
