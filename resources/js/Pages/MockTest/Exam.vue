<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import QuestionCard from '../../components/QuestionCard.vue';

const props = defineProps({
    mockTest: Object,
    sections: Array,
});

// answers[sectionIndex][questionId]
const answers = reactive({});
for (const s of props.sections) {
    answers[s.index] = {};
    for (const q of s.questions) {
        const m = q.metadata || {};
        if (q.type === 'fill_in_blanks_mc') answers[s.index][q.id] = (m.paragraphs || []).map(() => null);
        else if (q.type === 'sentence_ordering') answers[s.index][q.id] = (m.sentences || []).slice(1);
        else if ((m.pairs && m.dropdown_pool) || (m.items && m.choices)) answers[s.index][q.id] = {};
        else answers[s.index][q.id] = '';
    }
}

const current = ref(0);
const submitting = ref(false);

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
        <header class="sticky top-0 z-10 bg-white ring-1 ring-slate-200">
            <div class="mx-auto max-w-3xl px-6 py-3 flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-900">Thi thử · {{ mockTest.skill }}</span>
                <div class="flex items-center gap-4">
                    <span class="rounded-lg bg-slate-900 px-3 py-1 font-mono text-sm text-white" :class="remaining < 60 && 'bg-red-600'">⏱ {{ mmss }}</span>
                    <button @click="submit" :disabled="submitting" class="rounded-lg bg-brand-600 px-4 py-1.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-60">Nộp bài</button>
                </div>
            </div>
        </header>

        <div class="mx-auto max-w-3xl px-6 pt-4">
            <div class="flex flex-wrap gap-2">
                <button v-for="s in sections" :key="s.index" @click="current = s.index"
                        class="rounded-lg px-3 py-1.5 text-sm"
                        :class="current === s.index ? 'bg-brand-600 text-white' : 'bg-white ring-1 ring-slate-200 text-slate-600'">
                    Part {{ s.part }}
                </button>
            </div>
        </div>

        <main class="mx-auto max-w-3xl px-6 py-6">
            <div v-for="s in sections" v-show="current === s.index" :key="s.index" class="space-y-6">
                <QuestionCard
                    v-for="(q, i) in s.questions"
                    :key="q.id"
                    :question="q"
                    :index="i"
                    v-model:answer="answers[s.index][q.id]"
                />
                <div class="flex justify-between">
                    <button v-if="s.index > 0" @click="current--" class="rounded-lg bg-white px-4 py-2 text-sm ring-1 ring-slate-200">← Part trước</button>
                    <span></span>
                    <button v-if="s.index < sections.length - 1" @click="current++" class="rounded-lg bg-white px-4 py-2 text-sm ring-1 ring-slate-200">Part sau →</button>
                    <button v-else @click="submit" :disabled="submitting" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Nộp bài</button>
                </div>
            </div>
        </main>
    </div>
</template>
