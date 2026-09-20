<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderFulfillmentService;
use Illuminate\Console\Command;

class FulfillOrderCommand extends Command
{
    protected $signature = 'app:fulfill-order {code}';

    protected $description = 'Ép xử lý 1 đơn đã thanh toán (tạo/gia hạn tài khoản + gửi mail). Idempotent.';

    public function handle(OrderFulfillmentService $fulfillment): int
    {
        $order = Order::where('order_code', $this->argument('code'))->first();

        if (! $order) {
            $this->error('Không tìm thấy đơn với mã: ' . $this->argument('code'));

            return self::FAILURE;
        }

        $fulfillment->fulfill($order);
        $this->info("Đã xử lý đơn {$order->order_code} (email: {$order->email}).");

        return self::SUCCESS;
    }
}
