<template>
    <div class="max-w-4xl">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Billing & Plans</h1>
        <p class="text-gray-500 mb-6">Manage your subscription and view invoices</p>

        <!-- Current Plan -->
        <div class="bg-white rounded-2xl border p-6 mb-6">
            <h2 class="font-semibold text-gray-900 mb-2">Current Plan</h2>
            <div class="flex items-center gap-3">
                <span class="text-lg font-bold text-indigo-600">{{ auth.currentPlan?.name || 'Free' }}</span>
                <span class="text-sm text-gray-500">${{ auth.currentPlan?.price || '0' }}/mo</span>
            </div>
        </div>

        <!-- Plans -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div v-for="plan in plans" :key="plan.id"
                class="relative p-6 bg-white rounded-2xl border-2 transition-all"
                :class="plan.is_featured ? 'border-indigo-500 shadow-lg' : 'border-gray-200'">
                <div v-if="plan.is_featured" class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-indigo-600 text-white text-xs font-medium rounded-full">Popular</div>
                <h3 class="font-bold text-gray-900 text-lg">{{ plan.name }}</h3>
                <div class="mt-2 mb-4">
                    <span class="text-3xl font-bold">${{ plan.price }}</span>
                    <span class="text-gray-500">/mo</span>
                </div>
                <ul class="space-y-2 mb-6 text-sm text-gray-600">
                    <li>{{ plan.text_generations === -1 ? 'Unlimited' : plan.text_generations }} text generations</li>
                    <li>{{ plan.image_generations === -1 ? 'Unlimited' : plan.image_generations }} image generations</li>
                    <li>{{ plan.code_generations === -1 ? 'Unlimited' : plan.code_generations }} code generations</li>
                    <li>Up to {{ plan.words_per_generation.toLocaleString() }} words/generation</li>
                    <li v-for="feature in (plan.features || [])" :key="feature">{{ feature }}</li>
                </ul>
                <button @click="subscribe(plan)" :disabled="auth.currentPlan?.id === plan.id"
                    class="w-full py-2.5 rounded-lg font-medium text-sm transition-colors"
                    :class="auth.currentPlan?.id === plan.id ? 'bg-gray-100 text-gray-400' : plan.is_featured ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'border border-gray-300 hover:bg-gray-50'">
                    {{ auth.currentPlan?.id === plan.id ? 'Current Plan' : 'Upgrade' }}
                </button>
            </div>
        </div>

        <!-- Invoices -->
        <div class="bg-white rounded-2xl border">
            <h2 class="px-6 py-4 font-semibold border-b">Invoices</h2>
            <div v-if="invoices.length === 0" class="p-8 text-center text-gray-400">No invoices yet</div>
            <div v-else class="divide-y">
                <div v-for="inv in invoices" :key="inv.id" class="px-6 py-3 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium">{{ inv.date }}</p>
                        <p class="text-xs text-gray-500">{{ inv.total }}</p>
                    </div>
                    <a :href="inv.url" target="_blank" class="text-sm text-indigo-600 hover:underline">Download</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../../store/auth';
import api from '../../../services/api';

const auth = useAuthStore();
const plans = ref([]);
const invoices = ref([]);

onMounted(async () => {
    const [plansRes, invoicesRes] = await Promise.all([api.get('/plans'), api.get('/invoices').catch(() => ({ data: [] }))]);
    plans.value = plansRes.data;
    invoices.value = invoicesRes.data;
});

async function subscribe(plan) {
    if (plan.price === 0 || plan.price === '0.00') {
        await api.post('/subscribe', { plan_id: plan.id, payment_method: 'free' });
        auth.fetchUser();
        alert('Plan updated!');
    } else {
        alert('Stripe checkout would open here. Configure STRIPE_KEY in .env');
    }
}
</script>
