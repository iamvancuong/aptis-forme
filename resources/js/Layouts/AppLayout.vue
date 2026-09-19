<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flashSuccess = computed(() => page.props.flash?.success);
const flashWarning = computed(() => page.props.flash?.warning);
const url = computed(() => page.url);

const nav = [
    { label: 'Luyện tập', href: '/dashboard' },
    { label: 'Xếp hạng', href: '/leaderboard' },
    { label: 'Lịch sử', href: '/history' },
    { label: 'Hướng dẫn', href: '/instructions' },
];
function active(href) {
    return href === '/dashboard' ? url.value === '/dashboard' : url.value.startsWith(href);
}
function logout() {
    router.post('/logout');
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-5xl px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <Link href="/dashboard" class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-brand-600 to-violet-600 text-sm font-bold text-white">A</span>
                        <span class="font-bold text-slate-900">APTIS V2</span>
                    </Link>
                    <nav class="hidden gap-1 text-sm sm:flex">
                        <Link v-for="n in nav" :key="n.href" :href="n.href"
                              class="rounded-lg px-3 py-1.5"
                              :class="active(n.href) ? 'bg-brand-50 text-brand-700 font-medium' : 'text-slate-600 hover:text-brand-600'">
                            {{ n.label }}
                        </Link>
                    </nav>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <a v-if="user?.role === 'admin'" href="/admin"
                       class="rounded-lg bg-slate-900 px-3 py-1.5 font-medium text-white hover:bg-slate-800">Admin</a>
                    <span class="hidden text-slate-500 sm:inline">Xin chào, <b class="text-slate-800">{{ user?.name }}</b></span>
                    <button @click="logout" class="rounded-lg px-3 py-1.5 text-slate-500 hover:bg-slate-100 hover:text-red-600">Đăng xuất</button>
                </div>
            </div>
            <!-- Nav mobile -->
            <nav class="flex gap-1 overflow-x-auto px-4 pb-2 text-sm sm:hidden">
                <Link v-for="n in nav" :key="n.href" :href="n.href"
                      class="whitespace-nowrap rounded-lg px-3 py-1.5"
                      :class="active(n.href) ? 'bg-brand-50 text-brand-700 font-medium' : 'text-slate-600'">
                    {{ n.label }}
                </Link>
            </nav>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-8">
            <div v-if="flashSuccess" class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200">{{ flashSuccess }}</div>
            <div v-if="flashWarning" class="mb-6 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800 ring-1 ring-amber-200">{{ flashWarning }}</div>
            <slot />
        </main>
    </div>
</template>
