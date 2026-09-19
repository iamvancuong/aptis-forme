<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\Sales;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($o) => [
                'id' => $o->id,
                'order_code' => $o->order_code,
                'email' => $o->email,
                'package' => $o->package,
                'quantity' => $o->quantity,
                'amount' => $o->amount,
                'status' => $o->status,
                'sale' => $o->sale_code ? Sales::name($o->sale_code) : null,
                'paid_at' => $o->paid_at?->format('d/m/Y H:i'),
                'created_at' => $o->created_at?->format('d/m/Y H:i'),
            ]);

        // Doanh thu theo mã sale (chỉ đơn đã thanh toán).
        $bySale = Order::where('status', Order::STATUS_PAID)
            ->selectRaw('sale_code, count(*) as orders, sum(amount) as revenue')
            ->groupBy('sale_code')
            ->get()
            ->map(fn ($r) => [
                'sale' => $r->sale_code ? Sales::name($r->sale_code) : 'Trực tiếp',
                'orders' => (int) $r->orders,
                'revenue' => (int) $r->revenue,
            ]);

        return Inertia::render('Admin/Orders', [
            'orders' => $orders,
            'filters' => ['status' => $status],
            'revenue' => [
                'total' => (int) Order::where('status', Order::STATUS_PAID)->sum('amount'),
                'by_sale' => $bySale,
            ],
        ]);
    }
}
