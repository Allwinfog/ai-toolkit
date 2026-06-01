<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Template Management</h1>
        <div class="bg-white rounded-2xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr><th class="px-6 py-3 text-left font-medium text-gray-500">Name</th><th class="px-6 py-3 text-left font-medium text-gray-500">Category</th><th class="px-6 py-3 text-left font-medium text-gray-500">Type</th><th class="px-6 py-3 text-left font-medium text-gray-500">Uses</th><th class="px-6 py-3 text-left font-medium text-gray-500">Status</th></tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="t in templates" :key="t.id" class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium">{{ t.name }}</td>
                        <td class="px-6 py-3 capitalize">{{ t.category }}</td>
                        <td class="px-6 py-3 capitalize">{{ t.type }}</td>
                        <td class="px-6 py-3">{{ t.usage_count }}</td>
                        <td class="px-6 py-3"><span class="px-2 py-1 rounded text-xs" :class="t.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">{{ t.is_active ? 'Active' : 'Inactive' }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../../services/api';
const templates = ref([]);
onMounted(async () => { templates.value = (await api.get('/admin/templates')).data; });
</script>
