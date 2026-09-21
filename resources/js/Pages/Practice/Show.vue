<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, reactive, ref } from 'vue';
import QuestionCard from '../../components/QuestionCard.vue';
import QuestionNav from '../../components/QuestionNav.vue';
import AnswerReview from '../../components/AnswerReview.vue';
import { buildAnswerRows } from '../../composables/answerDisplay';
import { useAntiCopy } from '../../composables/antiCopy';

const page = usePage();
useAntiCopy(() => page.props.auth?.user?.role === 'admin');

const props = defineProps({
    set: Object,
    questions: Array,
    trial: { type: Boolean, default: false },
});

const checkUrl = computed(() => props.trial ? `/hoc-thu/${props.set.id}/check` : `/practice/${props.set.id}/check`);
const exitUrl = computed(() => props.trial ? '/' : '/dashboard');

// Khởi tạo answer đúng shape mà GradingService mong đợi cho từng dạng.
const answers = reactive({});
for (const q of props.questions) {
    const m = q.metadata || {};
    if (q.type === 'fill_in_blanks_mc') answers[q.id] = (m.paragraphs || []).map(() => null);
    else if (q.type === 'sentence_ordering') answers[q.id] = (m.sentences || []).slice(1);
    else if ((m.pairs && m.dropdown_pool) || (m.items && m.choices)
        || (m.statements && m.shared_choices)                       // Listening Part 3
        || q.type === 'text_question_match'                         // Reading Part 3
        || q.type === 'matching_headings'                          // Reading Part 4
        || (q.skill === 'listening' && m.questions)) answers[q.id] = {}; // Listening Part 4
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

// Bài Nói: chạy tự động (TTS → beep → ghi âm → tự chuyển câu). Cần 1 lần bấm
// "Bắt đầu" để trình duyệt cho phép mic + phát âm thanh.
const isSpeaking = computed(() => total > 0 && props.questions.every((q) => q.skill === 'speaking'));
const speakingStarted = ref(false);

function onSpeakingDone() {
    if (current.value < total - 1) {
        setTimeout(() => current.value++, 900); // nghỉ ngắn rồi câu sau tự chạy
    } else if (!props.trial) {
        setTimeout(() => submit(), 900); // câu cuối xong → tự nộp (không áp dụng học thử)
    }
}

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

// ── Kiểm tra tức thời (hiện đáp án ngay như v1) ──
const checked = reactive({}); // qid -> {is_correct, score, answer_key}
const checking = ref(false);
const currentChecked = computed(() => checked[currentQuestion.value.id]);
const isObjective = computed(() => !['writing', 'speaking'].includes(currentQuestion.value.skill));

async function checkCurrent() {
    const qid = currentQuestion.value.id;
    if (checked[qid] || checking.value) return;
    checking.value = true;
    try {
        const { data } = await axios.post(checkUrl.value, { question_id: qid, answer: answers[qid] });
        if (data.gradable) checked[qid] = data;
    } catch (e) {
        // im lặng
    } finally {
        checking.value = false;
    }
}

function fmtKey(k) {
    if (!k) return '';
    const val = k.correct_answers ?? k.correct_answer ?? k.correct_option ?? k.sentences;
    if (val === undefined || val === null) return '';
    if (Array.isArray(val)) return val.join(', ');
    if (typeof val === 'object') return Object.values(val).join(', ');
    return String(val);
}

const checkedRows = computed(() => currentChecked.value
    && buildAnswerRows(currentQuestion.value, answers[currentQuestion.value.id], currentChecked.value.answer_key)?.length);

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

    <div class="flex min-h-screen flex-col bg-slate-50">
        <header class="sticky top-0 z-20 border-b border-slate-200/70 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-2xl px-6 py-3">
                <div class="flex items-center justify-between">
                    <a :href="exitUrl" class="text-sm text-slate-500 hover:text-slate-800">← Thoát</a>
                    <div class="text-xs font-semibold uppercase tracking-wide text-brand-600">{{ set.skill }} · Part {{ currentQuestion.part }}</div>
                    <span class="text-sm font-medium text-slate-700">Câu {{ current + 1 }}/{{ total }}</span>
                </div>
                <div class="mt-2 h-1.5 rounded-full bg-slate-100">
                    <div class="h-1.5 rounded-full bg-brand-500 transition-all" :style="{ width: progress + '%' }"></div>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-2xl flex-1 px-6 py-8">
            <!-- Banner học thử -->
            <div v-if="trial" class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-gradient-to-r from-brand-600 to-violet-600 px-5 py-3 text-white">
                <span class="text-sm font-medium">🎓 Bạn đang học thử miễn phí kỹ năng này.</span>
                <a href="/register" class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 hover:bg-brand-50">Đăng ký học đầy đủ</a>
            </div>

            <p class="mb-4 text-sm font-semibold text-slate-500">{{ set.title }}</p>

            <!-- Bài Nói: màn bắt đầu (1 lần) -->
            <div v-if="isSpeaking && !speakingStarted" class="rounded-2xl bg-white p-8 text-center ring-1 ring-slate-200">
                <div class="text-4xl">🗣️</div>
                <h2 class="mt-3 text-xl font-bold text-slate-900">Bài thi Nói tự động</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                    Hệ thống sẽ <b>tự đọc đề</b>, phát tiếng <b>"beep"</b>, rồi <b>tự ghi âm</b> và
                    <b>tự chuyển câu</b> cho tới hết bài. Hãy đảm bảo micro đã bật và ở nơi yên tĩnh.
                </p>
                <button @click="speakingStarted = true"
                        class="mt-6 rounded-xl bg-brand-600 px-8 py-3 text-sm font-semibold text-white hover:bg-brand-700">
                    ▶ Bắt đầu
                </button>
            </div>

            <template v-else>
                <!-- Chỉ hiện 1 câu -->
                <QuestionCard
                    :key="currentQuestion.id"
                    :question="currentQuestion"
                    :index="current"
                    :autostart="isSpeaking && speakingStarted"
                    v-model:answer="answers[currentQuestion.id]"
                    @speaking-done="onSpeakingDone"
                />

                <!-- Kiểm tra tức thời + panel đáp án (như v1) -->
                <template v-if="!isSpeaking && isObjective">
                    <button
                        v-if="!currentChecked"
                        @click="checkCurrent"
                        :disabled="checking"
                        class="mt-4 w-full rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-900 disabled:opacity-60"
                    >{{ checking ? 'Đang kiểm tra…' : 'Kiểm tra đáp án' }}</button>

                    <div v-else class="mt-4 rounded-xl p-4 ring-1"
                         :class="currentChecked.is_correct ? 'bg-emerald-50 ring-emerald-200' : 'bg-red-50 ring-red-200'">
                        <div class="flex items-center gap-2 text-sm font-semibold"
                             :class="currentChecked.is_correct ? 'text-emerald-700' : 'text-red-700'">
                            <span>{{ currentChecked.is_correct ? '✓ Chính xác!' : '✗ Chưa đúng' }}</span>
                        </div>
                        <AnswerReview v-if="checkedRows" class="mt-3" :question="currentQuestion"
                                      :answer="answers[currentQuestion.id]" :answer-key="currentChecked.answer_key" />
                        <div v-else-if="fmtKey(currentChecked.answer_key)" class="mt-2 text-sm">
                            <span class="text-slate-500">Đáp án đúng:</span>
                            <span class="font-medium text-emerald-700">{{ fmtKey(currentChecked.answer_key) }}</span>
                        </div>
                        <div v-if="currentChecked.answer_key?.explanation" class="mt-2 text-sm text-slate-600" v-html="currentChecked.answer_key.explanation"></div>
                    </div>
                </template>

                <!-- Bài Nói: chạy tự động, không có nút điều hướng -->
                <div v-if="isSpeaking" class="mt-6 text-center text-sm text-slate-400">
                    Bài Nói đang chạy tự động — vui lòng nói khi có tín hiệu ghi âm.
                </div>

                <!-- Các kỹ năng khác: phần nội dung phụ (thanh điều hướng nằm ở footer cố định) -->
                <template v-else>
                    <div v-if="!trial" class="mt-6 text-center">
                        <button v-if="!isLast" @click="submit" :disabled="form.processing" class="text-xs text-slate-400 hover:text-red-600">Nộp bài sớm</button>
                    </div>
                    <div v-else class="mt-8 rounded-2xl bg-white p-6 text-center ring-1 ring-slate-200">
                        <p class="text-sm text-slate-600">Thích bài học? Đăng ký để luyện <b>không giới hạn</b>, thi thử full đề và được <b>AI chấm Writing/Speaking</b>.</p>
                        <a href="/register" class="mt-3 inline-block rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Đăng ký ngay →</a>
                    </div>
                </template>
            </template>
        </main>

        <!-- Thanh điều hướng CỐ ĐỊNH dưới đáy (không trôi theo nội dung) -->
        <footer v-if="!isSpeaking && !(isSpeaking && !speakingStarted)"
                class="sticky bottom-0 z-20 border-t border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-2xl items-center justify-between gap-3 px-6 py-3">
                <button @click="go(current - 1)" :disabled="current === 0"
                        class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 disabled:opacity-40">← Trước</button>
                <QuestionNav :items="navItems" :current="current" @jump="go" />
                <button v-if="!isLast" @click="go(current + 1)"
                        class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Tiếp →</button>
                <a v-else-if="trial" href="/register"
                   class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Đăng ký để tiếp tục</a>
                <button v-else @click="submit" :disabled="form.processing"
                        class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-60">
                    {{ form.processing ? 'Đang nộp…' : 'Nộp bài' }}
                </button>
            </div>
        </footer>
    </div>
</template>
