<?php

namespace App\Http\Controllers;

use App\Models\TeacherClassSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherClassSubjectController extends Controller
{
    /**
     * Display a listing of teacher-class-subject assignments.
     */
    public function index(): JsonResponse
    {
        $assignments = TeacherClassSubject::with([
            'teacher',
            'classRoom',
            'subject',
        ])
            ->latest()
            ->paginate(20);

        return response()->json($assignments);
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id',
            ],

            'class_room_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],

            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
        ]);

        // Prevent the same teacher from being assigned
        // to the same class and subject more than once.
        $alreadyAssigned = TeacherClassSubject::where(
            'teacher_id',
            $validated['teacher_id']
        )
            ->where(
                'class_room_id',
                $validated['class_room_id']
            )
            ->where(
                'subject_id',
                $validated['subject_id']
            )
            ->exists();

        if ($alreadyAssigned) {
            return response()->json([
                'message' => 'This teacher is already assigned to this class and subject.',
            ], 422);
        }

        $assignment = TeacherClassSubject::create($validated);

        return response()->json([
            'message' => 'Teacher assigned to class and subject successfully.',
            'data' => $assignment->load([
                'teacher',
                'classRoom',
                'subject',
            ]),
        ], 201);
    }

    /**
     * Display the specified assignment.
     */
    public function show(
        TeacherClassSubject $teacherClassSubject
    ): JsonResponse {
        return response()->json([
            'data' => $teacherClassSubject->load([
                'teacher',
                'classRoom',
                'subject',
            ]),
        ]);
    }

    /**
     * Update the specified assignment.
     */
    public function update(
        Request $request,
        TeacherClassSubject $teacherClassSubject
    ): JsonResponse {
        $validated = $request->validate([
            'teacher_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:teachers,id',
            ],

            'class_room_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:class_rooms,id',
            ],

            'subject_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:subjects,id',
            ],
        ]);

        $teacherId = $validated['teacher_id']
            ?? $teacherClassSubject->teacher_id;

        $classRoomId = $validated['class_room_id']
            ?? $teacherClassSubject->class_room_id;

        $subjectId = $validated['subject_id']
            ?? $teacherClassSubject->subject_id;

        $duplicate = TeacherClassSubject::where(
            'teacher_id',
            $teacherId
        )
            ->where(
                'class_room_id',
                $classRoomId
            )
            ->where(
                'subject_id',
                $subjectId
            )
            ->where(
                'id',
                '!=',
                $teacherClassSubject->id
            )
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'This teacher is already assigned to this class and subject.',
            ], 422);
        }

        $teacherClassSubject->update($validated);

        return response()->json([
            'message' => 'Teacher class subject assignment updated successfully.',
            'data' => $teacherClassSubject->fresh()->load([
                'teacher',
                'classRoom',
                'subject',
            ]),
        ]);
    }

    /**
     * Remove the specified assignment.
     */
    public function destroy(
        TeacherClassSubject $teacherClassSubject
    ): JsonResponse {
        $teacherClassSubject->delete();

        return response()->json([
            'message' => 'Teacher class subject assignment deleted successfully.',
        ]);
    }
}