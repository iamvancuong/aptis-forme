<script setup>
import { computed } from 'vue';
import { buildAnswerRows } from '../composables/answerDisplay';

// Bảng so sánh "Bạn chọn" vs "Đáp án" theo NỘI DUNG (không phải số index).
const props = defineProps({
    question: Object,
    answer: null,
    answerKey: Object,
    showYours: { type: Boolean, default: true },
});

const rows = computed(() => buildAnswerRows(props.question, props.answer, props.answerKey));
</script>

<template>
    <div v-if="rows && rows.length" class="space-y-2">
        <div v-for="(r, i) in rows" :key="i" class="rounded-lg bg-white/70 p-2.5 text-sm ring-1 ring-slate-200">
            <div v-if="r.label" class="mb-1 flex items-start justify-between gap-2">
                <span class="font-medium text-slate-700">{{ r.label }}</span>
                <span class="flex-shrink-0 text-xs font-semibold" :class="r.ok ? 'text-emerald-600' : 'text-red-600'">{{ r.ok ? '✓' : '✗' }}</span>
            </div>
            <div v-if="showYours && !r.ok" class="flex gap-2">
                <span class="w-20 flex-shrink-0 text-slate-400">Bạn chọn:</span>
                <span class="text-red-600" :class="!r.yours && 'italic text-slate-400'">{{ r.yours || '(bỏ trống)' }}</span>
            </div>
            <div class="flex gap-2">
                <span class="w-20 flex-shrink-0 text-slate-400">Đáp án:</span>
                <span class="font-medium text-emerald-700">{{ r.correct }}</span>
            </div>
        </div>
    </div>
</template>
