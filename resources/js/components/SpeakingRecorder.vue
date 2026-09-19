<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';

const props = defineProps({ question: Object });
// answer = mảng File (mỗi sub-câu 1 file). Rỗng = chưa ghi.
const answer = defineModel('answer');

const meta = computed(() => props.question.metadata || {});
const subQuestions = computed(() => {
    const qs = meta.value.questions || [];
    return qs.map((q) => (typeof q === 'string' ? q : q.prompt));
});
const part = computed(() => props.question.part);

const state = ref('idle'); // idle | reading | prep | recording | saving | done
const timer = ref(0);
const subIndex = ref(0);

let mediaRecorder = null;
let stream = null;
let audioChunks = [];
let interval = null;
const blobs = [];

const introByPart = {
    1: 'Personal Information. Please answer the questions below. You will have 30 seconds for each question. ',
    2: 'Describe a Picture. Please describe the picture and answer the questions below. You will have 45 seconds for each response. ',
    3: 'Compare Two Pictures. Please compare the two pictures and answer the questions. You will have 45 seconds for each response. ',
    4: 'Extended Discussion. Please answer the questions. You have 1 minute to think and 2 minutes to talk. ',
};

const stateLabel = computed(() => ({
    idle: 'Sẵn sàng',
    reading: '🔊 Đang đọc đề…',
    prep: '🤔 Chuẩn bị…',
    recording: '🔴 Đang ghi âm',
    saving: '💾 Đang lưu…',
    done: '✅ Đã ghi xong',
}[state.value]));

function playTTS(text, onEnd) {
    if (!window.speechSynthesis) { onEnd(); return; }
    window.speechSynthesis.cancel();
    const u = new SpeechSynthesisUtterance(text);
    u.lang = 'en-US';
    u.rate = 1.0;
    u.onend = onEnd;
    u.onerror = onEnd;
    window.speechSynthesis.speak(u);
}

function beep() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        if (ctx.state === 'suspended') ctx.resume();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.value = 800;
        gain.gain.setValueAtTime(0.1, ctx.currentTime);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start();
        setTimeout(() => osc.stop(), 200);
    } catch (e) { /* ignore */ }
}

async function setup() {
    if (mediaRecorder) return true;
    try {
        stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.ondataavailable = (e) => { if (e.data.size > 0) audioChunks.push(e.data); };
        mediaRecorder.onstop = () => {
            const blob = new Blob(audioChunks, { type: 'audio/webm' });
            blobs.push(new File([blob], `speaking_${props.question.id}_${blobs.length}.webm`, { type: 'audio/webm' }));
            answer.value = [...blobs];
        };
        return true;
    } catch (e) {
        alert('Không truy cập được micro. Vui lòng cấp quyền và thử lại.');
        return false;
    }
}

function countdown(seconds, onDone) {
    timer.value = seconds;
    clearInterval(interval);
    interval = setInterval(() => {
        timer.value--;
        if (timer.value <= 0) { clearInterval(interval); onDone(); }
    }, 1000);
}

function beepAndRecord(seconds, onDone) {
    beep();
    setTimeout(() => {
        audioChunks = [];
        state.value = 'recording';
        try { if (mediaRecorder.state === 'inactive') mediaRecorder.start(); } catch (e) {}
        countdown(seconds, () => {
            try { if (mediaRecorder.state !== 'inactive') mediaRecorder.stop(); } catch (e) {}
            setTimeout(onDone, 500);
        });
    }, 600);
}

function runSub() {
    const intro = subIndex.value === 0 ? (introByPart[part.value] || '') : '';

    if (part.value === 4) {
        if (subIndex.value > 0) return;
        const text = intro + subQuestions.value.join('. ');
        state.value = 'reading';
        playTTS(text, () => {
            state.value = 'prep';
            countdown(61, () => beepAndRecord(121, finishAll));
        });
    } else {
        const text = intro + (subQuestions.value[subIndex.value] || '');
        state.value = 'reading';
        playTTS(text, () => {
            const rec = part.value === 1 ? 31 : 46;
            beepAndRecord(rec, () => {
                subIndex.value++;
                if (subIndex.value < subQuestions.value.length) runSub();
                else finishAll();
            });
        });
    }
}

function finishAll() {
    state.value = 'saving';
    stream?.getTracks().forEach((t) => t.stop());
    mediaRecorder = null;
    setTimeout(() => (state.value = 'done'), 800);
}

async function start() {
    blobs.length = 0;
    answer.value = [];
    subIndex.value = 0;
    if (! (await setup())) return;
    runSub();
}

function mmss(s) {
    return `${String(Math.floor(s / 60)).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;
}

onBeforeUnmount(() => {
    clearInterval(interval);
    window.speechSynthesis?.cancel();
    stream?.getTracks().forEach((t) => t.stop());
});
</script>

<template>
    <div class="mt-3">
        <div class="mb-3 space-y-1 text-sm text-slate-600">
            <div v-for="(sq, si) in subQuestions" :key="si"
                 :class="state !== 'idle' && state !== 'done' && si === subIndex && part !== 4 ? 'font-semibold text-brand-700' : ''">
                {{ si + 1 }}. {{ sq }}
            </div>
        </div>

        <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-700">{{ stateLabel }}</span>
                <span v-if="state === 'recording' || state === 'prep'" class="font-mono text-lg font-bold"
                      :class="state === 'recording' ? 'text-red-600' : 'text-amber-600'">{{ mmss(timer) }}</span>
            </div>

            <!-- Thanh trạng thái ghi âm -->
            <div v-if="state === 'recording'" class="mt-2 flex items-center gap-2 text-xs text-red-600">
                <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-red-600"></span>
                Đang ghi câu {{ part === 4 ? '(toàn bài)' : subIndex + 1 }}…
            </div>

            <button v-if="state === 'idle'" type="button" @click="start"
                    class="mt-3 w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                ▶ Bắt đầu (tự đọc đề & ghi âm)
            </button>

            <div v-else-if="state === 'done'" class="mt-3">
                <p class="text-sm text-emerald-600">Đã ghi xong {{ answer?.length || 0 }} câu trả lời.</p>
                <button type="button" @click="start" class="mt-2 rounded-lg bg-white px-3 py-1.5 text-sm text-slate-600 ring-1 ring-slate-300 hover:bg-slate-50">Ghi lại</button>
            </div>
            <p v-else class="mt-2 text-xs text-slate-400">Hệ thống đang tự động đọc đề và ghi âm — vui lòng làm theo hướng dẫn.</p>
        </div>
        <p class="mt-2 text-xs text-slate-400">Bài Nói sẽ được AI chấm sau khi nộp.</p>
    </div>
</template>
