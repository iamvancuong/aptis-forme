<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Mã khuyến mãi khởi tạo (admin có thể sửa/tắt/thêm ở /admin/promo-codes).
        PromoCode::updateOrCreate(
            ['code' => 'NHAIAPTIS'],
            ['free_days' => 1, 'is_active' => true, 'note' => 'Mã ra mắt (mặc định)'],
        );

        // Admin v2 (chỉ quản user + thanh toán).
        User::updateOrCreate(
            ['email' => 'admin@aptis.local'],
            [
                'name' => 'Quản trị nhaiaptis',
                'password' => 'admin1234',
                'role' => 'admin',
                'source' => 'seed',
                'status' => 'active',
            ]
        );

        // Học viên thử nghiệm.
        User::updateOrCreate(
            ['email' => 'hocvien@aptis.local'],
            [
                'name' => 'Học Viên Demo',
                'password' => 'hocvien1234',
                'role' => 'user',
                'source' => 'seed',
                'status' => 'active',
                'expires_at' => now()->addYear(),
            ]
        );
    }
}
