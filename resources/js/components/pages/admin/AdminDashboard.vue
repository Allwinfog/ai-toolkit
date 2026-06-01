<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Admin Dashboard</h1>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div v-for="stat in statCards" :key="stat.label" class="p-5 bg-white rounded-xl border">
                <p class="text-sm text-gray-500">{{ stat.label }}</p>
                <p class="text-2xl font-bold mt-1" :class="stat.color">{{ stat.value }}</p>
                <p v-if="stat.sub" class="text-xs text-gray-400 mt-1">{{ stat.sub }}</p>
            </div>
        </div>

        <!-- Generations by Type -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border p-6">
                <h2 class="font-semibold mb-4">Generations by Type</h2>
                <div v-for="(count, type) in data?.generations?.by_type || {}" :key="type" class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="capitalize">{{ type }}</span>
                        <span class="font-medium">{{ count }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" :class="barColors[type]" :style="{ width: barPercent(count) + '%' }"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border p-6">
                <h2 class="font-semibold mb-4">Popular Templates</h2>
                <div v-for="tmpl in data?.popular_templates || []" :key="tmpl.id" class="flex justify-between py-2 border-b last:border-0 text-sm">
                    <span>{{ tmpl.name }}</span>
                    <span class="font-medium text-gray-500">{{ tmpl.usage_count }} uses</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../../services/api';

const data = ref(null);
const barColors = { text: 'bg-indigo-500', image: 'bg-pink-500', code: 'bg-emerald-500' };

const statCards = computed(() => {
    if (!data.value) return [];
    const d = data.value;
    return [
        { label: 'Total Users', value: d.users.total, color: 'text-gray-900', sub: `${d.users.new_today} today` },
        { label: 'Generations', value: d.generations.total, color: 'text-indigo-600', sub: `${d.generations.today} today` },
        { label: 'API Cost', value: '$' + Number(d.generations.total_cost).toFixed(2), color: 'text-amber-600' },
        { label: 'MRR', value: '$' + Number(d.revenue.mrr).toFixed(0), color: 'text-emerald-600' },
    ];
});

function barPercent(count) {
    const total = Object.values(data.value?.generations?.by_type || {}).reduce((a, b) => a + b, 1);
    return (count / total) * 100;
}

onMounted(async () => {
    const res = await api.get('/admin/dashboard');
    data.value = res.data;
});
</script>
