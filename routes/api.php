<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\GradingRuleController;
use App\Http\Controllers\GradingSchemeController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherClassSubjectController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Profile
    Route::get('/profile', function (Request $request) {
        return response()->json([
            'message' => 'Protected route accessed successfully.',
            'user' => $request->user(),
        ]);
    });

    // Student CRUD
    Route::apiResource('students', StudentController::class);

    // Teacher CRUD
    Route::apiResource('teachers', TeacherController::class);

    // Teacher Class Subject Assignment
    Route::apiResource(
        'teacher-class-subjects',
        TeacherClassSubjectController::class
    );

    // Class CRUD
    Route::apiResource('classes', ClassRoomController::class);

    // Subject CRUD
    Route::apiResource('subjects', SubjectController::class);

    // Guardian CRUD
    Route::apiResource('guardians', GuardianController::class);

    // Enrollment CRUD
    Route::apiResource('enrollments', EnrollmentController::class);

    // Attendance CRUD
    Route::apiResource('attendances', AttendanceController::class);

    // Fee CRUD
    Route::apiResource('fees', FeeController::class);

    // Exam CRUD
    Route::apiResource('exams', ExamController::class);

    // Result CRUD
    Route::apiResource('results', ResultController::class);

    // Grading Scheme CRUD
    Route::apiResource('grading-schemes', GradingSchemeController::class);

    // Grading Rule CRUD
    Route::apiResource('grading-rules', GradingRuleController::class);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});