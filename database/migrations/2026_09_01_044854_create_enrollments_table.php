<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            $table->string('enrollment_id', 50)->unique();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('class_room_id')
                ->constrained('class_rooms')
                ->cascadeOnDelete();

            $table->date('enrollment_date');

            $table->enum('status', [
                'active',
                'inactive',
                'completed'
            ])->default('active');

            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['student_id', 'class_room_id'],
                'student_class_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};