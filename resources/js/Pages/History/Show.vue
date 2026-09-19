<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({ attempt: Object, items: Array });

function fmtAnswer(v) {
    if (v === null || v === undefined || v === '') return '(bỏ trống)';
    if (Array.isArray(v)) return v.join(', ');
    if (typeof v === 'object') return Object.values(v).join(', ');
    return String(v);
}
function fmtKey(k) {
    if (!k) return '';
    const val = k.correct_answers ?? k.correct_answer ?? k.correct_option ?? k.sentences;
    if (val === undefined) return '';
    if (Array.isArray(val)) return val.join(', ');
    if (typeof val === 'object') return Object.values(val).join(', ');
    return String(val);
}
</script>

<template>
    <Head :title="`Kết quả — ${attempt.set_title || attempt.skill}`" />
    <div class="min-h-screen bg-slate-50">
        <header class="bg-white ring-1 ring-slate-200">
            <div class="mx-auto max-w-3xl px-6 py-4 flex items-center justify-between">
                <a href="/history" class="text-sm text-slate-500 hover:text-slate-800">← Lịch sử</a>
                <span class="text-xs font-semibold uppercase tracking-wide text-brand-600">{{ attempt.skill }}</span>
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-6 py-8">
            <div class="rounded-2xl bg-white p-6 ring-1 ring-slate-200 text-center">
                <div class="text-sm text-slate-500">{{ attempt.set_title || 'Bài làm' }}</div>
                <div class="mt-1 text-4xl font-bold" :class="attempt.score >= 50 ? 'text-emerald-600' : 'text-amber-600'">
                    {{ attempt.score !== null ? Math.round(attempt.score) + '%' : '—' }}
                </div>
                <div class="mt-1 text-xs text-slate-400">{{ attempt.created_at }}</div>
            </div>

            <div class="mt-6 space-y-4">
                <div v-for="(it, i) in items" :key="i" class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                    <div class="flex items-start justify-between">
                        <div class="text-sm font-semibold text-slate-700">Câu {{ i + 1 }}. {{ it.question?.stem }}</div>
                        <span v-if="it.is_correct === true" class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700">Đúng</span>
                        <span v-else-if="it.is_correct === false" class="rounded-full bg-red-50 px-2 py-0.5 text-xs text-red-700">Sai</span>
                        <span v-else class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Chờ chấm</span>
                    </div>
                    <dl class="mt-3 space-y-1 text-sm">
                        <div class="flex gap-2"><dt class="w-28 text-slate-400">Bạn trả lời:</dt><dd class="text-slate-800">{{ fmtAnswer(it.your_answer) }}</dd></div>
                        <div v-if="fmtKey(it.answer_key)" class="flex gap-2"><dt class="w-28 text-slate-400">Đáp án:</dt><dd class="font-medium text-emerald-700">{{ fmtKey(it.answer_key) }}</dd></div>
                        <div v-if="it.answer_key?.explanation" class="flex gap-2"><dt class="w-28 text-slate-400">Giải thích:</dt><dd class="text-slate-600" v-html="it.answer_key.explanation"></dd></div>
                    </dl>
                </div>
            </div>
        </main>
    </div>
</template>
