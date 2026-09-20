<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lượt đổi mã khuyến mãi (free 1 ngày). Mỗi email chỉ 1 lần (unique).
 * ip_address + fingerprint để giới hạn mềm chống 1 người tạo nhiều tài khoản.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redemptions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('code', 60);
            $table->string('ip_address', 45)->nullable();
            $table->string('fingerprint', 64)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['ip_address', 'created_at']);
            $table->index(['fingerprint', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redemptions');
    }
};
