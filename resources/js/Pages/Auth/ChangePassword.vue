<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({ password: '', password_confirmation: '' });
function submit() {
    form.post('/doi-mat-khau', { onFinish: () => form.reset() });
}
</script>

<template>
    <Head title="Đổi mật khẩu" />
    <main class="min-h-screen flex items-center justify-center px-6 bg-slate-50">
        <div class="w-full max-w-sm">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-slate-900">Đổi mật khẩu</h1>
                <p class="mt-1 text-sm text-slate-500">Đặt mật khẩu mới cho lần đăng nhập đầu tiên.</p>
            </div>
            <form @submit.prevent="submit" class="space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Mật khẩu mới</label>
                    <input v-model="form.password" type="password" autocomplete="new-password"
                           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none" required />
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nhập lại mật khẩu</label>
                    <input v-model="form.password_confirmation" type="password" autocomplete="new-password"
                           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none" required />
                </div>
                <button type="submit" :disabled="form.processing"
                        class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-60">
                    {{ form.processing ? 'Đang lưu…' : 'Đổi mật khẩu' }}
                </button>
            </form>
        </div>
    </main>
</template>
