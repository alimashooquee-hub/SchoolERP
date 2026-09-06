<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeeController extends Controller
{
    public function index(): JsonResponse
    {
        $fees = Fee::with('student')
            ->latest()
            ->paginate(20);

        return response()->json($fees);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],

            'fee_type' => [
                'required',
                'string',
                'max:100',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'due_date' => [
                'required',
                'date',
            ],

            'paid_date' => [
                'nullable',
                'date',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'pending',
                    'paid',
                    'partial',
                    'overdue',
                ]),
            ],

            'payment_method' => [
                'nullable',
                Rule::in([
                    'cash',
                    'bank',
                    'online',
                ]),
            ],

            'transaction_reference' => [
                'nullable',
                'string',
                'max:100',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $fee = Fee::create($validated);

        return response()->json([
            'message' => 'Fee created successfully.',
            'data' => $fee->load('student'),
        ], 201);
    }

    public function show(Fee $fee): JsonResponse
    {
        return response()->json([
            'data' => $fee->load('student'),
        ]);
    }

    public function update(
        Request $request,
        Fee $fee
    ): JsonResponse {
        $validated = $request->validate([
            'student_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:students,id',
            ],

            'fee_type' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],

            'amount' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],

            'due_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'paid_date' => [
                'nullable',
                'date',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'pending',
                    'paid',
                    'partial',
                    'overdue',
                ]),
            ],

            'payment_method' => [
                'nullable',
                Rule::in([
                    'cash',
                    'bank',
                    'online',
                ]),
            ],

            'transaction_reference' => [
                'nullable',
                'string',
                'max:100',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $fee->update($validated);

        return response()->json([
            'message' => 'Fee updated successfully.',
            'data' => $fee->fresh()->load('student'),
        ]);
    }

    public function destroy(Fee $fee): JsonResponse
    {
        $fee->delete();

        return response()->json([
            'message' => 'Fee deleted successfully.',
        ]);
    }
}