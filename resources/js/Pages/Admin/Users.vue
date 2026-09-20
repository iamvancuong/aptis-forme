<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({ users: Object, filters: Object });

const q = ref(props.filters.q || '');

function search() {
    router.get('/admin/users', { q: q.value }, { preserveState: true, replace: true });
}

function block(u) {
    router.post(`/admin/users/${u.id}/block`, {}, { preserveScroll: true });
}
function unblock(u) {
    router.post(`/admin/users/${u.id}/unblock`, {}, { preserveScroll: true });
}
function extend(u) {
    const days = prompt(`Gia hạn cho ${u.email} — số ngày?`, '30');
    if (days) router.post(`/admin/users/${u.id}/extend`, { days: Number(days) }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Admin · Học viên" />
    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900">Học viên</h1>
            <form @submit.prevent="search" class="flex gap-2">
                <input v-model="q" type="search" placeholder="Tìm email / tên"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none" />
                <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Tìm</button>
            </form>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl bg-white ring-1 ring-slate-200">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Nguồn</th>
                        <th class="px-4 py-3 font-medium">Hạn</th>
                        <th class="px-4 py-3 font-medium">Trạng thái</th>
                        <th class="px-4 py-3 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="u in users.data" :key="u.id">
                        <td class="px-4 py-3">
                            <Link :href="`/admin/users/${u.id}`" class="block hover:text-brand-700">
                                <div class="font-medium text-slate-900">{{ u.name }}</div>
                                <div class="text-slate-500">{{ u.email }}</div>
                            </Link>
                        </td>
                        <td class="px-4 py-3">
                            <span v-if="u.is_promo" class="rounded-full bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700">🎁 Mã KM</span>
                            <span v-else class="text-slate-600">{{ u.source }}</span>
                            <span v-if="u.converted" class="ml-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700">Đã gia hạn</span>
                            <span v-else-if="u.is_promo" class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Chưa gia hạn</span>
                        </td>
                        <td class="px-4 py-3">
                            <span :class="u.is_active_access ? 'text-slate-700' : 'text-red-600'">
                                {{ u.expires_at || 'Không giới hạn' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span v-if="u.status === 'blocked'" class="rounded-full bg-red-50 px-2 py-0.5 text-xs text-red-700">Đã khóa</span>
                            <span v-else class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700">Hoạt động</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2 text-xs">
                                <button @click="extend(u)" class="rounded-md bg-slate-100 px-2 py-1 hover:bg-slate-200">Gia hạn</button>
                                <button v-if="u.status === 'blocked'" @click="unblock(u)" class="rounded-md bg-emerald-100 px-2 py-1 text-emerald-700 hover:bg-emerald-200">Mở khóa</button>
                                <button v-else @click="block(u)" class="rounded-md bg-red-100 px-2 py-1 text-red-700 hover:bg-red-200">Khóa</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">Không có học viên nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="users.links" class="mt-4 flex flex-wrap gap-1">
            <Link
                v-for="(link, i) in users.links"
                :key="i"
                :href="link.url || ''"
                class="rounded-md px-3 py-1.5 text-sm"
                :class="[link.active ? 'bg-indigo-600 text-white' : 'bg-white ring-1 ring-slate-200 text-slate-600', !link.url && 'pointer-events-none opacity-40']"
                v-html="link.label"
            />
        </div>
    </AdminLayout>
</template>
