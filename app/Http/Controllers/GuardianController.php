<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuardianController extends Controller
{
    /**
     * Display all guardians.
     */
    public function index(): JsonResponse
    {
        $guardians = Guardian::latest()->paginate(20);

        return response()->json($guardians);
    }

    /**
     * Store a new guardian.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'guardian_id' => [
                'required',
                'string',
                'max:50',
                'unique:guardians,guardian_id',
            ],
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'relationship' => [
                'required',
                'string',
                'max:50',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'phone' => [
                'required',
                'string',
                'max:30',
            ],
            'address' => [
                'nullable',
                'string',
            ],
            'occupation' => [
                'nullable',
                'string',
                'max:150',
            ],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $guardian = Guardian::create($validated);

        return response()->json([
            'message' => 'Guardian created successfully.',
            'data' => $guardian,
        ], 201);
    }

    /**
     * Display one guardian.
     */
    public function show(Guardian $guardian): JsonResponse
    {
        return response()->json([
            'data' => $guardian,
        ]);
    }

    /**
     * Update a guardian.
     */
    public function update(
        Request $request,
        Guardian $guardian
    ): JsonResponse {
        $validated = $request->validate([
            'guardian_id' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('guardians', 'guardian_id')
                    ->ignore($guardian->id),
            ],
            'first_name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'relationship' => [
                'sometimes',
                'required',
                'string',
                'max:50',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:30',
            ],
            'address' => [
                'nullable',
                'string',
            ],
            'occupation' => [
                'nullable',
                'string',
                'max:150',
            ],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $guardian->update($validated);

        return response()->json([
            'message' => 'Guardian updated successfully.',
            'data' => $guardian->fresh(),
        ]);
    }

    /**
     * Delete a guardian using soft delete.
     */
    public function destroy(Guardian $guardian): JsonResponse
    {
        $guardian->delete();

        return response()->json([
            'message' => 'Guardian deleted successfully.',
        ]);
    }
}