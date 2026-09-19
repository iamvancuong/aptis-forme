<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({ skill: String, bySet: Boolean, parts: Array, sets: Array });

const skillMeta = {
    reading: ['📖', 'Reading'], listening: ['🎧', 'Listening'], grammar: ['✏️', 'Grammar & Vocab'],
    writing: ['📝', 'Writing'], speaking: ['🗣️', 'Speaking'],
};
const meta = (s) => skillMeta[s] || ['📘', s];
</script>

<template>
    <Head :title="meta(skill)[1]" />
    <AppLayout>
        <Link href="/dashboard" class="text-sm text-slate-500 hover:text-slate-800">← Kỹ năng</Link>
        <h1 class="mt-2 flex items-center gap-3 text-2xl font-bold text-slate-900">
            <span class="text-2xl">{{ meta(skill)[0] }}</span> {{ meta(skill)[1] }}
        </h1>

        <!-- Writing / Speaking: theo BỘ (mỗi bộ = 1 kịch bản đủ 4 part) -->
        <template v-if="bySet">
            <p class="mt-1 text-slate-500">Mỗi bộ đề là một kịch bản đầy đủ (Part 1 → 4). Chọn một bộ để luyện.</p>
            <div class="mt-6 grid gap-3">
                <Link
                    v-for="set in sets"
                    :key="set.id"
                    :href="`/practice/${set.id}`"
                    class="flex items-center justify-between gap-3 rounded-2xl bg-white p-4 ring-1 ring-slate-200 hover:ring-brand-300 hover:shadow-sm"
                >
                    <div class="min-w-0">
                        <div class="truncate font-medium text-slate-900">{{ set.title }}</div>
                        <div class="text-xs text-slate-400">{{ set.parts }} phần</div>
                    </div>
                    <span class="flex-shrink-0 rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-700">Luyện đề →</span>
                </Link>
                <div v-if="sets.length === 0" class="rounded-2xl bg-white p-8 text-center text-slate-400 ring-1 ring-slate-200">Chưa có bộ đề.</div>
            </div>
        </template>

        <!-- Reading / Listening / Grammar: theo PART -->
        <template v-else>
            <div class="mt-6 space-y-5">
                <section v-for="p in parts" :key="p.part" class="rounded-2xl bg-white ring-1 ring-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3">
                        <h2 class="font-semibold text-slate-900">Part {{ p.part }}</h2>
                        <span class="text-xs text-slate-400">{{ p.sets.length }} bộ đề</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <Link
                            v-for="set in p.sets"
                            :key="set.id"
                            :href="`/practice/${set.id}`"
                            class="flex items-center justify-between px-5 py-3 text-sm hover:bg-slate-50"
                        >
                            <span class="text-slate-800">{{ set.title }}</span>
                            <span class="rounded-lg bg-brand-50 px-3 py-1 text-xs font-medium text-brand-700">Làm bài →</span>
                        </Link>
                        <div v-if="p.sets.length === 0" class="px-5 py-4 text-sm text-slate-400">Chưa có bộ đề công khai.</div>
                    </div>
                </section>
            </div>
        </template>
    </AppLayout>
</template>
