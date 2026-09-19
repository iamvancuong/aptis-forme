<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({ orders: Object, filters: Object, revenue: Object });

const vnd = (n) => new Intl.NumberFormat('vi-VN').format(n) + '₫';

const statusLabel = {
    paid: ['Đã thanh toán', 'bg-emerald-50 text-emerald-700'],
    pending: ['Chờ thanh toán', 'bg-amber-50 text-amber-700'],
    canceled: ['Đã hủy', 'bg-slate-100 text-slate-500'],
    expired: ['Hết hạn', 'bg-slate-100 text-slate-500'],
};

function filterStatus(s) {
    router.get('/admin/orders', s ? { status: s } : {}, { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Admin · Đơn hàng" />
    <AdminLayout>
        <h1 class="text-2xl font-bold text-slate-900">Đơn hàng & Doanh thu</h1>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200 sm:col-span-1">
                <div class="text-sm text-slate-500">Tổng doanh thu</div>
                <div class="mt-1 text-2xl font-bold text-indigo-700">{{ vnd(revenue.total) }}</div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200 sm:col-span-2">
                <div class="text-sm text-slate-500">Theo nguồn giới thiệu</div>
                <div class="mt-2 space-y-1 text-sm">
                    <div v-for="(r, i) in revenue.by_sale" :key="i" class="flex justify-between">
                        <span class="text-slate-700">{{ r.sale }} <span class="text-slate-400">({{ r.orders }} đơn)</span></span>
                        <span class="font-medium">{{ vnd(r.revenue) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex gap-2 text-sm">
            <button @click="filterStatus(null)" class="rounded-lg px-3 py-1.5" :class="!filters.status ? 'bg-indigo-600 text-white' : 'bg-white ring-1 ring-slate-200 text-slate-600'">Tất cả</button>
            <button @click="filterStatus('paid')" class="rounded-lg px-3 py-1.5" :class="filters.status==='paid' ? 'bg-indigo-600 text-white' : 'bg-white ring-1 ring-slate-200 text-slate-600'">Đã thanh toán</button>
            <button @click="filterStatus('pending')" class="rounded-lg px-3 py-1.5" :class="filters.status==='pending' ? 'bg-indigo-600 text-white' : 'bg-white ring-1 ring-slate-200 text-slate-600'">Chờ</button>
        </div>

        <div class="mt-4 overflow-x-auto rounded-2xl bg-white ring-1 ring-slate-200">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Mã đơn</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Gói</th>
                        <th class="px-4 py-3 font-medium">Số tiền</th>
                        <th class="px-4 py-3 font-medium">Sale</th>
                        <th class="px-4 py-3 font-medium">Trạng thái</th>
                        <th class="px-4 py-3 font-medium">Thời gian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="o in orders.data" :key="o.id">
                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ o.order_code }}</td>
                        <td class="px-4 py-3">{{ o.email }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ o.package }} × {{ o.quantity }}</td>
                        <td class="px-4 py-3 font-medium">{{ vnd(o.amount) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ o.sale || '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs" :class="(statusLabel[o.status] || ['',''])[1]">
                                {{ (statusLabel[o.status] || [o.status])[0] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ o.paid_at || o.created_at }}</td>
                    </tr>
                    <tr v-if="orders.data.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">Chưa có đơn nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="orders.links" class="mt-4 flex flex-wrap gap-1">
            <Link v-for="(link, i) in orders.links" :key="i" :href="link.url || ''"
                  class="rounded-md px-3 py-1.5 text-sm"
                  :class="[link.active ? 'bg-indigo-600 text-white' : 'bg-white ring-1 ring-slate-200 text-slate-600', !link.url && 'pointer-events-none opacity-40']"
                  v-html="link.label" />
        </div>
    </AdminLayout>
</template>
