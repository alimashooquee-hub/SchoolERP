<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('grading_scheme_id')
                ->constrained('grading_schemes')
                ->cascadeOnDelete();

            $table->string('grade', 5);

            $table->decimal('min_percentage', 5, 2);

            $table->decimal('max_percentage', 5, 2);

            $table->decimal('grade_point', 4, 2)->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique([
                'grading_scheme_id',
                'grade',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_rules');
    }
};