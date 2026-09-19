<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({ attempts: Object });

const skillLabel = { reading: 'Reading', listening: 'Listening', grammar: 'Grammar', writing: 'Writing', speaking: 'Speaking' };
</script>

<template>
    <Head title="Lịch sử làm bài" />
    <AppLayout>
        <h1 class="text-2xl font-bold text-slate-900">Lịch sử làm bài</h1>

        <div class="mt-6 overflow-x-auto rounded-2xl bg-white ring-1 ring-slate-200">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Kỹ năng</th>
                        <th class="px-4 py-3 font-medium">Hình thức</th>
                        <th class="px-4 py-3 font-medium">Điểm</th>
                        <th class="px-4 py-3 font-medium">Thời gian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="a in attempts.data" :key="a.id" class="cursor-pointer hover:bg-slate-50" @click="router.visit(`/history/${a.id}`)">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ skillLabel[a.skill] || a.skill }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ a.mode === 'mock' ? 'Thi thử' : 'Luyện tập' }}</td>
                        <td class="px-4 py-3">
                            <span v-if="a.score !== null" class="font-semibold" :class="a.score >= 50 ? 'text-emerald-600' : 'text-amber-600'">{{ Math.round(a.score) }}%</span>
                            <span v-else class="text-slate-400">—</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ a.created_at }}</td>
                    </tr>
                    <tr v-if="attempts.data.length === 0">
                        <td colspan="4" class="px-4 py-10 text-center text-slate-400">Chưa có lượt làm bài nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
