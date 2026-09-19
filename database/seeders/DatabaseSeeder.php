<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin v2 (chỉ quản user + thanh toán).
        User::updateOrCreate(
            ['email' => 'admin@aptis.local'],
            [
                'name' => 'Admin V2',
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
