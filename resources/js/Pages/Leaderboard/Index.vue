<script setup>
import { Head, router } from '@inertiajs/vue3';

defineProps({ skill: String, leaderboard: Array });

function switchSkill(s) {
    router.get('/leaderboard', { skill: s }, { preserveState: true });
}
const medal = (r) => (r === 1 ? '🥇' : r === 2 ? '🥈' : r === 3 ? '🥉' : r);
</script>

<template>
    <Head title="Bảng xếp hạng" />
    <div class="min-h-screen bg-slate-50">
        <header class="bg-white ring-1 ring-slate-200">
            <div class="mx-auto max-w-2xl px-6 py-4 flex items-center justify-between">
                <a href="/dashboard" class="text-sm text-slate-500 hover:text-slate-800">← Trang chủ</a>
                <span class="font-semibold text-slate-900">Bảng xếp hạng</span>
            </div>
        </header>

        <main class="mx-auto max-w-2xl px-6 py-8">
            <div class="flex gap-2">
                <button @click="switchSkill('reading')" class="rounded-lg px-4 py-2 text-sm" :class="skill==='reading' ? 'bg-indigo-600 text-white' : 'bg-white ring-1 ring-slate-200 text-slate-600'">Reading</button>
                <button @click="switchSkill('listening')" class="rounded-lg px-4 py-2 text-sm" :class="skill==='listening' ? 'bg-indigo-600 text-white' : 'bg-white ring-1 ring-slate-200 text-slate-600'">Listening</button>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200">
                <div v-for="row in leaderboard" :key="row.rank"
                     class="flex items-center justify-between px-5 py-3 border-b border-slate-100 last:border-0"
                     :class="row.is_me && 'bg-indigo-50'">
                    <div class="flex items-center gap-3">
                        <span class="w-7 text-center text-lg">{{ medal(row.rank) }}</span>
                        <span class="font-medium text-slate-800">{{ row.name }} <span v-if="row.is_me" class="text-xs text-indigo-600">(bạn)</span></span>
                    </div>
                    <span class="font-bold text-slate-900">{{ row.score }}%</span>
                </div>
                <div v-if="leaderboard.length === 0" class="px-5 py-10 text-center text-slate-400">
                    Chưa có ai hoàn thành thi thử {{ skill }}. Hãy là người đầu tiên!
                </div>
            </div>
        </main>
    </div>
</template>
