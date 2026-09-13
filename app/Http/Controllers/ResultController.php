<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResultController extends Controller
{
    public function index(): JsonResponse
    {
        $results = Result::with([
            'exam',
            'student',
            'subject',
            'gradingScheme',
        ])
            ->latest()
            ->paginate(20);

        return response()->json($results);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_id' => [
                'required',
                'integer',
                'exists:exams,id',
            ],

            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],

            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],

            'total_marks' => [
                'required',
                'numeric',
                'min:1',
            ],

            'obtained_marks' => [
                'required',
                'numeric',
                'min:0',
                'lte:total_marks',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        // Get grading scheme automatically from the exam
        $exam = Exam::with([
            'gradingScheme.gradingRules',
        ])->findOrFail($validated['exam_id']);

        if (!$exam->gradingScheme) {
            throw ValidationException::withMessages([
                'exam_id' => [
                    'This exam does not have a grading scheme assigned.',
                ],
            ]);
        }

        $gradingScheme = $exam->gradingScheme;

        $baseMarks = (float) $gradingScheme->base_marks;
        $obtainedMarks = (float) $validated['obtained_marks'];

        if ($baseMarks <= 0) {
            throw ValidationException::withMessages([
                'exam_id' => [
                    'The grading scheme base marks must be greater than zero.',
                ],
            ]);
        }

        if ($obtainedMarks > $baseMarks) {
            throw ValidationException::withMessages([
                'obtained_marks' => [
                    'Obtained marks cannot be greater than the grading scheme base marks.',
                ],
            ]);
        }

        $percentage = ($obtainedMarks / $baseMarks) * 100;

        $gradingRule = $gradingScheme->gradingRules()
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        if (!$gradingRule) {
            throw ValidationException::withMessages([
                'exam_id' => [
                    'No grading rule matches the calculated percentage.',
                ],
            ]);
        }

        // Automatically save the exam's grading scheme
        $validated['grading_scheme_id'] = $gradingScheme->id;

        // Automatically calculate grade
        $validated['grade'] = $gradingRule->grade;

        // Use grading rule remarks if teacher did not provide remarks
        if (empty($validated['remarks'])) {
            $validated['remarks'] = $gradingRule->remarks;
        }

        $result = Result::create($validated);

        return response()->json([
            'message' => 'Result created successfully.',
            'data' => $result->load([
                'exam',
                'student',
                'subject',
                'gradingScheme',
            ]),
            'calculation' => [
                'base_marks' => $baseMarks,
                'obtained_marks' => $obtainedMarks,
                'percentage' => round($percentage, 2),
                'grade' => $gradingRule->grade,
                'grade_point' => $gradingRule->grade_point,
            ],
        ], 201);
    }

    public function show(Result $result): JsonResponse
    {
        return response()->json([
            'data' => $result->load([
                'exam',
                'student',
                'subject',
                'gradingScheme',
            ]),
        ]);
    }

    public function update(
        Request $request,
        Result $result
    ): JsonResponse {
        $validated = $request->validate([
            'exam_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:exams,id',
            ],

            'student_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:students,id',
            ],

            'subject_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:subjects,id',
            ],

            'total_marks' => [
                'sometimes',
                'required',
                'numeric',
                'min:1',
            ],

            'obtained_marks' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $totalMarks = (float) (
            $validated['total_marks']
            ?? $result->total_marks
        );

        $obtainedMarks = (float) (
            $validated['obtained_marks']
            ?? $result->obtained_marks
        );

        if ($obtainedMarks > $totalMarks) {
            throw ValidationException::withMessages([
                'obtained_marks' => [
                    'Obtained marks cannot be greater than total marks.',
                ],
            ]);
        }

        // Determine the effective exam
        $examId = $validated['exam_id'] ?? $result->exam_id;

        $exam = Exam::with([
            'gradingScheme.gradingRules',
        ])->findOrFail($examId);

        if (!$exam->gradingScheme) {
            throw ValidationException::withMessages([
                'exam_id' => [
                    'This exam does not have a grading scheme assigned.',
                ],
            ]);
        }

        $gradingScheme = $exam->gradingScheme;

        $baseMarks = (float) $gradingScheme->base_marks;

        if ($baseMarks <= 0) {
            throw ValidationException::withMessages([
                'exam_id' => [
                    'The grading scheme base marks must be greater than zero.',
                ],
            ]);
        }

        if ($obtainedMarks > $baseMarks) {
            throw ValidationException::withMessages([
                'obtained_marks' => [
                    'Obtained marks cannot be greater than the grading scheme base marks.',
                ],
            ]);
        }

        $percentage = ($obtainedMarks / $baseMarks) * 100;

        $gradingRule = $gradingScheme->gradingRules()
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        if (!$gradingRule) {
            throw ValidationException::withMessages([
                'exam_id' => [
                    'No grading rule matches the calculated percentage.',
                ],
            ]);
        }

        // Automatically use the selected exam's grading scheme
        $validated['exam_id'] = $exam->id;
        $validated['grading_scheme_id'] = $gradingScheme->id;
        $validated['total_marks'] = $totalMarks;
        $validated['obtained_marks'] = $obtainedMarks;

        // Automatically recalculate grade
        $validated['grade'] = $gradingRule->grade;

        if (
            !array_key_exists('remarks', $validated) ||
            empty($validated['remarks'])
        ) {
            $validated['remarks'] = $gradingRule->remarks;
        }

        $result->update($validated);

        return response()->json([
            'message' => 'Result updated successfully.',
            'data' => $result->fresh()->load([
                'exam',
                'student',
                'subject',
                'gradingScheme',
            ]),
            'calculation' => [
                'base_marks' => $baseMarks,
                'obtained_marks' => $obtainedMarks,
                'percentage' => round($percentage, 2),
                'grade' => $gradingRule->grade,
                'grade_point' => $gradingRule->grade_point,
            ],
        ]);
    }

    public function destroy(Result $result): JsonResponse
    {
        $result->delete();

        return response()->json([
            'message' => 'Result deleted successfully.',
        ]);
    }
}