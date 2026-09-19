<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ mockTest: Object });

const mins = props.mockTest.duration_seconds ? Math.round(props.mockTest.duration_seconds / 60) : null;
</script>

<template>
    <Head title="Kết quả thi thử" />
    <div class="min-h-screen bg-slate-50">
        <header class="bg-white ring-1 ring-slate-200">
            <div class="mx-auto max-w-2xl px-6 py-4 flex items-center justify-between">
                <a href="/dashboard" class="text-sm text-slate-500 hover:text-slate-800">← Trang chủ</a>
                <span class="font-semibold text-slate-900">Kết quả thi thử · {{ mockTest.skill }}</span>
            </div>
        </header>

        <main class="mx-auto max-w-2xl px-6 py-10">
            <div class="rounded-2xl bg-white p-8 ring-1 ring-slate-200 text-center">
                <div class="text-sm text-slate-500">Điểm tổng</div>
                <div class="mt-1 text-5xl font-bold" :class="mockTest.score >= 50 ? 'text-emerald-600' : 'text-amber-600'">
                    {{ Math.round(mockTest.score) }}%
                </div>
                <div v-if="mins !== null" class="mt-2 text-xs text-slate-400">Hoàn thành trong {{ mins }} phút</div>
            </div>

            <div v-if="mockTest.section_scores?.length" class="mt-6 rounded-2xl bg-white p-6 ring-1 ring-slate-200">
                <h2 class="text-sm font-semibold text-slate-700">Điểm từng phần</h2>
                <div class="mt-3 space-y-3">
                    <div v-for="(sc, i) in mockTest.section_scores" :key="i">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Part {{ i + 1 }}</span>
                            <span class="font-medium">{{ Math.round(sc) }}%</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-indigo-500" :style="{ width: sc + '%' }"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <Link href="/dashboard" class="flex-1 rounded-xl bg-white px-4 py-3 text-center text-sm font-semibold ring-1 ring-slate-200">Trang chủ</Link>
                <Link :href="`/mock-test/${mockTest.skill}`" class="flex-1 rounded-xl bg-indigo-600 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-700">Thi lại</Link>
            </div>
        </main>
    </div>
</template>
