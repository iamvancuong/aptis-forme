<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import BrandLogo from '../components/BrandLogo.vue';

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const url = computed(() => page.url);
const mobileOpen = ref(false);

const nav = [
    { label: 'Tổng quan', href: '/admin' },
    { label: 'Học viên', href: '/admin/users' },
    { label: 'Đơn hàng', href: '/admin/orders' },
    { label: 'Mã KM', href: '/admin/promo-codes' },
];

function isActive(href) {
    return href === '/admin' ? url.value === '/admin' : url.value.startsWith(href);
}

function logout() {
    router.post('/logout');
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <header class="bg-slate-900 text-white">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="flex h-14 items-center justify-between gap-4">
                    <!-- Logo -->
                    <span class="flex shrink-0 items-center gap-2 font-bold whitespace-nowrap">
                        <BrandLogo :size="26" />
                        <span class="hidden sm:inline">nhaiaptis · Admin</span>
                        <span class="sm:hidden">Admin</span>
                    </span>

                    <!-- Desktop: menu + hành động -->
                    <div class="hidden items-center gap-4 lg:flex">
                        <nav class="flex gap-1 text-sm">
                            <Link
                                v-for="n in nav"
                                :key="n.href"
                                :href="n.href"
                                class="rounded-md px-3 py-1.5"
                                :class="isActive(n.href) ? 'bg-white/15 text-white' : 'text-slate-300 hover:text-white'"
                            >{{ n.label }}</Link>
                        </nav>
                        <span class="h-4 w-px bg-white/20"></span>
                        <a href="/dashboard" class="text-sm text-slate-300 hover:text-white">Trang học viên</a>
                        <Link href="/doi-mat-khau" class="text-sm text-slate-300 hover:text-white">Đổi mật khẩu</Link>
                        <button @click="logout" class="text-sm text-slate-300 hover:text-white">Đăng xuất</button>
                    </div>

                    <!-- Mobile: nút toggle -->
                    <button @click="mobileOpen = !mobileOpen" class="rounded-md p-2 text-slate-300 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Menu">
                        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Mobile: menu xổ xuống -->
                <div v-show="mobileOpen" class="space-y-1 border-t border-white/10 py-2 lg:hidden">
                    <Link
                        v-for="n in nav"
                        :key="n.href"
                        :href="n.href"
                        @click="mobileOpen = false"
                        class="block rounded-md px-3 py-2 text-sm"
                        :class="isActive(n.href) ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
                    >{{ n.label }}</Link>
                    <div class="my-1 border-t border-white/10"></div>
                    <a href="/dashboard" class="block rounded-md px-3 py-2 text-sm text-slate-300 hover:bg-white/10 hover:text-white">Trang học viên</a>
                    <Link href="/doi-mat-khau" @click="mobileOpen = false" class="block rounded-md px-3 py-2 text-sm text-slate-300 hover:bg-white/10 hover:text-white">Đổi mật khẩu</Link>
                    <button @click="logout" class="block w-full rounded-md px-3 py-2 text-left text-sm text-slate-300 hover:bg-white/10 hover:text-white">Đăng xuất</button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-6 py-8">
            <div v-if="flashSuccess" class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200">
                {{ flashSuccess }}
            </div>
            <slot />
        </main>
    </div>
</template>
