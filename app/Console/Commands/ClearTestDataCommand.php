<?php

namespace App\Console\Commands;

use App\Models\Attempt;
use App\Models\Order;
use App\Models\Redemption;
use App\Models\User;
use Illuminate\Console\Command;

class ClearTestDataCommand extends Command
{
    protected $signature = 'app:clear-test-data {--force : Bỏ qua xác nhận} {--orders-only : Chỉ xóa đơn hàng}';

    protected $description = 'Xóa dữ liệu TEST: đơn hàng (và tuỳ chọn: lịch sử, đổi mã, học viên không phải admin)';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Xóa dữ liệu test? KHÔNG hoàn tác được.')) {
            return self::FAILURE;
        }

        Order::query()->delete();
        $this->info('Đã xóa đơn hàng.');

        if (! $this->option('orders-only')) {
            Attempt::query()->delete();
            Redemption::query()->delete();
            $deleted = User::where('role', '!=', 'admin')->delete();
            $this->info("Đã xóa lịch sử, lượt đổi mã, và {$deleted} học viên (giữ admin + mã KM).");
        }

        return self::SUCCESS;
    }
}
