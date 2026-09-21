<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({ stats: Object, bySkill: Array, topUsers: Array, attempts: Object, filters: Object });

const f = reactive({ skill: props.filters.skill || '', days: props.filters.days ?? 7, q: props.filters.q || '' });

function apply() {
    router.get('/admin/activity', { ...f }, { preserveState: true, replace: true });
}

const skills = ['reading', 'listening', 'grammar', 'writing', 'speaking'];
const skillColor = {
    reading: 'bg-sky-50 text-sky-700',
    listening: 'bg-amber-50 text-amber-700',
    grammar: 'bg-violet-50 text-violet-700',
    writing: 'bg-emerald-50 text-emerald-700',
    speaking: 'bg-rose-50 text-rose-700',
};

function fmtDuration(s) {
    if (!s) return '—';
    const m = Math.floor(s / 60);
    return m ? `${m}p${String(s % 60).padStart(2, '0')}` : `${s}s`;
}
</script>

<template>
    <Head title="Admin · Hoạt động" />
    <AdminLayout>
        <h1 class="text-2xl font-bold text-slate-900">Hoạt động làm bài</h1>

        <!-- Người dùng thực sự đang dùng -->
        <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                <div class="text-xs text-slate-500">Người làm bài hôm nay</div>
                <div class="mt-1 text-2xl font-bold text-slate-900">{{ stats.active_today }}</div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                <div class="text-xs text-slate-500">Người làm bài 7 ngày</div>
                <div class="mt-1 text-2xl font-bold text-slate-900">{{ stats.active_7d }}</div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                <div class="text-xs text-slate-500">Người làm bài 30 ngày</div>
                <div class="mt-1 text-2xl font-bold text-slate-900">{{ stats.active_30d }}</div>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                <div class="text-xs text-slate-500">Lượt làm bài hôm nay</div>
                <div class="mt-1 text-2xl font-bold text-slate-900">{{ stats.attempts_today }}</div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <form @submit.prevent="apply" class="mt-6 flex flex-wrap items-center gap-2">
            <select v-model="f.days" @change="apply" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option :value="1">Hôm nay + hôm qua</option>
                <option :value="7">7 ngày</option>
                <option :value="30">30 ngày</option>
                <option :value="0">Tất cả</option>
            </select>
            <select v-model="f.skill" @change="apply" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Mọi kỹ năng</option>
                <option v-for="s in skills" :key="s" :value="s">{{ s }}</option>
            </select>
            <input v-model="f.q" type="search" placeholder="Tìm email / tên"
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none" />
            <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Lọc</button>
        </form>

        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl bg-white ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-5 py-3 font-semibold text-slate-800">Theo kỹ năng</div>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs text-slate-400">
                        <tr><th class="px-5 py-2 font-medium">Kỹ năng</th><th class="px-5 py-2 font-medium text-right">Lượt làm</th><th class="px-5 py-2 font-medium text-right">Số người</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="s in bySkill" :key="s.skill">
                            <td class="px-5 py-2"><span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="skillColor[s.skill] || 'bg-slate-100 text-slate-600'">{{ s.skill }}</span></td>
                            <td class="px-5 py-2 text-right font-medium text-slate-800">{{ s.attempts }}</td>
                            <td class="px-5 py-2 text-right text-slate-600">{{ s.users }}</td>
                        </tr>
                        <tr v-if="!bySkill.length"><td colspan="3" class="px-5 py-6 text-center text-slate-400">Chưa có lượt làm bài.</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="rounded-2xl bg-white ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-5 py-3 font-semibold text-slate-800">Học viên tích cực nhất</div>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="u in topUsers" :key="u.id">
                            <td class="px-5 py-2">
                                <Link :href="`/admin/users/${u.id}`" class="hover:text-indigo-700">
                                    <div class="font-medium text-slate-800">{{ u.name }}</div>
                                    <div class="text-xs text-slate-500">{{ u.email }}</div>
                                </Link>
                            </td>
                            <td class="px-5 py-2 text-right font-medium text-slate-800">{{ u.attempts }} lượt</td>
                            <td class="px-5 py-2 text-right text-xs text-slate-400">{{ u.last_at }}</td>
                        </tr>
                        <tr v-if="!topUsers.length"><td colspan="3" class="px-5 py-6 text-center text-slate-400">—</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Từng lượt làm bài -->
        <div class="mt-6 overflow-x-auto rounded-2xl bg-white ring-1 ring-slate-200">
            <div class="border-b border-slate-100 px-5 py-3 font-semibold text-slate-800">Từng lượt làm bài ({{ attempts.total }})</div>
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Thời gian</th>
                        <th class="px-4 py-3 font-medium">Học viên</th>
                        <th class="px-4 py-3 font-medium">Bài</th>
                        <th class="px-4 py-3 font-medium text-right">Điểm</th>
                        <th class="px-4 py-3 font-medium text-right">Làm trong</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="a in attempts.data" :key="a.id">
                        <td class="whitespace-nowrap px-4 py-3 text-xs text-slate-500">{{ a.created_at }}</td>
                        <td class="px-4 py-3">
                            <Link v-if="a.user" :href="`/admin/users/${a.user.id}`" class="hover:text-indigo-700">
                                <div class="font-medium text-slate-800">{{ a.user.name }}</div>
                                <div class="text-xs text-slate-500">{{ a.user.email }}</div>
                            </Link>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="skillColor[a.skill] || 'bg-slate-100 text-slate-600'">{{ a.skill }}</span>
                                <span v-if="a.mode === 'mock'" class="rounded-full bg-slate-800 px-2 py-0.5 text-xs text-white">Thi thử</span>
                            </div>
                            <div class="mt-1 text-slate-700">{{ a.set_title || '—' }}</div>
                            <div class="text-xs text-slate-400">{{ a.answers_count }} câu</div>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold" :class="a.score === null ? 'text-slate-400' : (a.score >= 50 ? 'text-emerald-600' : 'text-amber-600')">
                            {{ a.score !== null ? Math.round(a.score) + '%' : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right text-slate-500">{{ fmtDuration(a.duration_seconds) }}</td>
                        <td class="px-4 py-3 text-right">
                            <Link :href="`/history/${a.id}`" class="rounded-md bg-slate-100 px-2 py-1 text-xs hover:bg-slate-200">Xem bài</Link>
                        </td>
                    </tr>
                    <tr v-if="attempts.data.length === 0">
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">Không có lượt làm bài nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="attempts.links" class="mt-4 flex flex-wrap gap-1">
            <Link
                v-for="(link, i) in attempts.links"
                :key="i"
                :href="link.url || ''"
                class="rounded-md px-3 py-1.5 text-sm"
                :class="[link.active ? 'bg-indigo-600 text-white' : 'bg-white ring-1 ring-slate-200 text-slate-600', !link.url && 'pointer-events-none opacity-40']"
                v-html="link.label"
            />
        </div>
    </AdminLayout>
</template>
