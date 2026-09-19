<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => User::where('role', 'user')->count(),
                'active_users' => User::where('role', 'user')->where('status', 'active')
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->count(),
                'paid_orders' => Order::where('status', Order::STATUS_PAID)->count(),
                'revenue' => (int) Order::where('status', Order::STATUS_PAID)->sum('amount'),
                'revenue_month' => (int) Order::where('status', Order::STATUS_PAID)
                    ->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount'),
            ],
        ]);
    }
}
