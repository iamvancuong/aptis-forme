<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    items: Array,       // [{ label, group?, answered }]
    current: Number,
});
const emit = defineEmits(['jump']);

const open = ref(false);
const q = ref('');

const filtered = computed(() => {
    const kw = q.value.trim().toLowerCase();
    return props.items
        .map((it, i) => ({ ...it, index: i }))
        .filter((it) => !kw
            || String(it.index + 1) === kw
            || (it.label || '').toLowerCase().includes(kw)
            || (it.group || '').toLowerCase().includes(kw));
});

function jump(i) {
    emit('jump', i);
    open.value = false;
    q.value = '';
}
</script>

<template>
    <div>
        <button type="button" @click="open = true"
                class="flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-600 ring-1 ring-slate-200 hover:ring-brand-300">
            <span>☰</span> Danh sách câu
        </button>

        <!-- Overlay panel -->
        <Teleport to="body">
            <div v-if="open" class="fixed inset-0 z-50 flex">
                <div class="flex-1 bg-slate-900/30" @click="open = false"></div>
                <aside class="flex h-full w-full max-w-sm flex-col bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                        <h3 class="font-semibold text-slate-900">Danh sách câu hỏi</h3>
                        <button @click="open = false" class="text-slate-400 hover:text-slate-700">✕</button>
                    </div>
                    <div class="border-b border-slate-100 p-3">
                        <input v-model="q" type="search" placeholder="Tìm theo số câu hoặc nội dung…"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none" />
                    </div>
                    <div class="flex-1 overflow-y-auto p-2">
                        <button
                            v-for="it in filtered"
                            :key="it.index"
                            @click="jump(it.index)"
                            class="flex w-full items-start gap-3 rounded-lg px-3 py-2 text-left hover:bg-slate-50"
                            :class="it.index === current && 'bg-brand-50'"
                        >
                            <span class="mt-0.5 grid h-7 w-7 flex-shrink-0 place-items-center rounded-lg text-xs font-medium ring-1"
                                  :class="it.index === current
                                      ? 'bg-brand-600 text-white ring-brand-600'
                                      : it.answered ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-white text-slate-500 ring-slate-200'">
                                {{ it.index + 1 }}
                            </span>
                            <span class="min-w-0">
                                <span v-if="it.group" class="block text-[11px] font-semibold uppercase tracking-wide text-brand-600">{{ it.group }}</span>
                                <span class="line-clamp-2 text-sm text-slate-700">{{ it.label || ('Câu ' + (it.index + 1)) }}</span>
                            </span>
                        </button>
                        <p v-if="filtered.length === 0" class="py-6 text-center text-sm text-slate-400">Không tìm thấy câu phù hợp.</p>
                    </div>
                </aside>
            </div>
        </Teleport>
    </div>
</template>
