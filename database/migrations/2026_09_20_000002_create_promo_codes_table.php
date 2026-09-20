<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mã khuyến mãi do admin tạo: học free `free_days` ngày.
 * Có hạn (expires_at), bật/tắt (is_active), giới hạn lượt (max_redemptions).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 60)->unique();
            $table->unsignedSmallInteger('free_days')->default(1);
            $table->unsignedInteger('max_redemptions')->nullable(); // null = không giới hạn
            $table->timestamp('expires_at')->nullable();            // null = không hết hạn
            $table->boolean('is_active')->default(true);
            $table->string('note')->nullable();                     // ghi chú/chiến dịch
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
