<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Đơn hàng v2 — mua/gia hạn khóa qua PayOS (tài khoản PayOS MỚI của v2).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_code')->unique(); // mã đơn gửi PayOS
            $table->string('email');
            $table->string('type')->default('registration');    // registration | renewal...
            $table->string('package')->nullable();               // gói giá đã chọn
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('amount');                // số tiền (VND)
            $table->string('status')->default('pending');        // pending | paid | cancelled
            $table->unsignedBigInteger('user_id')->nullable();   // gắn khi cấp tài khoản
            $table->string('sale_code', 16)->nullable();         // mã sale/giới thiệu
            $table->string('payos_link_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('status');
            $table->index('sale_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
