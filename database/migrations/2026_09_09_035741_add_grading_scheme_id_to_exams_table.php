<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('grading_scheme_id')
                ->nullable()
                ->after('class_room_id')
                ->constrained('grading_schemes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['grading_scheme_id']);
            $table->dropColumn('grading_scheme_id');
        });
    }
};