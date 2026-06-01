<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">AI</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Create your account</h1>
                <p class="text-gray-500 mt-1">Start generating with AI Toolkit</p>
            </div>

            <form @submit.prevent="handleRegister" class="bg-white rounded-2xl shadow-sm border p-8 space-y-5">
                <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">{{ error }}</div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input v-model="form.email" type="email" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input v-model="form.password" type="password" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input v-model="form.password_confirmation" type="password" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                </div>

                <button type="submit" :disabled="loading" class="w-full py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 disabled:opacity-50">
                    {{ loading ? 'Creating account...' : 'Create Account' }}
                </button>

                <p class="text-center text-sm text-gray-500">
                    Already have an account? <router-link to="/login" class="text-indigo-600 font-medium hover:underline">Sign in</router-link>
                </p>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../../store/auth';

const router = useRouter();
const auth = useAuthStore();
const loading = ref(false);
const error = ref('');
const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });

async function handleRegister() {
    loading.value = true;
    error.value = '';
    try {
        await auth.register(form);
        router.push('/');
    } catch (e) {
        error.value = e.response?.data?.message || 'Registration failed';
    } finally {
        loading.value = false;
    }
}
</script>
