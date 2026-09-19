<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    skill: String,
    duration: Number,
    parts: Array,
    supported: Boolean,
    canStart: Boolean,
});

const skillLabel = { reading: 'Reading', listening: 'Listening', writing: 'Writing', speaking: 'Speaking' };

const form = useForm({ skill: props.skill });
function start() {
    form.post('/mock-test');
}
</script>

<template>
    <Head :title="`Thi thử ${skillLabel[skill]}`" />
    <div class="min-h-screen bg-slate-50">
        <header class="bg-white ring-1 ring-slate-200">
            <div class="mx-auto max-w-2xl px-6 py-4 flex items-center justify-between">
                <a href="/dashboard" class="text-sm text-slate-500 hover:text-slate-800">← Trang chủ</a>
                <span class="font-semibold text-slate-900">Thi thử · {{ skillLabel[skill] }}</span>
            </div>
        </header>

        <main class="mx-auto max-w-2xl px-6 py-10">
            <div class="rounded-2xl bg-white p-6 ring-1 ring-slate-200">
                <h1 class="text-2xl font-bold text-slate-900">Đề thi thử {{ skillLabel[skill] }}</h1>
                <p class="mt-1 text-slate-500">Mô phỏng phòng thi thật — làm cả bài trong thời gian giới hạn.</p>

                <dl class="mt-6 space-y-2 text-sm">
                    <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">Thời lượng</dt><dd class="font-semibold">{{ duration }} phút</dd></div>
                    <div v-for="p in parts" :key="p.part" class="flex justify-between">
                        <dt class="text-slate-500">Part {{ p.part }}</dt>
                        <dd :class="p.enough ? 'text-emerald-600' : 'text-red-600'">
                            {{ p.enough ? 'Sẵn sàng' : 'Thiếu đề' }} ({{ p.available }} bộ)
                        </dd>
                    </div>
                </dl>

                <div v-if="!supported" class="mt-6 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    Thi thử {{ skillLabel[skill] }} cần chấm bằng AI — sẽ mở ở bản cập nhật tới.
                </div>

                <button
                    v-else
                    @click="start"
                    :disabled="!canStart || form.processing"
                    class="mt-6 w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50"
                >
                    {{ form.processing ? 'Đang tạo đề…' : 'Bắt đầu thi' }}
                </button>
            </div>
        </main>
    </div>
</template>
