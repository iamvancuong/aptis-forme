<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useAntiCopy } from '../composables/antiCopy';
import BrandLogo from '../components/BrandLogo.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const menuOpen = ref(false);
const userInitial = computed(() => (user.value?.name || '?').trim().charAt(0).toUpperCase());

useAntiCopy(() => page.props.auth?.user?.role === 'admin');
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
                        <BrandLogo :size="32" />
                        <span class="font-extrabold tracking-tight text-slate-900">nhai<span class="text-slate-400">aptis</span></span>
                    </Link>
                    <nav class="hidden gap-1 text-sm sm:flex">
                        <Link v-for="n in nav" :key="n.href" :href="n.href"
                              class="rounded-lg px-3 py-1.5"
                              :class="active(n.href) ? 'bg-brand-50 text-brand-700 font-medium' : 'text-slate-600 hover:text-brand-600'">
                            {{ n.label }}
                        </Link>
                    </nav>
                </div>
                <!-- Menu tài khoản -->
                <div class="relative">
                    <button @click="menuOpen = !menuOpen"
                            class="flex items-center gap-2 rounded-full py-1 pl-1 pr-2 hover:bg-slate-100">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-brand-600 to-violet-600 text-sm font-bold text-white">{{ userInitial }}</span>
                        <span class="hidden max-w-[120px] truncate text-sm font-medium text-slate-700 sm:inline">{{ user?.name }}</span>
                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                    </button>

                    <!-- backdrop -->
                    <div v-if="menuOpen" @click="menuOpen = false" class="fixed inset-0 z-30"></div>

                    <!-- dropdown -->
                    <div v-if="menuOpen" class="absolute right-0 z-40 mt-2 w-56 overflow-hidden rounded-2xl bg-white py-1 shadow-lg ring-1 ring-slate-200">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <div class="text-sm font-semibold text-slate-900">{{ user?.name }}</div>
                            <div class="truncate text-xs text-slate-400">{{ user?.email }}</div>
                        </div>
                        <a v-if="user?.role === 'admin'" href="/admin"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <span>🛠️</span> Trang quản trị
                        </a>
                        <Link href="/doi-mat-khau" @click="menuOpen = false"
                              class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <span>🔑</span> Đổi mật khẩu
                        </Link>
                        <button @click="logout"
                                class="flex w-full items-center gap-2 border-t border-slate-100 px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50">
                            <span>↩</span> Đăng xuất
                        </button>
                    </div>
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
