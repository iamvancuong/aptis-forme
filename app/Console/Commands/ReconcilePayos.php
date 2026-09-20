<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderFulfillmentService;
use App\Services\PayosService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Đối soát đơn `pending` với PayOS và kích hoạt đơn đã thanh toán.
 * Lưới an toàn khi webhook không tới. Chạy định kỳ qua cron; hoặc gọi tay 1 đơn bằng --order=.
 */
class ReconcilePayos extends Command
{
    protected $signature = 'payos:reconcile {--order= : Chỉ đối soát một order_code}';

    protected $description = 'Đối soát đơn pending với PayOS và kích hoạt đơn đã thanh toán';

    public function handle(PayosService $payos, OrderFulfillmentService $fulfillment): int
    {
        if (config('payos.fake') || ! $payos->isConfigured()) {
            $this->warn('PayOS chưa cấu hình (hoặc đang FAKE) — bỏ qua.');

            return self::SUCCESS;
        }

        $query = Order::where('status', Order::STATUS_PENDING);

        if ($code = $this->option('order')) {
            $query->where('order_code', $code);
        } else {
            $query->where('created_at', '>=', now()->subDays(3)); // chỉ đơn gần đây
        }

        $orders = $query->get();
        $activated = 0;

        foreach ($orders as $order) {
            try {
                $info = $payos->getPaymentInfo((int) $order->order_code);
            } catch (\Throwable $e) {
                $this->warn("Đơn {$order->order_code}: lỗi truy vấn — {$e->getMessage()}");

                continue;
            }

            if (($info['status'] ?? '') === 'PAID' && (int) ($info['amountPaid'] ?? 0) >= (int) $order->amount) {
                $fulfillment->fulfill($order); // idempotent
                $activated++;
                $this->info("✓ Đơn {$order->order_code} đã thanh toán → kích hoạt.");
                Log::info('PayOS reconcile: fulfilled order', ['order_code' => $order->order_code]);
            } else {
                $this->line("• Đơn {$order->order_code}: " . ($info['status'] ?? 'UNKNOWN'));
            }
        }

        $this->info("Hoàn tất: {$activated}/{$orders->count()} đơn được kích hoạt.");

        return self::SUCCESS;
    }
}
