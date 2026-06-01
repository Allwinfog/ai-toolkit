<template>
    <div class="max-w-4xl">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Text Generator</h1>
        <p class="text-gray-500 mb-6">Generate blog posts, articles, marketing copy, and more</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Input -->
            <div class="bg-white rounded-2xl border p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">System Prompt (optional)</label>
                        <textarea v-model="form.system_prompt" rows="2" placeholder="e.g., You are a professional copywriter..."
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Prompt <span class="text-red-500">*</span></label>
                        <textarea v-model="form.prompt" rows="6" required placeholder="Write a 500-word blog post about..."
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Model</label>
                            <select v-model="form.model" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="gpt-4o-mini">GPT-4o Mini (Fast)</option>
                                <option value="gpt-4o">GPT-4o (Best)</option>
                                <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Max Tokens</label>
                            <input v-model.number="form.max_tokens" type="number" min="100" max="8000"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Temperature: {{ form.temperature }}</label>
                        <input v-model.number="form.temperature" type="range" min="0" max="2" step="0.1" class="w-full" />
                    </div>

                    <button @click="generate" :disabled="loading || !form.prompt"
                        class="w-full py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                        {{ loading ? 'Generating...' : 'Generate Text' }}
                    </button>
                </div>
            </div>

            <!-- Output -->
            <div class="bg-white rounded-2xl border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">Output</h2>
                    <button v-if="result" @click="copyResult" class="text-sm text-indigo-600 hover:underline">
                        {{ copied ? 'Copied!' : 'Copy' }}
                    </button>
                </div>

                <div v-if="loading" class="flex items-center justify-center py-20">
                    <div class="animate-spin w-8 h-8 border-2 border-indigo-600 border-t-transparent rounded-full"></div>
                </div>

                <div v-else-if="result" class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap">{{ result }}</div>

                <div v-else class="text-center py-20 text-gray-400">
                    <p class="text-4xl mb-3">✍️</p>
                    <p>Your generated text will appear here</p>
                </div>

                <div v-if="meta" class="mt-4 pt-4 border-t flex gap-4 text-xs text-gray-400">
                    <span>{{ meta.tokens_used }} tokens</span>
                    <span>{{ meta.word_count }} words</span>
                    <span>${{ meta.cost }}</span>
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
const meta = ref(null);
const copied = ref(false);

const form = reactive({
    prompt: '',
    system_prompt: '',
    model: 'gpt-4o-mini',
    max_tokens: 2048,
    temperature: 0.7,
});

async function generate() {
    loading.value = true;
    result.value = '';
    meta.value = null;
    try {
        const res = await api.post('/generate/text', form);
        const gen = res.data.generation;
        result.value = gen.result;
        meta.value = { tokens_used: gen.tokens_used, word_count: gen.word_count, cost: gen.cost };
        auth.fetchUser(); // refresh usage
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
