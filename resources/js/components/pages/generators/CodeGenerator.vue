<template>
    <div class="max-w-4xl">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Code Generator</h1>
        <p class="text-gray-500 mb-6">Generate production-ready code in any language</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">What do you need? <span class="text-red-500">*</span></label>
                    <textarea v-model="form.prompt" rows="6" required placeholder="Create a REST API endpoint in Laravel that..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm font-mono"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Language</label>
                        <select v-model="form.language" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option v-for="lang in languages" :key="lang" :value="lang">{{ lang }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Model</label>
                        <select v-model="form.model" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="gpt-4o-mini">GPT-4o Mini</option>
                            <option value="gpt-4o">GPT-4o (Best)</option>
                        </select>
                    </div>
                </div>

                <button @click="generate" :disabled="loading || !form.prompt"
                    class="w-full py-3 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 disabled:opacity-50 transition-colors">
                    {{ loading ? 'Generating code...' : 'Generate Code' }}
                </button>
            </div>

            <div class="bg-white rounded-2xl border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">Output</h2>
                    <button v-if="result" @click="copyResult" class="text-sm text-emerald-600 hover:underline">
                        {{ copied ? 'Copied!' : 'Copy' }}
                    </button>
                </div>

                <div v-if="loading" class="flex items-center justify-center py-20">
                    <div class="animate-spin w-8 h-8 border-2 border-emerald-600 border-t-transparent rounded-full"></div>
                </div>

                <pre v-else-if="result" class="bg-gray-900 text-gray-100 rounded-xl p-4 text-sm overflow-x-auto max-h-[500px] overflow-y-auto"><code>{{ result }}</code></pre>

                <div v-else class="text-center py-20 text-gray-400">
                    <p class="text-4xl mb-3">💻</p>
                    <p>Your generated code will appear here</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useAuthStore } from '../../../store/auth';
import api from '../../../services/api';

const auth = useAuthStore();
const loading = ref(false);
const result = ref('');
const copied = ref(false);

const languages = ['PHP', 'JavaScript', 'TypeScript', 'Python', 'Java', 'C#', 'Go', 'Rust', 'Ruby', 'Swift', 'Kotlin', 'SQL', 'HTML/CSS', 'React', 'Vue', 'Laravel', 'Node.js'];

const form = reactive({ prompt: '', language: 'PHP', model: 'gpt-4o-mini', max_tokens: 4096 });

async function generate() {
    loading.value = true;
    result.value = '';
    try {
        const res = await api.post('/generate/code', form);
        result.value = res.data.generation.result;
        auth.fetchUser();
    } catch (e) {
        result.value = 'Error: ' + (e.response?.data?.message || e.message);
    } finally {
        loading.value = false;
    }
}

function copyResult() {
    navigator.clipboard.writeText(result.value);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
}
</script>
