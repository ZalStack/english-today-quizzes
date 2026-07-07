<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->foreignId('category_id')->constrained('quiz_categories')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->integer('duration')->comment('Duration in minutes');
            $table->integer('total_questions')->default(0);
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->string('enroll_key')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('show_score')->default(true);
            $table->boolean('show_correct_answer')->default(true);
            $table->boolean('show_wrong_answer')->default(true);
            $table->boolean('show_explanation')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
