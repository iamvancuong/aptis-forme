<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';

const props = defineProps({ attempt: Object, items: Array });

const grading = ref(null);
function gradeAI(answerId, skill) {
    grading.value = answerId;
    const url = skill === 'speaking' ? `/ai/grade-speaking/${answerId}` : `/ai/grade-writing/${answerId}`;
    router.post(url, {}, {
        preserveScroll: true,
        onFinish: () => (grading.value = null),
    });
}

const onlyWrong = ref(false);
const correctCount = computed(() => props.items.filter((i) => i.is_correct === true).length);
const shown = computed(() => (onlyWrong.value ? props.items.filter((i) => i.is_correct === false) : props.items));

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
    <AppLayout>
        <Link href="/history" class="text-sm text-slate-500 hover:text-slate-800">← Lịch sử</Link>

        <div class="mt-4 rounded-2xl bg-white p-6 ring-1 ring-slate-200 text-center">
            <div class="text-sm text-slate-500">{{ attempt.set_title || 'Bài làm' }}</div>
            <div class="mt-1 text-4xl font-bold" :class="attempt.score >= 50 ? 'text-emerald-600' : 'text-amber-600'">
                {{ attempt.score !== null ? Math.round(attempt.score) + '%' : '—' }}
            </div>
            <div class="mt-1 text-sm text-slate-500">Đúng {{ correctCount }}/{{ items.length }} câu · {{ attempt.created_at }}</div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">Chi tiết từng câu</h2>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" v-model="onlyWrong" class="rounded border-slate-300 text-brand-600" />
                Chỉ hiện câu sai
            </label>
        </div>

        <div class="mt-3 space-y-4">
            <div v-for="(it, i) in shown" :key="i" class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm font-semibold text-slate-700">{{ it.question?.stem }}</div>
                    <span v-if="it.is_correct === true" class="flex-shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700">Đúng</span>
                    <span v-else-if="it.is_correct === false" class="flex-shrink-0 rounded-full bg-red-50 px-2 py-0.5 text-xs text-red-700">Sai</span>
                    <span v-else class="flex-shrink-0 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Chờ chấm</span>
                </div>
                <dl class="mt-3 space-y-1 text-sm">
                    <div class="flex gap-2"><dt class="w-28 flex-shrink-0 text-slate-400">Bạn trả lời:</dt><dd class="whitespace-pre-wrap text-slate-800">{{ fmtAnswer(it.your_answer) }}</dd></div>
                    <div v-if="fmtKey(it.answer_key)" class="flex gap-2"><dt class="w-28 flex-shrink-0 text-slate-400">Đáp án:</dt><dd class="font-medium text-emerald-700">{{ fmtKey(it.answer_key) }}</dd></div>
                    <div v-if="it.answer_key?.explanation" class="flex gap-2"><dt class="w-28 flex-shrink-0 text-slate-400">Giải thích:</dt><dd class="text-slate-600" v-html="it.answer_key.explanation"></dd></div>
                </dl>

                <!-- Chấm AI Speaking -->
                <template v-if="it.skill === 'speaking'">
                    <button
                        v-if="!it.ai"
                        @click="gradeAI(it.answer_id, 'speaking')"
                        :disabled="grading === it.answer_id"
                        class="mt-3 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-60"
                    >{{ grading === it.answer_id ? 'AI đang chấm…' : '🎙 Chấm bằng AI' }}</button>
                    <div v-else class="mt-4 rounded-xl bg-brand-50 p-4 ring-1 ring-brand-200">
                        <div class="flex flex-wrap items-center gap-3 text-xs">
                            <span class="rounded-full bg-brand-600 px-2.5 py-1 font-semibold text-white">CEFR {{ it.ai.cefr_level }}</span>
                            <span class="rounded-full bg-white px-2.5 py-1 font-medium text-slate-700 ring-1 ring-slate-200">Tổng: <b class="text-brand-700">{{ it.ai.overall_score_10 }}/10</b></span>
                            <span v-for="(v, k) in it.ai.scores" :key="k" class="rounded-full bg-white px-2.5 py-1 text-slate-600 ring-1 ring-slate-200">{{ k }}: {{ v }}/5</span>
                        </div>
                        <dl class="mt-3 space-y-1 text-sm">
                            <div v-for="(v, k) in it.ai.feedback" :key="k"><dt class="inline font-medium text-slate-600">{{ k }}:</dt> <dd class="inline text-slate-700">{{ v }}</dd></div>
                        </dl>
                        <p v-if="it.ai.improved_sample" class="mt-3 rounded-lg bg-white p-3 text-sm text-emerald-700 ring-1 ring-slate-200"><b>Mẫu hay hơn:</b> {{ it.ai.improved_sample }}</p>
                    </div>
                </template>

                <!-- Chấm AI Writing -->
                <template v-if="it.skill === 'writing'">
                    <button
                        v-if="!it.ai"
                        @click="gradeAI(it.answer_id, 'writing')"
                        :disabled="grading === it.answer_id"
                        class="mt-3 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-60"
                    >{{ grading === it.answer_id ? 'AI đang chấm…' : '🤖 Chấm bằng AI' }}</button>

                    <div v-else class="mt-4 rounded-xl bg-brand-50 p-4 ring-1 ring-brand-200">
                        <div class="flex flex-wrap gap-3 text-xs">
                            <span v-for="(v, k) in it.ai.scores" :key="k" class="rounded-full bg-white px-2.5 py-1 font-medium text-slate-700 ring-1 ring-slate-200">
                                {{ k }}: <b class="text-brand-700">{{ v }}/5</b>
                            </span>
                        </div>
                        <dl class="mt-3 space-y-1 text-sm">
                            <div v-for="(v, k) in it.ai.feedback" :key="k"><dt class="inline font-medium text-slate-600">{{ k }}:</dt> <dd class="inline text-slate-700">{{ v }}</dd></div>
                        </dl>
                        <div v-for="(pr, pi) in it.ai.part_responses" :key="pi" class="mt-3 rounded-lg bg-white p-3 ring-1 ring-slate-200">
                            <div class="text-xs font-semibold text-slate-500">{{ pr.label }}</div>
                            <p class="mt-1 text-sm text-emerald-700"><b>Gợi ý:</b> {{ pr.improved_sample }}</p>
                            <div v-for="(c, ci) in pr.detailed_corrections" :key="ci" class="mt-1 text-xs text-slate-600">
                                <span class="text-red-600 line-through">{{ c.original }}</span> → <span class="text-emerald-700">{{ c.corrected }}</span> — {{ c.explanation }}
                            </div>
                        </div>
                        <div v-if="it.ai.suggestions?.length" class="mt-3 text-sm text-slate-700">
                            <b>Nên cải thiện:</b>
                            <ul class="mt-1 list-disc pl-5">
                                <li v-for="(s, si) in it.ai.suggestions" :key="si">{{ s }}</li>
                            </ul>
                        </div>
                    </div>
                </template>
            </div>
            <div v-if="shown.length === 0" class="rounded-2xl bg-white p-8 text-center text-slate-400 ring-1 ring-slate-200">
                Không có câu nào để hiển thị. 🎉
            </div>
        </div>
    </AppLayout>
</template>
