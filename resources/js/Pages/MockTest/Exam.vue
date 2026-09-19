<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import QuestionCard from '../../components/QuestionCard.vue';

const props = defineProps({
    mockTest: Object,
    sections: Array,
});

// Lưu answer theo cấu trúc chấm: answers[sectionIndex][questionId]
const answers = reactive({});
// Danh sách phẳng để "next/next" xuyên suốt các part
const flat = [];
for (const s of props.sections) {
    answers[s.index] = {};
    for (const q of s.questions) {
        const m = q.metadata || {};
        if (q.type === 'fill_in_blanks_mc') answers[s.index][q.id] = (m.paragraphs || []).map(() => null);
        else if (q.type === 'sentence_ordering') answers[s.index][q.id] = (m.sentences || []).slice(1);
        else if ((m.pairs && m.dropdown_pool) || (m.items && m.choices)) answers[s.index][q.id] = {};
        else answers[s.index][q.id] = '';
        flat.push({ sectionIndex: s.index, part: s.part, question: q });
    }
}

const total = flat.length;
const current = ref(0);
const submitting = ref(false);
const cur = computed(() => flat[current.value]);
const progress = computed(() => Math.round(((current.value + 1) / total) * 100));
const isLast = computed(() => current.value === total - 1);
function go(i) { if (i >= 0 && i < total) current.value = i; }

// Đồng hồ đếm ngược
const deadline = new Date(props.mockTest.started_at).getTime() + props.mockTest.duration_minutes * 60000;
const remaining = ref(Math.max(0, Math.floor((deadline - Date.now()) / 1000)));
let timer;
const mmss = computed(() => {
    const m = Math.floor(remaining.value / 60), s = remaining.value % 60;
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
});
onMounted(() => {
    timer = setInterval(() => {
        remaining.value = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
        if (remaining.value <= 0) submit();
    }, 1000);
});
onBeforeUnmount(() => clearInterval(timer));

function answered(item) {
    const a = answers[item.sectionIndex][item.question.id];
    if (a === '' || a === null || a === undefined) return false;
    if (Array.isArray(a)) return a.some((x) => x !== null && x !== '');
    if (typeof a === 'object') return Object.values(a).some((x) => x !== null && x !== '');
    return true;
}

function submit() {
    if (submitting.value) return;
    submitting.value = true;
    clearInterval(timer);
    router.post(`/mock-test/${props.mockTest.id}/submit`, { answers }, {
        onFinish: () => (submitting.value = false),
    });
}
</script>

<template>
    <Head title="Đang thi thử" />
    <div class="min-h-screen bg-slate-50">
        <header class="sticky top-0 z-20 border-b border-slate-200/70 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-2xl px-6 py-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-900">Thi thử · {{ mockTest.skill }}</span>
                    <span class="rounded-lg bg-slate-900 px-3 py-1 font-mono text-sm text-white" :class="remaining < 60 && 'bg-red-600'">⏱ {{ mmss }}</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-semibold uppercase tracking-wide text-brand-600">Part {{ cur.part }}</span>
                    <span>Câu {{ current + 1 }}/{{ total }}</span>
                </div>
                <div class="mt-1.5 h-1.5 rounded-full bg-slate-100">
                    <div class="h-1.5 rounded-full bg-brand-500 transition-all" :style="{ width: progress + '%' }"></div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-2xl px-6 py-8">
            <QuestionCard
                :key="cur.question.id"
                :question="cur.question"
                :index="current"
                v-model:answer="answers[cur.sectionIndex][cur.question.id]"
            />

            <div class="mt-6 flex items-center justify-between gap-3">
                <button @click="go(current - 1)" :disabled="current === 0"
                        class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 disabled:opacity-40">← Trước</button>
                <button v-if="!isLast" @click="go(current + 1)"
                        class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Tiếp →</button>
                <button v-else @click="submit" :disabled="submitting"
                        class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-60">
                    {{ submitting ? 'Đang nộp…' : 'Nộp bài' }}
                </button>
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-2">
                <button v-for="(item, i) in flat" :key="i" @click="go(i)"
                        class="grid h-8 w-8 place-items-center rounded-lg text-xs font-medium ring-1 transition"
                        :class="i === current
                            ? 'bg-brand-600 text-white ring-brand-600'
                            : answered(item) ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-white text-slate-500 ring-slate-200 hover:ring-brand-300'">
                    {{ i + 1 }}
                </button>
            </div>

            <div class="mt-6 text-center">
                <button @click="submit" :disabled="submitting" class="text-sm text-slate-400 hover:text-red-600">Nộp bài sớm</button>
            </div>
        </main>
    </div>
</template>
