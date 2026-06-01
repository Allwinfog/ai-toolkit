<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <router-link v-for="card in quickActions" :key="card.to" :to="card.to"
                class="group p-6 bg-white rounded-2xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" :class="card.iconBg">
                    <span class="text-lg">{{ card.emoji }}</span>
                </div>
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600">{{ card.title }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ card.desc }}</p>
            </router-link>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div v-for="stat in stats" :key="stat.label" class="p-5 bg-white rounded-xl border">
                <p class="text-sm text-gray-500">{{ stat.label }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stat.value }}</p>
            </div>
        </div>

        <!-- Recent Generations -->
        <div class="bg-white rounded-2xl border">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Recent Generations</h2>
                <router-link to="/history" class="text-sm text-indigo-600 hover:underline">View all</router-link>
            </div>
            <div v-if="recent.length === 0" class="p-12 text-center text-gray-400">
                No generations yet. Try creating your first one!
            </div>
            <div v-else class="divide-y">
                <div v-for="gen in recent" :key="gen.id" class="px-6 py-4 flex items-center gap-4">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm"
                        :class="typeColors[gen.type]">
                        {{ typeIcons[gen.type] }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ gen.prompt.substring(0, 80) }}</p>
                        <p class="text-xs text-gray-400">{{ gen.model_used }} &middot; {{ timeAgo(gen.created_at) }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full" :class="typeColors[gen.type]">{{ gen.type }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../../../store/auth';
import api from '../../../services/api';

const auth = useAuthStore();
const recent = ref([]);

const quickActions = [
    { to: '/text', title: 'Text Generator', desc: 'Blog posts, articles, marketing copy', emoji: '✍️', iconBg: 'bg-indigo-100' },
    { to: '/image', title: 'Image Generator', desc: 'AI art, logos, illustrations', emoji: '🎨', iconBg: 'bg-pink-100' },
    { to: '/code', title: 'Code Generator', desc: 'Functions, APIs, full components', emoji: '💻', iconBg: 'bg-emerald-100' },
];

const typeColors = { text: 'bg-indigo-100 text-indigo-700', image: 'bg-pink-100 text-pink-700', code: 'bg-emerald-100 text-emerald-700' };
const typeIcons = { text: 'T', image: 'I', code: 'C' };

const stats = computed(() => {
    const u = auth.usage;
    if (!u) return [];
    return [
        { label: 'Text Generated', value: u.text.used },
        { label: 'Images Created', value: u.image.used },
        { label: 'Code Snippets', value: u.code.used },
        { label: 'Total Words', value: u.total_words?.toLocaleString() || '0' },
    ];
});

function timeAgo(date) {
    const diff = (Date.now() - new Date(date)) / 1000;
    if (diff < 60) return 'just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
}

onMounted(async () => {
    try {
        const res = await api.get('/generations', { params: { per_page: 5 } });
        recent.value = res.data.data || [];
    } catch {}
});
</script>
