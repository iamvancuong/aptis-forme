<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lượt làm bài (practice / mock) của học viên v2.
 * `set_id` trỏ tới sets trong db1 → KHÔNG FK chéo DB.
 * `mock_test_id` trỏ tới mock_tests trong db2 (cùng DB) → để index, không FK cứng
 * cho nhẹ ràng buộc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('skill');
            $table->enum('mode', ['practice', 'mock']);
            $table->unsignedBigInteger('set_id')->nullable();        // → db1.sets (no FK)
            $table->unsignedBigInteger('mock_test_id')->nullable();  // → db2.mock_tests
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_grading_requested')->default(false);
            $table->boolean('is_seen')->default(true);
            $table->timestamp('grading_requested_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'skill']);
            $table->index('set_id');
            $table->index('mock_test_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
