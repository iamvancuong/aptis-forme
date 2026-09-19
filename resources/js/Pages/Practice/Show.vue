<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    set: Object,
    questions: Array,
});

// answers[questionId] = [chỉ số lựa chọn theo từng chỗ trống]
const answers = reactive({});
props.questions.forEach((q) => {
    if (q.type === 'fill_in_blanks_mc') {
        answers[q.id] = (q.metadata.paragraphs || []).map(() => null);
    } else {
        answers[q.id] = null;
    }
});

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
                <div class="text-xs font-semibold uppercase tracking-wide text-indigo-600">
                    {{ set.skill }} · Part {{ set.part }}
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-6 py-8">
            <h1 class="text-xl font-bold text-slate-900">{{ set.title }}</h1>

            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <section
                    v-for="(q, qi) in questions"
                    :key="q.id"
                    class="rounded-2xl bg-white p-5 ring-1 ring-slate-200"
                >
                    <div class="text-sm font-semibold text-slate-700">Câu {{ qi + 1 }}. {{ q.stem }}</div>

                    <!-- fill_in_blanks_mc: mỗi đoạn có [BLANK] → chọn 1 đáp án -->
                    <div v-if="q.type === 'fill_in_blanks_mc'" class="mt-3 space-y-3">
                        <div
                            v-for="(para, bi) in q.metadata.paragraphs"
                            :key="bi"
                            class="flex flex-wrap items-center gap-2 text-sm text-slate-800"
                        >
                            <span>{{ para.split('[BLANK]')[0] }}</span>
                            <select
                                v-model="answers[q.id][bi]"
                                class="rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-300 focus:outline-none"
                            >
                                <option :value="null" disabled>— chọn —</option>
                                <option
                                    v-for="(opt, oi) in q.metadata.choices[bi]"
                                    :key="oi"
                                    :value="String(oi)"
                                >{{ opt }}</option>
                            </select>
                            <span>{{ para.split('[BLANK]')[1] }}</span>
                        </div>
                    </div>

                    <!-- Type khác: Pha 1 chưa hỗ trợ render -->
                    <div v-else class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">
                        Dạng câu "{{ q.type }}" sẽ được hỗ trợ ở pha sau.
                    </div>
                </section>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
                >
                    {{ form.processing ? 'Đang nộp…' : 'Nộp bài' }}
                </button>
            </form>
        </main>
    </div>
</template>
