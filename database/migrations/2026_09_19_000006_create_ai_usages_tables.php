<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Đếm lượt dùng AI chấm bài theo part (writing & speaking), phục vụ giới hạn/reset.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('writing_ai_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('writing_part');
            $table->unsignedInteger('usage_count')->default(0);
            $table->unsignedInteger('reset_version')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'writing_part', 'reset_version']);
        });

        Schema::create('speaking_ai_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('speaking_part');
            $table->unsignedInteger('usage_count')->default(0);
            $table->unsignedInteger('reset_version')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'speaking_part', 'reset_version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('writing_ai_usages');
        Schema::dropIfExists('speaking_ai_usages');
    }
};
