<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Flow đăng ký-trả tiền của v2. Đăng ký = chọn gói + nhập email → tạo đơn → PayOS.
 */
class RegistrationController extends Controller
{
    /** Link giới thiệu sale: /dk/{sale}/{goi?} → nhớ mã sale, chuyển tới trang đăng ký. */
    public function referral(Request $request, string $sale, ?string $goi = null)
    {
        if ($code = Sales::resolve($sale)) {
            $request->session()->put('sale_code', $code);
        }

        $map = ['thang' => 'month', 'tuan' => 'week'];
        $goiKey = $map[strtolower((string) $goi)] ?? null;

        return redirect()->route('register', $goiKey ? ['goi' => $goiKey] : []);
    }

    public function create(Request $request)
    {
        $packages = config('pricing.packages');

        $selected = $request->query('goi');
        if (! is_string($selected) || ! array_key_exists($selected, $packages)) {
            $selected = 'month';
        }

        return view('auth.register', [
            'packages' => $packages,
            'selected' => $selected,
            'sale' => Sales::resolve($request->session()->get('sale_code')),
        ]);
    }

    public function store(Request $request)
    {
        $packages = config('pricing.packages');

        $data = $request->validate([
            'email' => 'required|email|max:255',
            'package' => 'required|in:' . implode(',', array_keys($packages)),
            'quantity' => 'required|integer|min:1',
            'sale' => 'nullable|string|max:16',
        ]);

        $package = $packages[$data['package']];
        $quantity = min($data['quantity'], $package['max']);
        $amount = $package['price'] * $quantity;

        $saleCode = Sales::resolve($data['sale'] ?? null)
            ?? Sales::resolve($request->session()->get('sale_code'));

        // Chống double-submit: dùng lại đơn pending y hệt tạo gần đây.
        $order = Order::where('email', $data['email'])
            ->where('type', Order::TYPE_REGISTRATION)
            ->where('package', $data['package'])
            ->where('quantity', $quantity)
            ->where('amount', $amount)
            ->where('status', Order::STATUS_PENDING)
            ->where('created_at', '>=', now()->subHours(2))
            ->latest()
            ->first();

        if (! $order) {
            $order = Order::create([
                'order_code' => Order::generateCode(),
                'email' => $data['email'],
                'type' => Order::TYPE_REGISTRATION,
                'package' => $data['package'],
                'quantity' => $quantity,
                'amount' => $amount,
                'status' => Order::STATUS_PENDING,
                'sale_code' => $saleCode,
            ]);
        }

        return redirect()->to(URL::signedRoute('payment.show', $order));
    }
}
