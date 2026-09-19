<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps({ skills: Array });

const skillMeta = {
    reading: ['📖', 'Reading', 'Đọc hiểu', 'from-sky-500 to-blue-600'],
    listening: ['🎧', 'Listening', 'Nghe hiểu', 'from-violet-500 to-purple-600'],
    grammar: ['✏️', 'Grammar & Vocab', 'Ngữ pháp & từ vựng', 'from-emerald-500 to-teal-600'],
    writing: ['📝', 'Writing', 'Viết', 'from-amber-500 to-orange-600'],
    speaking: ['🗣️', 'Speaking', 'Nói', 'from-rose-500 to-pink-600'],
};
const meta = (s) => skillMeta[s] || ['📘', s, '', 'from-slate-500 to-slate-600'];
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
        <p class="mt-1 text-slate-500">Chọn một kỹ năng để xem các phần và bộ đề.</p>

        <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="s in skills"
                :key="s.skill"
                :href="`/skills/${s.skill}`"
                class="group overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-lg hover:ring-brand-300"
            >
                <div class="flex items-center gap-4 bg-gradient-to-br p-5 text-white" :class="meta(s.skill)[3]">
                    <span class="text-3xl">{{ meta(s.skill)[0] }}</span>
                    <div>
                        <div class="text-lg font-bold">{{ meta(s.skill)[1] }}</div>
                        <div class="text-xs text-white/80">{{ meta(s.skill)[2] }}</div>
                    </div>
                </div>
                <div class="flex items-center justify-between px-5 py-4">
                    <div class="text-sm text-slate-500">
                        <span class="font-semibold text-slate-900">{{ s.parts_count }}</span> phần ·
                        <span class="font-semibold text-slate-900">{{ s.sets_count }}</span> bộ đề
                    </div>
                    <span class="text-sm font-medium text-brand-600 group-hover:translate-x-0.5">Vào luyện →</span>
                </div>
            </Link>
        </div>
    </AppLayout>
</template>
