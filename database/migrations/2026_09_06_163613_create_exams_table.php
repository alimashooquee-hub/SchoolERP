<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();

            // Exam information
            $table->string('exam_name');
            $table->date('exam_date');

            // Class
            $table->foreignId('class_room_id')
                ->constrained('class_rooms')
                ->cascadeOnDelete();

            // Exam status
            $table->enum('status', [
                'upcoming',
                'ongoing',
                'completed',
                'cancelled',
            ])->default('upcoming');

            // Additional information
            $table->text('description')->nullable();

            $table->timestamps();

            // Prevent duplicate exam name for same class and date
            $table->unique([
                'exam_name',
                'class_room_id',
                'exam_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};