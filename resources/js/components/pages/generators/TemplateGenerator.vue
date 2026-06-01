<template>
    <div class="max-w-3xl" v-if="template">
        <router-link to="/templates" class="text-sm text-indigo-600 hover:underline mb-4 inline-block">&larr; Back to Templates</router-link>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ template.name }}</h1>
        <p class="text-gray-500 mb-6">{{ template.description }}</p>

        <div class="bg-white rounded-2xl border p-6 space-y-4">
            <div v-for="field in template.fields" :key="field.name">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ field.label }} <span v-if="field.required" class="text-red-500">*</span></label>
                <textarea v-if="field.type === 'textarea'" v-model="inputs[field.name]" :rows="field.rows || 3" :placeholder="field.placeholder"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                <select v-else-if="field.type === 'select'" v-model="inputs[field.name]" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                </select>
                <input v-else v-model="inputs[field.name]" :type="field.type || 'text'" :placeholder="field.placeholder"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
            </div>

            <button @click="generate" :disabled="loading" class="w-full py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 disabled:opacity-50">
                {{ loading ? 'Generating...' : 'Generate' }}
            </button>
        </div>

        <div v-if="result" class="mt-6 bg-white rounded-2xl border p-6">
            <div class="flex justify-between mb-3">
                <h2 class="font-semibold">Result</h2>
                <button @click="navigator.clipboard.writeText(result)" class="text-sm text-indigo-600">Copy</button>
            </div>
            <div v-if="template.type === 'image' && imageUrl">
                <img :src="imageUrl" class="w-full rounded-xl" />
            </div>
            <pre v-else-if="template.type === 'code'" class="bg-gray-900 text-gray-100 rounded-xl p-4 text-sm overflow-auto"><code>{{ result }}</code></pre>
            <div v-else class="prose prose-sm whitespace-pre-wrap">{{ result }}</div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../../services/api';

const route = useRoute();
const template = ref(null);
const inputs = reactive({});
const loading = ref(false);
const result = ref('');
const imageUrl = ref('');

onMounted(async () => {
    const res = await api.get(`/templates/${route.params.id}`);
    template.value = res.data;
    res.data.fields.forEach(f => { inputs[f.name] = f.default || ''; });
});

async function generate() {
    loading.value = true;
    result.value = '';
    try {
        const res = await api.post(`/generate/template/${template.value.id}`, { inputs });
        result.value = res.data.generation.result;
        imageUrl.value = res.data.generation.image_url || '';
    } catch (e) {
        result.value = 'Error: ' + (e.response?.data?.message || e.message);
    } finally {
        loading.value = false;
    }
}
</script>
