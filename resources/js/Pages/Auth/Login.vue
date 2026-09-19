<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const flashError = computed(() => page.props.flash?.error);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Đăng nhập" />

    <main class="min-h-screen flex items-center justify-center px-6 bg-slate-50">
        <div class="w-full max-w-sm">
            <div class="text-center mb-8">
                <span class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-brand-600 to-violet-600 text-lg font-bold text-white">A</span>
                <h1 class="mt-3 text-2xl font-bold text-slate-900">APTIS V2</h1>
                <p class="mt-1 text-sm text-slate-500">Đăng nhập để vào luyện thi</p>
            </div>

            <div v-if="flashError" class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
                {{ flashError }}
            </div>

            <form @submit.prevent="submit" class="space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="username"
                        class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none"
                        required
                    />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Mật khẩu</label>
                    <input
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none"
                        required
                    />
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input v-model="form.remember" type="checkbox" class="rounded border-slate-300" />
                    Ghi nhớ đăng nhập
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-60"
                >
                    {{ form.processing ? 'Đang đăng nhập…' : 'Đăng nhập' }}
                </button>
            </form>
        </div>
    </main>
</template>
