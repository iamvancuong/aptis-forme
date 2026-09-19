<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lượt thi thử của học viên v2 (thuộc db2).
 * `sections` = [{part, set_id}] — set_id trỏ tới sets trong db1 (KHÔNG đặt FK chéo DB).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('skill');
            $table->json('sections');                 // [{part:1, set_id:3}, ...] — set_id thuộc db1
            $table->integer('duration_minutes');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->json('section_scores')->nullable();
            $table->string('status')->default('in_progress'); // in_progress | completed | expired
            $table->timestamps();

            $table->index(['user_id', 'skill']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_tests');
    }
};
