<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps({ skills: Array });

const skillMeta = {
    reading: ['📖', 'Reading'], listening: ['🎧', 'Listening'], grammar: ['✏️', 'Grammar'],
    writing: ['📝', 'Writing'], speaking: ['🗣️', 'Speaking'],
};
</script>

<template>
    <Head title="Trang chủ" />
    <AppLayout>
        <!-- Thi thử -->
        <div class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-violet-600 p-7 text-white">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">Thi thử full đề có tính giờ</h2>
                    <p class="mt-1 text-sm text-brand-100">Mô phỏng phòng thi thật, chấm điểm ngay.</p>
                </div>
                <div class="flex gap-2">
                    <Link href="/mock-test/reading" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-brand-700 hover:bg-brand-50">Thi Reading</Link>
                    <Link href="/mock-test/listening" class="rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/30 hover:bg-white/25">Thi Listening</Link>
                </div>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-slate-900">Luyện tập theo kỹ năng</h1>
        <p class="mt-1 text-slate-500">Nội dung bài học đồng bộ trực tiếp từ hệ thống.</p>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="s in skills" :key="s.skill + s.part"
                 class="group rounded-2xl bg-white p-5 ring-1 ring-slate-200 transition hover:ring-brand-300 hover:shadow-sm">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-brand-600">
                    <span class="text-base">{{ (skillMeta[s.skill] || ['📘'])[0] }}</span>
                    {{ (skillMeta[s.skill] || [null, s.skill])[1] }} · Part {{ s.part }}
                </div>
                <div class="mt-1 text-lg font-semibold text-slate-900">{{ s.sets_count }} bộ đề</div>
                <Link v-if="s.first_set_id" :href="`/practice/${s.first_set_id}`"
                      class="mt-3 inline-block rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-700">
                    Làm thử bộ đầu →
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
