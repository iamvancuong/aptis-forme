<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import BrandLogo from '../components/BrandLogo.vue';

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const url = computed(() => page.url);

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
                <!-- Hàng 1: logo + hành động -->
                <div class="flex items-center justify-between gap-3 py-3">
                    <span class="flex shrink-0 items-center gap-2 font-bold whitespace-nowrap">
                        <BrandLogo :size="26" />
                        <span class="hidden sm:inline">nhaiaptis · Admin</span>
                        <span class="sm:hidden">Admin</span>
                    </span>
                    <div class="flex flex-wrap items-center justify-end gap-x-3 gap-y-1 text-sm">
                        <a href="/dashboard" class="text-slate-300 hover:text-white">Trang học viên</a>
                        <Link href="/doi-mat-khau" class="text-slate-300 hover:text-white">Đổi mật khẩu</Link>
                        <button @click="logout" class="text-slate-300 hover:text-white">Đăng xuất</button>
                    </div>
                </div>
                <!-- Hàng 2: menu (cuộn ngang trên mobile) -->
                <nav class="-mx-1 flex gap-1 overflow-x-auto pb-2 text-sm">
                    <Link
                        v-for="n in nav"
                        :key="n.href"
                        :href="n.href"
                        class="shrink-0 whitespace-nowrap rounded-md px-3 py-1.5"
                        :class="isActive(n.href) ? 'bg-white/15 text-white' : 'text-slate-300 hover:text-white'"
                    >{{ n.label }}</Link>
                </nav>
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
