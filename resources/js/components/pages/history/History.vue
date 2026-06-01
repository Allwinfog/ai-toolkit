<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Generation History</h1>

        <div class="flex flex-wrap gap-2 mb-6">
            <button v-for="t in ['all', 'text', 'image', 'code']" :key="t" @click="filter = t === 'all' ? '' : t"
                class="px-4 py-2 rounded-full text-sm font-medium"
                :class="(t === 'all' && !filter) || filter === t ? 'bg-indigo-600 text-white' : 'bg-white border text-gray-600 hover:bg-gray-50'">
                {{ t === 'all' ? 'All' : t.charAt(0).toUpperCase() + t.slice(1) }}
            </button>
        </div>

        <div class="bg-white rounded-2xl border divide-y">
            <div v-for="gen in generations" :key="gen.id" class="p-5 hover:bg-gray-50 transition-colors">
                <div class="flex items-start gap-4">
                    <span class="w-9 h-9 rounded-lg flex items-center justify-center text-sm shrink-0" :class="typeColors[gen.type]">
                        {{ typeIcons[gen.type] }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ gen.prompt.substring(0, 120) }}{{ gen.prompt.length > 120 ? '...' : '' }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ gen.model_used }} &middot; {{ gen.word_count }} words &middot; {{ new Date(gen.created_at).toLocaleDateString() }}</p>
                        <div v-if="expanded === gen.id" class="mt-3 p-4 bg-gray-50 rounded-lg">
                            <img v-if="gen.image_url" :src="gen.image_url" class="max-w-sm rounded-lg mb-3" />
                            <pre v-else-if="gen.type === 'code'" class="bg-gray-900 text-gray-100 rounded-lg p-3 text-xs overflow-auto"><code>{{ gen.result }}</code></pre>
                            <p v-else class="text-sm text-gray-700 whitespace-pre-wrap">{{ gen.result }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button @click="toggleFavorite(gen)" class="text-lg" :class="gen.is_favorite ? 'text-amber-500' : 'text-gray-300 hover:text-amber-400'">
                            {{ gen.is_favorite ? '★' : '☆' }}
                        </button>
                        <button @click="expanded = expanded === gen.id ? null : gen.id" class="text-sm text-indigo-600 hover:underline">
                            {{ expanded === gen.id ? 'Hide' : 'View' }}
                        </button>
                        <button @click="deleteGen(gen)" class="text-sm text-red-500 hover:underline">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="generations.length === 0" class="text-center py-12 text-gray-400">No generations yet</div>

        <div v-if="hasMore" class="text-center mt-6">
            <button @click="loadMore" class="px-6 py-2 border rounded-lg text-sm hover:bg-gray-50">Load more</button>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import api from '../../../services/api';

const generations = ref([]);
const filter = ref('');
const expanded = ref(null);
const page = ref(1);
const hasMore = ref(false);

const typeColors = { text: 'bg-indigo-100 text-indigo-700', image: 'bg-pink-100 text-pink-700', code: 'bg-emerald-100 text-emerald-700' };
const typeIcons = { text: 'T', image: 'I', code: 'C' };

async function load() {
    const res = await api.get('/generations', { params: { type: filter.value || undefined, page: page.value, per_page: 20 } });
    if (page.value === 1) generations.value = res.data.data;
    else generations.value.push(...res.data.data);
    hasMore.value = res.data.current_page < res.data.last_page;
}

function loadMore() { page.value++; load(); }

async function toggleFavorite(gen) {
    await api.post(`/generations/${gen.id}/favorite`);
    gen.is_favorite = !gen.is_favorite;
}

async function deleteGen(gen) {
    if (!confirm('Delete this generation?')) return;
    await api.delete(`/generations/${gen.id}`);
    generations.value = generations.value.filter(g => g.id !== gen.id);
}

watch(filter, () => { page.value = 1; load(); });
onMounted(load);
</script>
