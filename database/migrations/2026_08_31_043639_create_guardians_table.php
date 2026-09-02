<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();

            $table->string('guardian_id', 50)->unique();

            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();

            $table->string('relationship', 50);

            $table->string('email')->nullable();
            $table->string('phone', 30);

            $table->text('address')->nullable();

            $table->string('occupation', 150)->nullable();

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
        Schema::dropIfExists('guardians');
    }
};