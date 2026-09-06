<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    public function index(): JsonResponse
    {
        $exams = Exam::with('classRoom')
            ->latest('exam_date')
            ->paginate(20);

        return response()->json($exams);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_name' => [
                'required',
                'string',
                'max:100',
            ],

            'exam_date' => [
                'required',
                'date',
            ],

            'class_room_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'upcoming',
                    'ongoing',
                    'completed',
                    'cancelled',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $exam = Exam::create($validated);

        return response()->json([
            'message' => 'Exam created successfully.',
            'data' => $exam->load('classRoom'),
        ], 201);
    }

    public function show(Exam $exam): JsonResponse
    {
        return response()->json([
            'data' => $exam->load('classRoom'),
        ]);
    }

    public function update(
        Request $request,
        Exam $exam
    ): JsonResponse {
        $validated = $request->validate([
            'exam_name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],

            'exam_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'class_room_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:class_rooms,id',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'upcoming',
                    'ongoing',
                    'completed',
                    'cancelled',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $exam->update($validated);

        return response()->json([
            'message' => 'Exam updated successfully.',
            'data' => $exam->fresh()->load('classRoom'),
        ]);
    }

    public function destroy(Exam $exam): JsonResponse
    {
        $exam->delete();

        return response()->json([
            'message' => 'Exam deleted successfully.',
        ]);
    }
}