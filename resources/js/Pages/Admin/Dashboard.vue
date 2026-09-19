<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({ stats: Object });

const vnd = (n) => new Intl.NumberFormat('vi-VN').format(n) + '₫';
</script>

<template>
    <Head title="Admin · Tổng quan" />
    <AdminLayout>
        <h1 class="text-2xl font-bold text-slate-900">Tổng quan</h1>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                <div class="text-sm text-slate-500">Học viên</div>
                <div class="mt-1 text-2xl font-bold text-slate-900">{{ stats.users }}</div>
                <div class="text-xs text-emerald-600">{{ stats.active_users }} còn hạn</div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                <div class="text-sm text-slate-500">Đơn đã thanh toán</div>
                <div class="mt-1 text-2xl font-bold text-slate-900">{{ stats.paid_orders }}</div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                <div class="text-sm text-slate-500">Doanh thu tháng này</div>
                <div class="mt-1 text-2xl font-bold text-indigo-700">{{ vnd(stats.revenue_month) }}</div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                <div class="text-sm text-slate-500">Tổng doanh thu</div>
                <div class="mt-1 text-2xl font-bold text-slate-900">{{ vnd(stats.revenue) }}</div>
            </div>
        </div>

        <p class="mt-8 text-sm text-slate-500">
            Admin v2 chỉ quản lý <b>Học viên</b> và <b>Thanh toán</b>. Nội dung bài học được quản lý ở hệ thống v1.
        </p>
    </AdminLayout>
</template>
