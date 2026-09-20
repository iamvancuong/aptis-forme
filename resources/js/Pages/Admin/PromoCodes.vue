<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({ codes: Array, totals: Object });

const form = useForm({
    code: '',
    free_days: 1,
    max_redemptions: null,
    expires_at: '',
    note: '',
});

function randomCode() {
    const s = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let r = '';
    for (let i = 0; i < 6; i++) r += s[Math.floor(Math.random() * s.length)];
    form.code = 'NHAI' + r;
}

function create() {
    form.transform((d) => ({ ...d, max_redemptions: d.max_redemptions || null, expires_at: d.expires_at || null }))
        .post('/admin/promo-codes', { preserveScroll: true, onSuccess: () => form.reset() });
}

function toggle(c) {
    router.put(`/admin/promo-codes/${c.id}`, { is_active: !c.is_active }, { preserveScroll: true });
}

function editExpiry(c) {
    const v = prompt(`Hạn dùng mã ${c.code} (YYYY-MM-DD, để trống = không hết hạn):`, c.expires_at || '');
    if (v === null) return;
    router.put(`/admin/promo-codes/${c.id}`, { expires_at: v || null }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Admin · Mã khuyến mãi" />
    <AdminLayout>
        <h1 class="text-2xl font-bold text-slate-900">Mã khuyến mãi</h1>
        <p class="mt-1 text-sm text-slate-500">Tạo mã học free, đặt hạn &amp; giới hạn lượt, theo dõi số người đổi và tỉ lệ gia hạn.</p>

        <!-- Tổng quan -->
        <div class="mt-5 grid grid-cols-3 gap-3">
            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                <div class="text-xs text-slate-400">Số mã</div>
                <div class="mt-1 text-2xl font-extrabold text-slate-900">{{ totals.codes }}</div>
            </div>
            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                <div class="text-xs text-slate-400">Lượt đổi (tài khoản free)</div>
                <div class="mt-1 text-2xl font-extrabold text-slate-900">{{ totals.used }}</div>
            </div>
            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                <div class="text-xs text-slate-400">Đã gia hạn / trả phí</div>
                <div class="mt-1 text-2xl font-extrabold text-emerald-600">
                    {{ totals.converted }}
                    <span class="text-sm font-medium text-slate-400">({{ totals.used > 0 ? Math.round(totals.converted / totals.used * 100) : 0 }}%)</span>
                </div>
            </div>
        </div>

        <!-- Tạo mã -->
        <form @submit.prevent="create" class="mt-6 rounded-2xl bg-white p-5 ring-1 ring-slate-200">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="text-xs font-medium text-slate-500">Mã</label>
                    <div class="mt-1 flex gap-1">
                        <input v-model="form.code" placeholder="VD: NHAIAPTIS" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm uppercase focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none" />
                        <button type="button" @click="randomCode" class="shrink-0 rounded-lg bg-slate-100 px-2 text-xs text-slate-600 hover:bg-slate-200" title="Tạo ngẫu nhiên">🎲</button>
                    </div>
                    <p v-if="form.errors.code" class="mt-1 text-xs text-red-600">{{ form.errors.code }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Số ngày free</label>
                    <input v-model="form.free_days" type="number" min="1" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none" />
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Giới hạn lượt</label>
                    <input v-model="form.max_redemptions" type="number" min="1" placeholder="∞" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none" />
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Hết hạn</label>
                    <input v-model="form.expires_at" type="date" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none" />
                </div>
                <div class="flex items-end">
                    <button :disabled="form.processing" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-60">Tạo mã</button>
                </div>
            </div>
            <div class="mt-3">
                <input v-model="form.note" placeholder="Ghi chú / chiến dịch (tuỳ chọn)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none" />
            </div>
        </form>

        <!-- Danh sách mã -->
        <div class="mt-6 overflow-x-auto rounded-2xl bg-white ring-1 ring-slate-200">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Mã</th>
                        <th class="px-4 py-3 font-medium">Free</th>
                        <th class="px-4 py-3 font-medium">Đổi / Giới hạn</th>
                        <th class="px-4 py-3 font-medium">Gia hạn</th>
                        <th class="px-4 py-3 font-medium">Hết hạn</th>
                        <th class="px-4 py-3 font-medium">Trạng thái</th>
                        <th class="px-4 py-3 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="c in codes" :key="c.id">
                        <td class="px-4 py-3">
                            <div class="font-mono font-bold text-slate-900">{{ c.code }}</div>
                            <div v-if="c.note" class="text-xs text-slate-400">{{ c.note }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ c.free_days }} ngày</td>
                        <td class="px-4 py-3 text-slate-600">{{ c.used }} / {{ c.max_redemptions ?? '∞' }}</td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-emerald-600">{{ c.converted }}</span>
                            <span class="text-xs text-slate-400"> ({{ c.conversion }}%)</span>
                        </td>
                        <td class="px-4 py-3">
                            <button @click="editExpiry(c)" class="text-slate-600 hover:text-indigo-600" :class="c.expired && 'text-red-600'">
                                {{ c.expires_label || 'Không' }} ✎
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <span v-if="c.expired" class="rounded-full bg-amber-50 px-2 py-0.5 text-xs text-amber-700">Hết hạn</span>
                            <span v-else-if="c.is_active" class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700">Bật</span>
                            <span v-else class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Tắt</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button @click="toggle(c)" class="rounded-md px-2 py-1 text-xs"
                                    :class="c.is_active ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'">
                                {{ c.is_active ? 'Tắt mã' : 'Bật mã' }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="codes.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">Chưa có mã nào. Tạo mã đầu tiên phía trên.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
