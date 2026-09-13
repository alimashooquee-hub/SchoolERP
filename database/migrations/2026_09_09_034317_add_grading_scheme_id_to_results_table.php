<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('grading_scheme_id')
                ->after('subject_id')
                ->constrained('grading_schemes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['grading_scheme_id']);
            $table->dropColumn('grading_scheme_id');
        });
    }
};