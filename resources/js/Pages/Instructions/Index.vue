<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({ instructions: Array });
</script>

<template>
    <Head title="Hướng dẫn" />
    <AppLayout>
        <h1 class="text-2xl font-bold text-slate-900">Hướng dẫn</h1>
        <div class="mt-6 grid gap-3">
            <Link v-for="ins in instructions" :key="ins.slug" :href="`/instructions/${ins.slug}`"
                  class="flex items-center justify-between rounded-xl bg-white p-4 ring-1 ring-slate-200 hover:ring-brand-300">
                <span class="font-medium text-slate-900">{{ ins.title }}</span>
                <span class="text-brand-600">→</span>
            </Link>
            <div v-if="instructions.length === 0" class="rounded-xl bg-white p-8 text-center text-slate-400 ring-1 ring-slate-200">
                Chưa có bài hướng dẫn.
            </div>
        </div>
    </AppLayout>
</template>
