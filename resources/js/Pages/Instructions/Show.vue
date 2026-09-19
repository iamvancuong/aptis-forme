<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({ instruction: Object });
</script>

<template>
    <Head :title="instruction.title" />
    <div class="min-h-screen bg-slate-50">
        <header class="bg-white ring-1 ring-slate-200">
            <div class="mx-auto max-w-3xl px-6 py-4 flex items-center justify-between">
                <a href="/instructions" class="text-sm text-slate-500 hover:text-slate-800">← Hướng dẫn</a>
            </div>
        </header>
        <main class="mx-auto max-w-3xl px-6 py-8">
            <article class="rounded-2xl bg-white p-6 ring-1 ring-slate-200">
                <h1 class="text-2xl font-bold text-slate-900">{{ instruction.title }}</h1>
                <div v-if="instruction.video_url" class="mt-4 aspect-video">
                    <iframe :src="instruction.video_url" class="h-full w-full rounded-lg" allowfullscreen></iframe>
                </div>
                <div class="prose prose-slate mt-4 max-w-none" v-html="instruction.content"></div>
            </article>
        </main>
    </div>
</template>
