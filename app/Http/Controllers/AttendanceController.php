<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index(): JsonResponse
    {
        $attendances = Attendance::with([
            'student',
            'classRoom',
        ])->latest('attendance_date')->paginate(20);

        return response()->json($attendances);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
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
            'attendance_date' => [
                'required',
                'date',
            ],
            'status' => [
                'nullable',
                Rule::in([
                    'present',
                    'absent',
                    'late',
                    'leave',
                ]),
            ],
            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $attendance = Attendance::create($validated);

        return response()->json([
            'message' => 'Attendance recorded successfully.',
            'data' => $attendance->load([
                'student',
                'classRoom',
            ]),
        ], 201);
    }

    public function show(Attendance $attendance): JsonResponse
    {
        return response()->json([
            'data' => $attendance->load([
                'student',
                'classRoom',
            ]),
        ]);
    }

    public function update(
        Request $request,
        Attendance $attendance
    ): JsonResponse {
        $validated = $request->validate([
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
            'attendance_date' => [
                'sometimes',
                'required',
                'date',
            ],
            'status' => [
                'nullable',
                Rule::in([
                    'present',
                    'absent',
                    'late',
                    'leave',
                ]),
            ],
            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $attendance->update($validated);

        return response()->json([
            'message' => 'Attendance updated successfully.',
            'data' => $attendance->fresh()->load([
                'student',
                'classRoom',
            ]),
        ]);
    }

    public function destroy(Attendance $attendance): JsonResponse
    {
        $attendance->delete();

        return response()->json([
            'message' => 'Attendance deleted successfully.',
        ]);
    }
}