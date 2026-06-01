<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Templates</h1>
        <p class="text-gray-500 mb-6">Pre-built prompts for common use cases</p>

        <!-- Category Filter -->
        <div class="flex flex-wrap gap-2 mb-6">
            <button v-for="(label, key) in categories" :key="key" @click="selectedCategory = selectedCategory === key ? '' : key"
                class="px-4 py-2 rounded-full text-sm font-medium transition-colors"
                :class="selectedCategory === key ? 'bg-indigo-600 text-white' : 'bg-white border text-gray-600 hover:bg-gray-50'">
                {{ label }}
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <router-link v-for="tmpl in filtered" :key="tmpl.id" :to="`/templates/${tmpl.id}`"
                class="group p-5 bg-white rounded-xl border hover:border-indigo-300 hover:shadow-md transition-all">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-lg shrink-0">
                        {{ typeIcons[tmpl.type] }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600">{{ tmpl.name }}</h3>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ tmpl.description }}</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ tmpl.type }}</span>
                            <span v-if="tmpl.is_premium" class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Premium</span>
                            <span class="text-xs text-gray-400">{{ tmpl.usage_count }} uses</span>
                        </div>
                    </div>
                </div>
            </router-link>
        </div>

        <div v-if="filtered.length === 0" class="text-center py-12 text-gray-400">No templates found</div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../../services/api';

const templates = ref([]);
const categories = ref({});
const selectedCategory = ref('');
const typeIcons = { text: '✍️', image: '🎨', code: '💻' };

const filtered = computed(() => {
    if (!selectedCategory.value) return templates.value;
    return templates.value.filter(t => t.category === selectedCategory.value);
});

onMounted(async () => {
    const [tmplRes, catRes] = await Promise.all([api.get('/templates'), api.get('/templates/categories')]);
    templates.value = tmplRes.data;
    categories.value = catRes.data;
});
</script>
