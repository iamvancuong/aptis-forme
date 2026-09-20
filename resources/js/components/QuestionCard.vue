<script setup>
import { computed } from 'vue';
import SpeakingRecorder from './SpeakingRecorder.vue';

const props = defineProps({
    question: Object,
    index: Number,
    autostart: { type: Boolean, default: false },
});
defineEmits(['speaking-done']);

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

// Nhãn chữ cái cho lựa chọn (A, B, C…) — Reading Part 3
const letter = (i) => String.fromCharCode(65 + i);
</script>

<template>
    <section class="rounded-2xl bg-white p-5 ring-1 ring-slate-200">
        <div class="text-sm font-semibold text-slate-700">Câu {{ index + 1 }}. {{ question.stem }}</div>

        <!-- listening: mô tả/đề (audio 404 ở local là bình thường) -->
        <div v-if="question.audio_url" class="mt-3">
            <audio :src="question.audio_url" controls class="w-full"></audio>
        </div>
        <!-- listening không có audio → báo nhỏ, đỏ, in nghiêng (trừ Part 2 dùng audio riêng theo người nói) -->
        <p v-else-if="question.skill === 'listening' && !(question.audio_urls && question.audio_urls.length)"
           class="mt-3 text-xs italic text-red-500">Không có audio</p>

        <!-- Ảnh đề (Speaking Part 2/4, Reading… — lấy từ metadata, /storage/) -->
        <div v-if="meta.image_path" class="mt-3">
            <img :src="`/storage/${meta.image_path}`" alt="Ảnh đề" class="mx-auto max-h-64 rounded-lg object-contain shadow-sm" />
        </div>
        <div v-if="meta.image_paths && meta.image_paths.length" class="mt-3 grid grid-cols-2 gap-3">
            <img v-for="(img, ii) in meta.image_paths" :key="ii" :src="`/storage/${img}`" :alt="`Ảnh ${ii + 1}`" class="max-h-56 rounded-lg object-contain shadow-sm" />
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

        <!-- listening_mcq: choices[] chọn theo index (gồm cả slug lạ 'listening-part-1') -->
        <div v-else-if="question.type === 'listening_mcq' || (question.skill === 'listening' && Number(question.part) === 1 && meta.choices)" class="mt-3">
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

        <!-- Reading Part 3 (text_question_match): đọc các đoạn A/B/C/D rồi gán mỗi câu hỏi vào 1 đoạn → {qi: optionIndex} -->
        <div v-else-if="question.type === 'text_question_match'" class="mt-3 space-y-4">
            <div class="space-y-3">
                <div v-for="(opt, oi) in meta.options" :key="oi" class="rounded-lg bg-slate-50 p-3 text-sm leading-relaxed text-slate-700">
                    <span class="mr-1 font-bold text-brand-600">{{ letter(oi) }}.</span>
                    <span>{{ opt }}</span>
                </div>
            </div>
            <div class="space-y-3 border-t border-slate-100 pt-3">
                <div v-for="(q, qi) in meta.questions" :key="qi"
                     class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3 text-sm">
                    <span class="flex-1 text-slate-800">{{ qi + 1 }}. {{ q }}</span>
                    <select :value="(answer || {})[qi] ?? ''"
                            @change="e => { answer = { ...(answer||{}), [qi]: e.target.value }; }"
                            class="rounded-md border border-slate-300 px-2 py-1 text-sm">
                        <option value="" disabled>— chọn —</option>
                        <option v-for="(opt, oi) in meta.options" :key="oi" :value="String(oi)">{{ letter(oi) }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Reading Part 4 (matching_headings): gán tiêu đề phù hợp cho mỗi đoạn → {pi: headingIndex} -->
        <div v-else-if="question.type === 'matching_headings'" class="mt-3 space-y-4">
            <div v-if="meta.headings" class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">
                <div class="mb-1 font-medium text-slate-700">Danh sách tiêu đề:</div>
                <ol class="list-inside list-decimal space-y-0.5">
                    <li v-for="(h, hi) in meta.headings" :key="hi">{{ h }}</li>
                </ol>
            </div>
            <div class="space-y-4">
                <div v-for="(para, pi) in meta.paragraphs" :key="pi" class="space-y-2">
                    <div class="text-sm leading-relaxed text-slate-800"><b class="text-brand-600">Đoạn {{ pi + 1 }}.</b> {{ para }}</div>
                    <select :value="(answer || {})[pi] ?? ''"
                            @change="e => { answer = { ...(answer||{}), [pi]: e.target.value }; }"
                            class="w-full rounded-md border border-slate-300 px-2 py-1 text-sm">
                        <option value="" disabled>— chọn tiêu đề —</option>
                        <option v-for="(h, hi) in meta.headings" :key="hi" :value="String(hi)">{{ hi + 1 }}. {{ h }}</option>
                    </select>
                </div>
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

        <!-- matching (items + choices) → {idx: choiceIndex} : listening 2, reading 3/4 -->
        <div v-else-if="meta.items && meta.choices" class="mt-3 space-y-3">
            <div v-for="(it, ii) in meta.items" :key="ii"
                 class="flex flex-col gap-2 rounded-lg border border-slate-100 p-2 sm:flex-row sm:items-center sm:gap-3 sm:border-0 sm:p-0 text-sm">
                <div class="flex-1">
                    <div class="font-medium text-slate-800">{{ it }}</div>
                    <!-- Listening Part 2: mỗi người nói có audio riêng -->
                    <audio v-if="question.audio_urls && question.audio_urls[ii]"
                           :src="question.audio_urls[ii]" controls class="mt-1 w-full max-w-xs"></audio>
                    <p v-else-if="question.skill === 'listening'" class="mt-1 text-xs italic text-red-500">Không có audio</p>
                </div>
                <select :value="(answer || {})[ii] ?? ''"
                        @change="e => { answer = { ...(answer||{}), [ii]: e.target.value }; }"
                        class="rounded-md border border-slate-300 px-2 py-1 text-sm">
                    <option value="" disabled>— chọn —</option>
                    <option v-for="(c, ci) in meta.choices" :key="ci" :value="String(ci)">{{ c }}</option>
                </select>
            </div>
        </div>

        <!-- Listening Part 3 (multi_matching): statements + shared_choices → {idx: choiceIndex} -->
        <div v-else-if="meta.statements && meta.shared_choices" class="mt-3 space-y-3">
            <div v-if="meta.topic" class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">{{ meta.topic }}</div>
            <div v-for="(st, si) in meta.statements" :key="si"
                 class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-3 text-sm">
                <span class="flex-1 text-slate-800">{{ si + 1 }}. {{ st }}</span>
                <select :value="(answer || {})[si] ?? ''"
                        @change="e => { answer = { ...(answer||{}), [si]: e.target.value }; }"
                        class="rounded-md border border-slate-300 px-2 py-1 text-sm">
                    <option value="" disabled>— chọn —</option>
                    <option v-for="(c, ci) in meta.shared_choices" :key="ci" :value="String(ci)">{{ c }}</option>
                </select>
            </div>
        </div>

        <!-- Listening Part 4 (single_choice): nhiều câu con, mỗi câu chọn 1 đáp án → {idx: choiceIndex} -->
        <div v-else-if="question.skill === 'listening' && meta.questions" class="mt-3 space-y-4">
            <div v-if="meta.topic" class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">{{ meta.topic }}</div>
            <div v-for="(sq, qi) in meta.questions" :key="qi" class="rounded-lg border border-slate-200 p-3">
                <div class="text-sm font-medium text-slate-800">{{ qi + 1 }}. {{ sq.question }}</div>
                <div class="mt-2 space-y-1.5">
                    <label v-for="(c, ci) in sq.choices" :key="ci"
                           class="flex items-center gap-2 rounded-lg border px-3 py-2 text-sm cursor-pointer"
                           :class="(answer || {})[qi] === String(ci) ? 'border-brand-500 bg-brand-50' : 'border-slate-200'">
                        <input type="radio" :value="String(ci)" :checked="(answer || {})[qi] === String(ci)"
                               @change="answer = { ...(answer||{}), [qi]: String(ci) }" class="text-brand-600">
                        <span>{{ c }}</span>
                    </label>
                </div>
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

        <!-- WRITING: nhập bài, chấm AI sau khi nộp -->
        <div v-else-if="question.skill === 'writing'" class="mt-3 space-y-3">
            <!-- Tiêu đề đề bài (như v1: "Art Club - Part 2") -->
            <div v-if="question.title" class="text-sm font-semibold text-brand-600">{{ question.title }}</div>

            <!-- Part 1: nhiều câu ngắn theo fields -->
            <template v-if="meta.fields">
                <div v-for="(f, fi) in meta.fields" :key="fi">
                    <label class="block text-sm text-slate-600">{{ fi + 1 }}. {{ f.label }}</label>
                    <input :value="(answer || [])[fi] ?? ''"
                           @input="e => { const a = Array.isArray(answer) ? [...answer] : []; a[fi] = e.target.value; answer = a; }"
                           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none" />
                </div>
            </template>
            <!-- Part 3: trả lời từng post -->
            <template v-else-if="meta.questions">
                <div v-for="(pq, pi) in meta.questions" :key="pi">
                    <label class="block text-sm font-medium text-slate-700">Bài {{ pi + 1 }}: {{ typeof pq === 'string' ? pq : pq.prompt }}</label>
                    <p v-if="pq.word_limit" class="mt-0.5 text-xs text-slate-400">Viết {{ pq.word_limit.min }}–{{ pq.word_limit.max }} từ</p>
                    <textarea :value="(answer || [])[pi] ?? ''" rows="3"
                              @input="e => { const a = Array.isArray(answer) ? [...answer] : []; a[pi] = e.target.value; answer = a; }"
                              class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none"></textarea>
                </div>
            </template>
            <!-- Part 4: đọc email → viết 2 email -->
            <template v-else-if="meta.task1 || meta.task2">
                <!-- Email nhận được (đề để đọc) -->
                <div v-if="meta.email || meta.context" class="rounded-xl bg-slate-50 p-4 text-sm ring-1 ring-slate-200">
                    <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-brand-600">✉️ Email nhận được</div>
                    <p v-if="meta.context" class="mb-2 italic text-slate-500">{{ meta.context }}</p>
                    <template v-if="meta.email">
                        <div v-if="meta.email.greeting" class="font-bold text-slate-800">{{ meta.email.greeting }}</div>
                        <div v-if="meta.email.body" class="mt-1 whitespace-pre-line leading-relaxed text-slate-700">{{ meta.email.body }}</div>
                        <div v-if="meta.email.sign_off" class="mt-2 whitespace-pre-line font-medium text-slate-800">{{ meta.email.sign_off }}</div>
                    </template>
                </div>
                <!-- Task 1 -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Task 1 — Email thân mật</label>
                    <p v-if="meta.task1 && meta.task1.instruction" class="mt-0.5 text-xs text-slate-500">{{ meta.task1.instruction }}</p>
                    <p v-if="meta.task1 && meta.task1.word_limit" class="text-xs text-slate-400">Khoảng {{ meta.task1.word_limit.min }}–{{ meta.task1.word_limit.max }} từ</p>
                    <textarea :value="(answer || {}).task1 ?? ''" rows="4"
                              @input="e => { answer = { ...(answer||{}), task1: e.target.value }; }"
                              class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none"></textarea>
                </div>
                <!-- Task 2 -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Task 2 — Email trang trọng</label>
                    <p v-if="meta.task2 && meta.task2.instruction" class="mt-0.5 text-xs text-slate-500">{{ meta.task2.instruction }}</p>
                    <p v-if="meta.task2 && meta.task2.word_limit" class="text-xs text-slate-400">Khoảng {{ meta.task2.word_limit.min }}–{{ meta.task2.word_limit.max }} từ</p>
                    <textarea :value="(answer || {}).task2 ?? ''" rows="6"
                              @input="e => { answer = { ...(answer||{}), task2: e.target.value }; }"
                              class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none"></textarea>
                </div>
            </template>
            <!-- Part 2: 1 đoạn -->
            <template v-else>
                <!-- Đề bài (scenario) + gợi ý -->
                <div v-if="meta.scenario || meta.hints" class="rounded-xl bg-slate-50 p-4 text-sm ring-1 ring-slate-200">
                    <p v-if="meta.scenario" class="text-slate-700">{{ meta.scenario }}</p>
                    <p v-if="meta.hints" class="mt-2 text-amber-700">💡 {{ meta.hints }}</p>
                </div>
                <p v-if="meta.word_limit" class="text-xs text-slate-400">Viết {{ meta.word_limit.min }}–{{ meta.word_limit.max }} từ</p>
                <textarea :value="answer || ''" rows="5" placeholder="Viết bài của bạn…"
                          @input="e => answer = e.target.value"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none"></textarea>
            </template>
            <p class="text-xs text-slate-400">Bài Viết sẽ được AI chấm sau khi nộp.</p>
        </div>

        <!-- SPEAKING: tự đọc đề + beep + tự ghi âm (như v1) -->
        <SpeakingRecorder v-else-if="question.skill === 'speaking'" :question="question" :autostart="autostart"
                          v-model:answer="answer" @done="$emit('speaking-done')" />

        <!-- fallback -->
        <div v-else class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">
            Dạng câu "{{ question.type }}" sẽ được hỗ trợ đầy đủ ở bản cập nhật tới.
        </div>
    </section>
</template>
