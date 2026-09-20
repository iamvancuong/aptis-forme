<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderFulfillmentService;
use App\Services\PayosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class PaymentController extends Controller
{
    public function __construct(
        private PayosService $payos,
        private OrderFulfillmentService $fulfillment,
    ) {}

    public function show(Order $order)
    {
        if ($order->isPaid()) {
            return redirect()->route('login')->with('success', 'Đơn đã thanh toán. Vui lòng kiểm tra email.');
        }

        if (in_array($order->status, [Order::STATUS_CANCELED, Order::STATUS_EXPIRED], true)) {
            $reason = $order->status === Order::STATUS_CANCELED ? 'đã bị hủy' : 'đã hết hạn';

            return redirect()->route('register')
                ->with('error', "Đơn {$order->order_code} {$reason}. Vui lòng tạo đơn mới.");
        }

        $package = config("pricing.packages.{$order->package}");

        // 🧪 Giả lập.
        if (config('payos.fake')) {
            return view('payment.pending', ['order' => $order, 'package' => $package, 'state' => 'fake']);
        }

        if (! $this->payos->isConfigured()) {
            return view('payment.pending', ['order' => $order, 'package' => $package, 'state' => 'unconfigured']);
        }

        // ♻️ Đơn đã có link PayOS → tái dùng QR đã lưu (PayOS cấm 2 link cùng orderCode).
        if ($order->payos_link_id) {
            if (! empty($order->meta['qr'])) {
                return $this->renderCheckout($order, $package);
            }

            return redirect()->away($this->checkoutUrl($order)); // đơn cũ chưa lưu QR
        }

        try {
            $link = $this->payos->createPaymentLink(
                $order,
                description: $this->paymentDescription($order),
                returnUrl: URL::signedRoute('payment.return', $order),
                cancelUrl: URL::signedRoute('payment.cancel', $order),
            );

            $order->update([
                'payos_link_id' => $link['paymentLinkId'],
                'meta' => array_merge((array) $order->meta, [
                    'checkout_url' => $link['checkoutUrl'],
                    'qr' => $link['qrCode'],
                    'account_number' => $link['accountNumber'],
                    'bin' => $link['bin'],
                    'amount' => $link['amount'],
                    'transfer_content' => $link['description'],
                ]),
            ]);

            return $this->renderCheckout($order->fresh(), $package);
        } catch (\Throwable $e) {
            Log::error('PayOS create link failed', ['order' => $order->id, 'error' => $e->getMessage()]);

            if ($recovered = $this->recoverExistingLink($order)) {
                return redirect()->away($recovered);
            }

            return view('payment.pending', [
                'order' => $order,
                'package' => $package,
                'state' => 'error',
                'retryUrl' => URL::signedRoute('payment.show', $order),
            ]);
        }
    }

    /** Trang thanh toán RIÊNG (brand nhaiaptis) — tự vẽ QR, KHÔNG hiện tên chủ tài khoản. */
    private function renderCheckout(Order $order, $package)
    {
        return view('payment.checkout', [
            'order' => $order,
            'package' => $package,
            'qr' => $order->meta['qr'] ?? '',
            'accountNumber' => $order->meta['account_number'] ?? '',
            'amount' => (int) ($order->meta['amount'] ?? $order->amount),
            'content' => $order->meta['transfer_content'] ?? $this->paymentDescription($order),
            'checkoutUrl' => $order->meta['checkout_url'] ?? '',
            'statusUrl' => route('payment.status', $order),
        ]);
    }

    /**
     * Kiểm tra trạng thái đơn (JS poll). TỰ hỏi PayOS nếu đơn còn pending →
     * nếu đã trả thì fulfill ngay (tạo tài khoản + gửi mail) — không lệ thuộc webhook.
     */
    public function status(Order $order)
    {
        if (! $order->isPaid()
            && $order->payos_link_id
            && ! config('payos.fake')
            && $this->payos->isConfigured()) {
            try {
                $info = $this->payos->getPaymentInfo($order->order_code);
                $paid = ($info['status'] ?? '') === 'PAID'
                    || (int) ($info['amountPaid'] ?? 0) >= (int) $order->amount;

                if ($paid) {
                    $this->fulfillment->fulfill($order); // idempotent: an toàn nếu webhook cũng chạy
                    $order->refresh();
                }
            } catch (\Throwable $e) {
                Log::warning('Poll PayOS status failed', ['order' => $order->id, 'error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'paid' => $order->isPaid(),
            'status' => $order->status,
        ]);
    }

    private function paymentDescription(Order $order): string
    {
        return $order->sale_code ? "nhaiaptis {$order->sale_code}" : 'Thanh toan nhaiaptis';
    }

    private function checkoutUrl(Order $order): string
    {
        return $order->meta['checkout_url']
            ?? $this->payos->checkoutUrlFor($order->payos_link_id);
    }

    private function recoverExistingLink(Order $order): ?string
    {
        try {
            $linkId = $this->payos->getPaymentInfo($order->order_code)['raw']['id'] ?? null;
            if (! $linkId) {
                return null;
            }

            $checkoutUrl = $this->payos->checkoutUrlFor($linkId);
            $order->update([
                'payos_link_id' => $linkId,
                'meta' => array_merge((array) $order->meta, ['checkout_url' => $checkoutUrl]),
            ]);

            return $checkoutUrl;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** 🧪 Giả lập "đã thanh toán" — CHỈ khi PAYOS_FAKE=true. */
    public function devFulfill(Order $order)
    {
        abort_unless(config('payos.fake'), 404);

        if (! $order->isPaid()) {
            $this->fulfillment->fulfill($order);
        }

        return redirect()->to(URL::signedRoute('payment.return', $order));
    }

    public function return(Order $order)
    {
        return view('payment.return', compact('order'));
    }

    public function cancel(Order $order)
    {
        if (! $order->isPaid()) {
            $order->update(['status' => Order::STATUS_CANCELED]);
        }

        return view('payment.cancel', compact('order'));
    }

    /** Webhook PayOS — nguồn xác nhận thanh toán DUY NHẤT đáng tin. */
    public function webhook(Request $request)
    {
        $payload = $request->all();

        Log::info('PayOS webhook', ['payload' => $payload]);

        if (! $this->payos->verifyWebhook($payload)) {
            Log::warning('PayOS webhook chữ ký không hợp lệ (ack)', ['payload' => $payload]);

            return response()->json(['success' => true]);
        }

        $data = $payload['data'] ?? [];

        $order = Order::where('order_code', $data['orderCode'] ?? 0)->first();
        if (! $order) {
            return response()->json(['success' => true]);
        }

        $isSuccess = ($payload['code'] ?? null) === '00' || ($payload['success'] ?? false) === true;
        $amountOk = (int) ($data['amount'] ?? -1) === (int) $order->amount;

        if ($isSuccess && $amountOk) {
            $this->fulfillment->fulfill($order);
        }

        return response()->json(['success' => true]);
    }
}
