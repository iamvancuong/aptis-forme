<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    appName: String,
    phase: String,
});
</script>

<template>
    <Head title="Trang chủ" />

    <main class="min-h-screen flex items-center justify-center px-6">
        <div class="max-w-xl w-full text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-indigo-600">
                {{ phase }}
            </p>
            <h1 class="mt-3 text-4xl font-bold text-slate-900">
                {{ appName }}
            </h1>
            <p class="mt-4 text-slate-600">
                Nền tảng đã dựng xong: Laravel 12 + Inertia + Vue 3 + Tailwind v4.
                Sẵn sàng cho các pha tiếp theo (nối DB v1, khu làm bài, bán khóa).
            </p>
            <div class="mt-8 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 ring-1 ring-emerald-200">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Inertia + Vue hoạt động
            </div>
        </div>
    </main>
</template>
