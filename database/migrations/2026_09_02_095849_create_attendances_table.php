<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Student and class
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('class_room_id')
                ->constrained('class_rooms')
                ->cascadeOnDelete();

            // Attendance information
            $table->date('attendance_date');

            $table->enum('status', [
                'present',
                'absent',
                'late',
                'leave',
            ])->default('present');

            $table->text('remarks')->nullable();

            $table->timestamps();

            // One attendance record per student per class per day
            $table->unique([
                'student_id',
                'class_room_id',
                'attendance_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};