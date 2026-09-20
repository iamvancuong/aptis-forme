<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({ user: Object, attempts: Array, orders: Array });

const vnd = (n) => new Intl.NumberFormat('vi-VN').format(n) + '₫';

function extend() {
    const days = prompt(`Gia hạn cho ${props.user.email} — số ngày?`, '30');
    if (days) router.post(`/admin/users/${props.user.id}/extend`, { days: Number(days) }, { preserveScroll: true });
}
function addAi() {
    const amount = prompt(`Thêm lượt AI cho ${props.user.email}?`, '10');
    if (amount) router.post(`/admin/users/${props.user.id}/add-ai`, { amount: Number(amount) }, { preserveScroll: true });
}
function resetAi() {
    if (confirm('Reset lượt AI về mặc định?')) router.post(`/admin/users/${props.user.id}/reset-ai`, {}, { preserveScroll: true });
}
function toggleBlock() {
    const url = props.user.status === 'blocked' ? 'unblock' : 'block';
    router.post(`/admin/users/${props.user.id}/${url}`, {}, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Học viên · ${user.email}`" />
    <AdminLayout>
        <Link href="/admin/users" class="text-sm text-slate-500 hover:text-slate-800">← Danh sách học viên</Link>

        <div class="mt-4 grid gap-6 lg:grid-cols-3">
            <!-- Thông tin + hành động -->
            <div class="space-y-4">
                <div class="rounded-2xl bg-white p-6 ring-1 ring-slate-200">
                    <div class="text-lg font-bold text-slate-900">{{ user.name }}</div>
                    <div class="text-sm text-slate-500">{{ user.email }}</div>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Trạng thái</dt><dd :class="user.status === 'blocked' ? 'text-red-600' : 'text-emerald-600'">{{ user.status === 'blocked' ? 'Đã khóa' : 'Hoạt động' }}</dd></div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Nguồn</dt>
                            <dd>
                                <span v-if="user.is_promo" class="rounded-full bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700">🎁 Mã {{ user.promo_code || 'KM' }}</span>
                                <span v-else>{{ user.source }}</span>
                            </dd>
                        </div>
                        <div v-if="user.is_promo" class="flex justify-between">
                            <dt class="text-slate-500">Gia hạn</dt>
                            <dd :class="user.converted ? 'text-emerald-600 font-medium' : 'text-slate-500'">
                                {{ user.converted ? 'Đã gia hạn / trả phí' : 'Chưa gia hạn' }}
                            </dd>
                        </div>
                        <div class="flex justify-between"><dt class="text-slate-500">Hạn dùng</dt><dd :class="!user.is_active_access && 'text-red-600'">{{ user.expires_at || 'Không giới hạn' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Trình độ đích</dt><dd>{{ user.target_level || '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Thiết bị tối đa</dt><dd>{{ user.max_devices }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Vi phạm</dt><dd>{{ user.violation_count }}</dd></div>
                    </dl>
                </div>

                <div class="rounded-2xl bg-white p-6 ring-1 ring-slate-200">
                    <div class="text-sm font-semibold text-slate-700">Lượt chấm AI</div>
                    <div class="mt-1 text-2xl font-bold text-brand-700">{{ user.ai_remaining }}<span class="text-sm font-normal text-slate-400"> còn lại</span></div>
                    <div class="text-xs text-slate-400">Đã cộng thêm: {{ user.ai_extra_uses }}</div>
                    <div class="mt-3 flex gap-2">
                        <button @click="addAi" class="rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-700">+ Thêm lượt</button>
                        <button @click="resetAi" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200">Reset</button>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 ring-1 ring-slate-200">
                    <div class="text-sm font-semibold text-slate-700">Hành động</div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button @click="extend" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200">Gia hạn</button>
                        <button @click="toggleBlock" class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                :class="user.status === 'blocked' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-700 hover:bg-red-200'">
                            {{ user.status === 'blocked' ? 'Mở khóa' : 'Khóa' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lịch sử + đơn hàng -->
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl bg-white ring-1 ring-slate-200">
                    <div class="border-b border-slate-100 px-5 py-3 font-semibold text-slate-800">Lịch sử làm bài ({{ attempts.length }})</div>
                    <div class="max-h-96 overflow-y-auto">
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="a in attempts" :key="a.id">
                                    <td class="px-5 py-2 font-medium text-slate-800">{{ a.skill }}</td>
                                    <td class="px-5 py-2 text-slate-500">{{ a.mode === 'mock' ? 'Thi thử' : 'Luyện tập' }}</td>
                                    <td class="px-5 py-2">{{ a.score !== null ? Math.round(a.score) + '%' : '—' }}</td>
                                    <td class="px-5 py-2 text-xs text-slate-400">{{ a.created_at }}</td>
                                </tr>
                                <tr v-if="attempts.length === 0"><td class="px-5 py-6 text-center text-slate-400" colspan="4">Chưa làm bài nào.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white ring-1 ring-slate-200">
                    <div class="border-b border-slate-100 px-5 py-3 font-semibold text-slate-800">Đơn hàng ({{ orders.length }})</div>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="o in orders" :key="o.order_code">
                                <td class="px-5 py-2 font-mono text-xs text-slate-500">{{ o.order_code }}</td>
                                <td class="px-5 py-2">{{ o.package }}</td>
                                <td class="px-5 py-2 font-medium">{{ vnd(o.amount) }}</td>
                                <td class="px-5 py-2">
                                    <span class="rounded-full px-2 py-0.5 text-xs" :class="o.status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'">{{ o.status }}</span>
                                </td>
                                <td class="px-5 py-2 text-xs text-slate-400">{{ o.paid_at || '—' }}</td>
                            </tr>
                            <tr v-if="orders.length === 0"><td class="px-5 py-6 text-center text-slate-400" colspan="5">Chưa có đơn.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
