<template>
    <div class="max-w-4xl">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Image Generator</h1>
        <p class="text-gray-500 mb-6">Create AI-powered images from text descriptions</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Input -->
            <div class="bg-white rounded-2xl border p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Describe your image <span class="text-red-500">*</span></label>
                    <textarea v-model="form.prompt" rows="5" required placeholder="A serene mountain landscape at sunset with..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Model</label>
                        <select v-model="form.model" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="dall-e-3">DALL-E 3</option>
                            <option value="dall-e-2">DALL-E 2</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Size</label>
                        <select v-model="form.size" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="1024x1024">1024x1024</option>
                            <option value="1792x1024">1792x1024 (Wide)</option>
                            <option value="1024x1792">1024x1792 (Tall)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Quality</label>
                        <select v-model="form.quality" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="standard">Standard</option>
                            <option value="hd">HD</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Style</label>
                        <select v-model="form.style" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="vivid">Vivid</option>
                            <option value="natural">Natural</option>
                        </select>
                    </div>
                </div>

                <button @click="generate" :disabled="loading || !form.prompt"
                    class="w-full py-3 bg-pink-600 text-white rounded-lg font-medium hover:bg-pink-700 disabled:opacity-50 transition-colors">
                    {{ loading ? 'Creating image...' : 'Generate Image' }}
                </button>
            </div>

            <!-- Output -->
            <div class="bg-white rounded-2xl border p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Result</h2>

                <div v-if="loading" class="flex items-center justify-center py-20">
                    <div class="animate-spin w-8 h-8 border-2 border-pink-600 border-t-transparent rounded-full"></div>
                </div>

                <div v-else-if="imageUrl">
                    <img :src="imageUrl" alt="Generated image" class="w-full rounded-xl shadow-sm" />
                    <p v-if="revisedPrompt" class="mt-3 text-xs text-gray-500">{{ revisedPrompt }}</p>
                    <a :href="imageUrl" download class="inline-block mt-3 text-sm text-pink-600 hover:underline">Download</a>
                </div>

                <div v-else class="text-center py-20 text-gray-400">
                    <p class="text-4xl mb-3">🎨</p>
                    <p>Your generated image will appear here</p>
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
const imageUrl = ref('');
const revisedPrompt = ref('');

const form = reactive({
    prompt: '',
    model: 'dall-e-3',
    size: '1024x1024',
    quality: 'standard',
    style: 'vivid',
});

async function generate() {
    loading.value = true;
    imageUrl.value = '';
    try {
        const res = await api.post('/generate/image', form);
        imageUrl.value = res.data.generation.image_url;
        revisedPrompt.value = res.data.generation.result;
        auth.fetchUser();
    } catch (e) {
        alert('Error: ' + (e.response?.data?.message || e.message));
    } finally {
        loading.value = false;
    }
}
</script>
