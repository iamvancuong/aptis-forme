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
            <div class="mx-auto max-w-6xl px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-2 font-bold">
                        <BrandLogo :size="26" />
                        nhaiaptis · Admin
                    </span>
                    <nav class="flex gap-1 text-sm">
                        <Link
                            v-for="n in nav"
                            :key="n.href"
                            :href="n.href"
                            class="rounded-md px-3 py-1.5"
                            :class="isActive(n.href) ? 'bg-white/15 text-white' : 'text-slate-300 hover:text-white'"
                        >{{ n.label }}</Link>
                    </nav>
                </div>
                <div class="flex items-center gap-3">
                    <a href="/dashboard" class="text-sm text-slate-300 hover:text-white">Trang học viên</a>
                    <Link href="/doi-mat-khau" class="text-sm text-slate-300 hover:text-white">Đổi mật khẩu</Link>
                    <button @click="logout" class="text-sm text-slate-300 hover:text-white">Đăng xuất</button>
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
