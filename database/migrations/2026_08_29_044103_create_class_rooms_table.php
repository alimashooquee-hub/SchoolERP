<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();

            $table->string('class_name', 100);
            $table->string('section', 50)->nullable();

            $table->string('class_code', 50)->unique();

            $table->unsignedInteger('capacity')->nullable();

            $table->text('description')->nullable();

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};