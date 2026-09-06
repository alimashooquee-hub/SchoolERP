<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();

            // Student
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Fee information
            $table->string('fee_type');
            $table->decimal('amount', 10, 2);

            // Payment dates
            $table->date('due_date');
            $table->date('paid_date')->nullable();

            // Fee status
            $table->enum('status', [
                'pending',
                'paid',
                'partial',
                'overdue',
            ])->default('pending');

            // Payment information
            $table->enum('payment_method', [
                'cash',
                'bank',
                'online',
            ])->nullable();

            $table->string('transaction_reference')->nullable();

            // Additional information
            $table->text('remarks')->nullable();

            $table->timestamps();

            // Prevent duplicate fee record for same student,
            // fee type and due date
            $table->unique([
                'student_id',
                'fee_type',
                'due_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};