<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users v2 — theo schema v1 nhưng BỎ `google_email` (thuộc lớp học/Meet đã cắt).
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Vai trò & trạng thái truy cập
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->string('source', 20)->default('manual'); // manual | register | import...
            $table->enum('status', ['active', 'blocked'])->default('active');
            $table->timestamp('expires_at')->nullable();      // hạn sử dụng khóa
            $table->string('target_level')->nullable()->default('B2');

            // Giới hạn thiết bị & chống gian lận
            $table->integer('max_devices')->default(2);
            $table->integer('violation_count')->default(0);
            $table->timestamp('last_violation_at')->nullable();
            $table->boolean('devtools_guard_disabled')->default(false);
            $table->boolean('must_change_password')->default(false);

            // Quản lý lượt dùng AI (writing + speaking)
            $table->integer('ai_reset_version')->default(1);
            $table->unsignedInteger('speaking_ai_reset_version')->default(0);
            $table->integer('ai_extra_uses')->default(0);

            $table->rememberToken();
            $table->timestamps();

            $table->index('status');
            $table->index('expires_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
