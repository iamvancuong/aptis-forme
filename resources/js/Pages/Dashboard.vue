<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    skills: Array,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flashSuccess = computed(() => page.props.flash?.success);
const flashWarning = computed(() => page.props.flash?.warning);

function logout() {
    router.post('/logout');
}
</script>

<template>
    <Head title="Trang chủ" />

    <div class="min-h-screen bg-slate-50">
        <header class="bg-white ring-1 ring-slate-200">
            <div class="mx-auto max-w-5xl px-6 py-4 flex items-center justify-between">
                <div class="font-bold text-slate-900">APTIS V2</div>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-slate-600">Xin chào, <b>{{ user?.name }}</b></span>
                    <button @click="logout" class="text-slate-500 hover:text-red-600">Đăng xuất</button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-10">
            <div v-if="flashSuccess" class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200">
                {{ flashSuccess }}
            </div>
            <div v-if="flashWarning" class="mb-6 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800 ring-1 ring-amber-200">
                {{ flashWarning }}
            </div>

            <h1 class="text-2xl font-bold text-slate-900">Luyện tập theo kỹ năng</h1>
            <p class="mt-1 text-slate-500">Nội dung bài học đồng bộ trực tiếp từ hệ thống (db1).</p>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="s in skills"
                    :key="s.skill + s.part"
                    class="rounded-2xl bg-white p-5 ring-1 ring-slate-200"
                >
                    <div class="text-xs font-semibold uppercase tracking-wide text-indigo-600">
                        {{ s.skill }} · Part {{ s.part }}
                    </div>
                    <div class="mt-1 text-lg font-semibold text-slate-900">{{ s.sets_count }} bộ đề</div>
                    <Link
                        v-if="s.first_set_id"
                        :href="`/practice/${s.first_set_id}`"
                        class="mt-3 inline-block rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Làm thử bộ đầu →
                    </Link>
                </div>
            </div>
        </main>
    </div>
</template>
