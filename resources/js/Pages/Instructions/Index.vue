<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({ instructions: Array });
</script>

<template>
    <Head title="Hướng dẫn" />
    <div class="min-h-screen bg-slate-50">
        <header class="bg-white ring-1 ring-slate-200">
            <div class="mx-auto max-w-3xl px-6 py-4 flex items-center justify-between">
                <a href="/dashboard" class="text-sm text-slate-500 hover:text-slate-800">← Trang chủ</a>
                <span class="font-semibold text-slate-900">Hướng dẫn</span>
            </div>
        </header>
        <main class="mx-auto max-w-3xl px-6 py-8">
            <div class="grid gap-3">
                <Link v-for="ins in instructions" :key="ins.slug" :href="`/instructions/${ins.slug}`"
                      class="rounded-xl bg-white p-4 ring-1 ring-slate-200 hover:ring-indigo-300">
                    <span class="font-medium text-slate-900">{{ ins.title }}</span>
                </Link>
                <div v-if="instructions.length === 0" class="rounded-xl bg-white p-8 text-center text-slate-400 ring-1 ring-slate-200">
                    Chưa có bài hướng dẫn.
                </div>
            </div>
        </main>
    </div>
</template>
