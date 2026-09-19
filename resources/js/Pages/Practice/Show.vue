<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';
import QuestionCard from '../../components/QuestionCard.vue';

const props = defineProps({
    set: Object,
    questions: Array,
});

// Khởi tạo answer đúng shape mà GradingService mong đợi cho từng dạng.
const answers = reactive({});
for (const q of props.questions) {
    const m = q.metadata || {};
    if (q.type === 'fill_in_blanks_mc') {
        answers[q.id] = (m.paragraphs || []).map(() => null);
    } else if (q.type === 'sentence_ordering') {
        answers[q.id] = (m.sentences || []).slice(1); // thứ tự hiện tại (đã xáo ở server)
    } else if ((m.pairs && m.dropdown_pool) || (m.items && m.choices)) {
        answers[q.id] = {};
    } else if (['writing', 'speaking'].includes(q.skill)) {
        answers[q.id] = null;
    } else {
        answers[q.id] = ''; // choice đơn
    }
}

const startedAt = Date.now();
const form = useForm({ answers, duration_seconds: 0 });

function submit() {
    form.duration_seconds = Math.round((Date.now() - startedAt) / 1000);
    form.answers = answers;
    form.post(`/practice/${props.set.id}/attempt`);
}
</script>

<template>
    <Head :title="`Làm bài — ${set.title}`" />

    <div class="min-h-screen bg-slate-50">
        <header class="bg-white ring-1 ring-slate-200">
            <div class="mx-auto max-w-3xl px-6 py-4 flex items-center justify-between">
                <a href="/dashboard" class="text-sm text-slate-500 hover:text-slate-800">← Trang chủ</a>
                <div class="text-xs font-semibold uppercase tracking-wide text-brand-600">
                    {{ set.skill }} · Part {{ set.part }}
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-6 py-8">
            <h1 class="text-xl font-bold text-slate-900">{{ set.title }}</h1>

            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <QuestionCard
                    v-for="(q, i) in questions"
                    :key="q.id"
                    :question="q"
                    :index="i"
                    v-model:answer="answers[q.id]"
                />

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-60"
                >
                    {{ form.processing ? 'Đang nộp…' : 'Nộp bài' }}
                </button>
            </form>
        </main>
    </div>
</template>
