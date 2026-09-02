<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
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

    // Class CRUD
    Route::apiResource('classes', ClassRoomController::class);

    // Subject CRUD
    Route::apiResource('subjects', SubjectController::class);

    // Guardian CRUD
    Route::apiResource('guardians', GuardianController::class);

    // Enrollment CRUD
    Route::apiResource('enrollments', EnrollmentController::class);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
