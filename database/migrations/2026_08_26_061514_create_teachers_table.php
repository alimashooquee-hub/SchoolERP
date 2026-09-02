```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            $table->string('teacher_id', 50)->unique();

            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();

            $table->string('email')->nullable()->unique();
            $table->string('phone', 30)->nullable();

            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->string('qualification', 255)->nullable();
            $table->string('specialization', 255)->nullable();

            $table->date('joining_date')->nullable();

            $table->text('address')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'on_leave',
                'resigned'
            ])->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};