<script setup>
import { computed } from 'vue';

const props = defineProps({
    question: Object,
    index: Number,
});

// answer: giá trị nộp lên (shape khớp GradingService theo từng dạng)
const answer = defineModel('answer');

const meta = computed(() => props.question.metadata || {});

// ── sentence_ordering: đổi thứ tự các câu (bỏ câu mở đầu cố định) ──
const orderable = computed(() => (meta.value.sentences || []).slice(1));
function move(list, from, to) {
    if (to < 0 || to >= list.length) return;
    const arr = [...list];
    const [x] = arr.splice(from, 1);
    arr.splice(to, 0, x);
    answer.value = arr;
}
const orderingList = computed(() => Array.isArray(answer.value) && answer.value.length ? answer.value : orderable.value);
</script>

<template>
    <section class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
        <div class="text-sm font-semibold text-slate-700">Câu {{ index + 1 }}. {{ question.stem }}</div>

        <!-- listening: mô tả/đề (audio 404 ở local là bình thường) -->
        <div v-if="question.audio_url" class="mt-3">
            <audio :src="question.audio_url" controls class="w-full"></audio>
        </div>

        <!-- grammar mcq3 / choice đơn: options [{id,text}] -->
        <div v-if="question.type === 'mcq3'" class="mt-3 space-y-2">
            <label v-for="opt in meta.options" :key="opt.id"
                   class="flex items-center gap-2 rounded-lg border px-3 py-2 text-sm cursor-pointer"
                   :class="answer === opt.id ? 'border-brand-500 bg-brand-50' : 'border-slate-200'">
                <input type="radio" :value="opt.id" v-model="answer" class="text-brand-600">
                <span><b>{{ opt.id }}.</b> {{ opt.text }}</span>
            </label>
        </div>

        <!-- listening_mcq: choices[] chọn theo index -->
        <div v-else-if="question.type === 'listening_mcq'" class="mt-3">
            <div v-if="meta.description" class="mb-3 rounded-lg bg-slate-50 p-3 text-sm text-slate-600" v-html="meta.description"></div>
            <div class="space-y-2">
                <label v-for="(c, i) in meta.choices" :key="i"
                       class="flex items-center gap-2 rounded-lg border px-3 py-2 text-sm cursor-pointer"
                       :class="answer === String(i) ? 'border-brand-500 bg-brand-50' : 'border-slate-200'">
                    <input type="radio" :value="String(i)" v-model="answer" class="text-brand-600">
                    <span>{{ c }}</span>
                </label>
            </div>
        </div>

        <!-- fill_in_blanks_mc: đoạn có [BLANK] + dropdown -->
        <div v-else-if="question.type === 'fill_in_blanks_mc'" class="mt-3 space-y-3">
            <div v-for="(para, bi) in meta.paragraphs" :key="bi" class="flex flex-wrap items-center gap-2 text-sm text-slate-800">
                <span>{{ para.split('[BLANK]')[0] }}</span>
                <select :value="(answer || [])[bi] ?? null"
                        @change="e => { const a = Array.isArray(answer) ? [...answer] : []; a[bi] = e.target.value; answer = a; }"
                        class="rounded-md border border-slate-300 px-2 py-1 text-sm">
                    <option :value="null" disabled>— chọn —</option>
                    <option v-for="(opt, oi) in meta.choices[bi]" :key="oi" :value="String(oi)">{{ opt }}</option>
                </select>
                <span>{{ para.split('[BLANK]')[1] }}</span>
            </div>
        </div>

        <!-- vocab match: pairs + dropdown_pool → {id: word} -->
        <div v-else-if="meta.pairs && meta.dropdown_pool" class="mt-3 space-y-2">
            <div v-for="p in meta.pairs" :key="p.id" class="flex items-center gap-3 text-sm">
                <span class="w-40 font-medium text-slate-800">{{ p.prompt }}</span>
                <span class="text-slate-400">{{ meta.connector || '=' }}</span>
                <select :value="(answer || {})[p.id] ?? ''"
                        @change="e => { answer = { ...(answer||{}), [p.id]: e.target.value }; }"
                        class="rounded-md border border-slate-300 px-2 py-1 text-sm">
                    <option value="" disabled>— chọn —</option>
                    <option v-for="(w, wi) in meta.dropdown_pool" :key="wi" :value="w">{{ w }}</option>
                </select>
            </div>
        </div>

        <!-- matching (items + choices) → {idx: choiceIndex} : listening 2/3/4, reading 3/4 -->
        <div v-else-if="meta.items && meta.choices" class="mt-3 space-y-2">
            <div v-for="(it, ii) in meta.items" :key="ii" class="flex items-center gap-3 text-sm">
                <span class="w-40 font-medium text-slate-800">{{ it }}</span>
                <select :value="(answer || {})[ii] ?? ''"
                        @change="e => { answer = { ...(answer||{}), [ii]: e.target.value }; }"
                        class="rounded-md border border-slate-300 px-2 py-1 text-sm">
                    <option value="" disabled>— chọn —</option>
                    <option v-for="(c, ci) in meta.choices" :key="ci" :value="String(ci)">{{ c }}</option>
                </select>
            </div>
        </div>

        <!-- reading sentence_ordering: sắp thứ tự -->
        <div v-else-if="question.type === 'sentence_ordering'" class="mt-3 space-y-2">
            <div v-if="meta.sentences?.[0]" class="rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-500">
                (Mở đầu) {{ meta.sentences[0] }}
            </div>
            <div v-for="(s, si) in orderingList" :key="si" class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                <span class="flex-1">{{ s }}</span>
                <button type="button" @click="move(orderingList, si, si-1)" class="rounded bg-slate-100 px-2 py-0.5 hover:bg-slate-200">↑</button>
                <button type="button" @click="move(orderingList, si, si+1)" class="rounded bg-slate-100 px-2 py-0.5 hover:bg-slate-200">↓</button>
            </div>
        </div>

        <!-- writing / speaking: Pha 4 (AI) -->
        <div v-else-if="['writing','speaking'].includes(question.skill)" class="mt-3 rounded-lg bg-brand-50 px-3 py-2 text-xs text-brand-700">
            Phần {{ question.skill }} sẽ được chấm bằng AI (đang phát triển ở pha sau).
        </div>

        <!-- fallback -->
        <div v-else class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">
            Dạng câu "{{ question.type }}" sẽ được hỗ trợ đầy đủ ở bản cập nhật tới.
        </div>
    </section>
</template>
