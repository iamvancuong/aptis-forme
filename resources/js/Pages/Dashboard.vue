<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

const props = defineProps({
    skills: Array,
    stats: Object,
    next: Object,
});

// emoji · tên EN · tên VN · màu thanh tiến độ · màu chữ · nền nhạt
const skillMeta = {
    reading: { emoji: '📖', en: 'Reading', vn: 'Đọc hiểu', bar: 'bg-emerald-500', text: 'text-emerald-600', soft: 'bg-emerald-50' },
    listening: { emoji: '🎧', en: 'Listening', vn: 'Nghe hiểu', bar: 'bg-cyan-500', text: 'text-cyan-600', soft: 'bg-cyan-50' },
    grammar: { emoji: '✏️', en: 'Grammar & Vocab', vn: 'Ngữ pháp & từ vựng', bar: 'bg-violet-500', text: 'text-violet-600', soft: 'bg-violet-50' },
    writing: { emoji: '📝', en: 'Writing', vn: 'Viết', bar: 'bg-amber-500', text: 'text-amber-600', soft: 'bg-amber-50' },
    speaking: { emoji: '🗣️', en: 'Speaking', vn: 'Nói', bar: 'bg-blue-600', text: 'text-blue-600', soft: 'bg-blue-50' },
};
const meta = (s) => skillMeta[s] || { emoji: '📘', en: s, vn: '', bar: 'bg-slate-500', text: 'text-slate-600', soft: 'bg-slate-50' };

const firstName = computed(() => (props.stats?.name || '').trim().split(/\s+/).pop() || 'bạn');
const overall = computed(() => props.stats?.overall);
const nextMeta = computed(() => (props.next ? meta(props.next.skill) : null));

const studyTime = computed(() => {
    const m = props.stats?.study_minutes || 0;
    if (m < 60) return `${m} phút`;
    const h = Math.floor(m / 60);
    return `${h} giờ ${m % 60} phút`;
});
</script>

<template>
    <Head title="Trang chủ" />
    <AppLayout>
        <!-- ══════════ Lời chào ══════════ -->
        <div>
            <p class="text-sm font-medium text-slate-400">Hôm nay</p>
            <h1 class="mt-0.5 text-2xl font-extrabold tracking-tight text-slate-900">
                Xin chào, {{ firstName }} 👋
            </h1>
        </div>

        <!-- ══════════ Thống kê nhanh: streak · thời gian học · lượt làm ══════════ -->
        <div class="mt-4 grid grid-cols-3 gap-3">
            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                <div class="text-xs font-medium text-slate-400">🔥 Chuỗi ngày</div>
                <div class="mt-1 text-xl font-extrabold text-slate-900">{{ stats?.streak || 0 }}<span class="ml-1 text-sm font-medium text-slate-400">ngày</span></div>
            </div>
            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                <div class="text-xs font-medium text-slate-400">⏱️ Thời gian học</div>
                <div class="mt-1 text-xl font-extrabold text-slate-900">{{ studyTime }}</div>
            </div>
            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                <div class="text-xs font-medium text-slate-400">📊 Lượt làm bài</div>
                <div class="mt-1 text-xl font-extrabold text-slate-900">{{ stats?.total_attempts || 0 }}</div>
            </div>
        </div>

        <!-- ══════════ Tiến độ tổng quan ══════════ -->
        <div class="mt-5 rounded-2xl bg-white p-6 ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-slate-700">Điểm trung bình của bạn</h2>
                <span class="text-xl font-extrabold text-slate-900">
                    {{ overall !== null && overall !== undefined ? overall + '%' : '—' }}
                </span>
            </div>
            <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-gradient-to-r from-brand-600 to-violet-600 transition-all"
                     :style="{ width: (overall || 0) + '%' }"></div>
            </div>
            <p class="mt-2 text-sm text-slate-500">
                <template v-if="overall !== null && overall !== undefined">
                    Dựa trên {{ stats.total_attempts }} lượt làm bài đã hoàn thành.
                </template>
                <template v-else>
                    Hoàn thành bài đầu tiên để bắt đầu theo dõi tiến độ nhé.
                </template>
            </p>
        </div>

        <!-- ══════════ Tiến độ theo kỹ năng ══════════ -->
        <h2 class="mt-8 text-lg font-bold text-slate-900">Luyện tập theo kỹ năng</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <Link
                v-for="s in skills"
                :key="s.skill"
                :href="`/skills/${s.skill}`"
                class="group rounded-2xl bg-white p-5 ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-brand-300"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl text-xl" :class="meta(s.skill).soft">{{ meta(s.skill).emoji }}</span>
                        <div>
                            <div class="font-bold text-slate-900">{{ meta(s.skill).en }}</div>
                            <div class="text-xs text-slate-400">{{ meta(s.skill).vn }}</div>
                        </div>
                    </div>
                    <span class="text-lg font-extrabold" :class="s.avg_score !== null ? meta(s.skill).text : 'text-slate-300'">
                        {{ s.avg_score !== null ? s.avg_score + '%' : '—' }}
                    </span>
                </div>
                <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full transition-all" :class="meta(s.skill).bar" :style="{ width: (s.avg_score || 0) + '%' }"></div>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                    <span>
                        <template v-if="s.avg_score !== null">
                            Đã làm {{ s.done_sets }}/{{ s.sets_count }} bộ đề
                        </template>
                        <template v-else>
                            {{ s.parts_count }} phần · {{ s.sets_count }} bộ đề
                        </template>
                    </span>
                    <span class="font-medium text-brand-600 group-hover:translate-x-0.5">Vào luyện →</span>
                </div>
            </Link>
        </div>

        <!-- ══════════ Bài tiếp theo + Thi thử ══════════ -->
        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            <!-- Gợi ý bài tiếp theo -->
            <div v-if="next" class="flex items-center justify-between rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-brand-600">Bài tiếp theo</p>
                    <p class="mt-1 font-bold text-slate-900">{{ nextMeta.emoji }} {{ nextMeta.en }}</p>
                    <p class="text-sm text-slate-500">Tiếp tục luyện để cân bằng các kỹ năng.</p>
                </div>
                <Link :href="`/skills/${next.skill}`" class="shrink-0 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    Tiếp tục học →
                </Link>
            </div>

            <!-- Thi thử full đề -->
            <div class="flex items-center justify-between rounded-2xl bg-gradient-to-br from-brand-600 to-violet-600 p-5 text-white">
                <div>
                    <p class="font-bold">Thi thử full đề có tính giờ</p>
                    <p class="mt-0.5 text-sm text-brand-100">Mô phỏng phòng thi thật, chấm điểm ngay.</p>
                </div>
                <div class="flex shrink-0 flex-col gap-2">
                    <Link href="/mock-test/reading" class="rounded-xl bg-white px-4 py-2 text-center text-sm font-semibold text-brand-700 hover:bg-brand-50">Thi Reading</Link>
                    <Link href="/mock-test/listening" class="rounded-xl bg-white/15 px-4 py-2 text-center text-sm font-semibold text-white ring-1 ring-white/30 hover:bg-white/25">Thi Listening</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
