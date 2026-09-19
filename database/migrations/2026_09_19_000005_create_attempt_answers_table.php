<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Câu trả lời của từng lượt làm bài.
 * `question_id` trỏ tới questions trong db1 → KHÔNG FK chéo DB (chỉ index).
 * `attempt_id` cùng db2 → FK cascade bình thường.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('question_id');   // → db1.questions (no FK)
            $table->json('answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->json('ai_metadata')->nullable();
            $table->string('grading_status')->nullable();
            $table->timestamps();

            $table->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_answers');
    }
};
