<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Plan Management</h1>
        <div class="bg-white rounded-2xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr><th class="px-6 py-3 text-left font-medium text-gray-500">Plan</th><th class="px-6 py-3 text-left font-medium text-gray-500">Price</th><th class="px-6 py-3 text-left font-medium text-gray-500">Text</th><th class="px-6 py-3 text-left font-medium text-gray-500">Image</th><th class="px-6 py-3 text-left font-medium text-gray-500">Code</th><th class="px-6 py-3 text-left font-medium text-gray-500">Users</th></tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="plan in plans" :key="plan.id" class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium">{{ plan.name }}</td>
                        <td class="px-6 py-3">${{ plan.price }}/{{ plan.billing_cycle }}</td>
                        <td class="px-6 py-3">{{ plan.text_generations === -1 ? '∞' : plan.text_generations }}</td>
                        <td class="px-6 py-3">{{ plan.image_generations === -1 ? '∞' : plan.image_generations }}</td>
                        <td class="px-6 py-3">{{ plan.code_generations === -1 ? '∞' : plan.code_generations }}</td>
                        <td class="px-6 py-3">{{ plan.users_count || 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../../services/api';
const plans = ref([]);
onMounted(async () => { plans.value = (await api.get('/admin/plans')).data; });
</script>
