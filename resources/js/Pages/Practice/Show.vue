<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import QuestionCard from '../../components/QuestionCard.vue';
import QuestionNav from '../../components/QuestionNav.vue';

const props = defineProps({
    set: Object,
    questions: Array,
});

// Khởi tạo answer đúng shape mà GradingService mong đợi cho từng dạng.
const answers = reactive({});
for (const q of props.questions) {
    const m = q.metadata || {};
    if (q.type === 'fill_in_blanks_mc') answers[q.id] = (m.paragraphs || []).map(() => null);
    else if (q.type === 'sentence_ordering') answers[q.id] = (m.sentences || []).slice(1);
    else if ((m.pairs && m.dropdown_pool) || (m.items && m.choices)) answers[q.id] = {};
    else if (q.skill === 'writing') {
        if (m.fields) answers[q.id] = m.fields.map(() => '');
        else if (m.questions) answers[q.id] = m.questions.map(() => '');
        else if (m.task1 || m.task2) answers[q.id] = { task1: '', task2: '' };
        else answers[q.id] = '';
    } else if (q.skill === 'speaking') answers[q.id] = [];
    else answers[q.id] = '';
}

const total = props.questions.length;
const current = ref(0);
const startedAt = Date.now();
const form = useForm({ answers, duration_seconds: 0 });

const currentQuestion = computed(() => props.questions[current.value]);
const progress = computed(() => Math.round(((current.value + 1) / total) * 100));
const isLast = computed(() => current.value === total - 1);

// Câu đã trả lời? (để tô chấm điều hướng)
function answered(qid) {
    const a = answers[qid];
    if (a === '' || a === null || a === undefined) return false;
    if (Array.isArray(a)) return a.some((x) => x !== null && x !== '');
    if (typeof a === 'object') return Object.values(a).some((x) => x !== null && x !== '');
    return true;
}

function go(i) {
    if (i >= 0 && i < total) current.value = i;
}

const navItems = computed(() => props.questions.map((q) => ({
    label: q.stem || q.title,
    answered: answered(q.id),
})));

function submit() {
    form.duration_seconds = Math.round((Date.now() - startedAt) / 1000);
    form.answers = answers;
    form.post(`/practice/${props.set.id}/attempt`);
}
</script>

<template>
    <Head :title="`Làm bài — ${set.title}`" />

    <div class="min-h-screen bg-slate-50">
        <header class="sticky top-0 z-20 border-b border-slate-200/70 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-2xl px-6 py-3">
                <div class="flex items-center justify-between">
                    <a href="/dashboard" class="text-sm text-slate-500 hover:text-slate-800">← Thoát</a>
                    <div class="text-xs font-semibold uppercase tracking-wide text-brand-600">{{ set.skill }} · Part {{ currentQuestion.part }}</div>
                    <span class="text-sm font-medium text-slate-700">Câu {{ current + 1 }}/{{ total }}</span>
                </div>
                <div class="mt-2 h-1.5 rounded-full bg-slate-100">
                    <div class="h-1.5 rounded-full bg-brand-500 transition-all" :style="{ width: progress + '%' }"></div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-2xl px-6 py-8">
            <p class="mb-4 text-sm font-semibold text-slate-500">{{ set.title }}</p>

            <!-- Chỉ hiện 1 câu -->
            <QuestionCard
                :key="currentQuestion.id"
                :question="currentQuestion"
                :index="current"
                v-model:answer="answers[currentQuestion.id]"
            />

            <!-- Điều hướng -->
            <div class="mt-6 flex items-center justify-between gap-3">
                <button
                    @click="go(current - 1)"
                    :disabled="current === 0"
                    class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 disabled:opacity-40"
                >← Trước</button>

                <QuestionNav :items="navItems" :current="current" @jump="go" />

                <button
                    v-if="!isLast"
                    @click="go(current + 1)"
                    class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700"
                >Tiếp →</button>
                <button
                    v-else
                    @click="submit"
                    :disabled="form.processing"
                    class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-60"
                >{{ form.processing ? 'Đang nộp…' : 'Nộp bài' }}</button>
            </div>

            <div class="mt-6 text-center">
                <button v-if="!isLast" @click="submit" :disabled="form.processing" class="text-xs text-slate-400 hover:text-red-600">Nộp bài sớm</button>
            </div>
        </main>
    </div>
</template>
