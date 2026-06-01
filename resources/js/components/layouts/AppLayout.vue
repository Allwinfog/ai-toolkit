<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-30" :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }" style="transition: transform 0.2s">
            <div class="flex items-center gap-2 h-16 px-6 border-b border-gray-200">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">AI</span>
                </div>
                <span class="font-bold text-lg text-gray-900">AI Toolkit</span>
            </div>

            <nav class="px-3 py-4 space-y-1">
                <router-link v-for="item in navItems" :key="item.to" :to="item.to"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
                    :class="$route.path === item.to ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'">
                    <component :is="item.icon" class="w-5 h-5" />
                    {{ item.label }}
                </router-link>

                <div v-if="auth.isAdmin" class="pt-4 mt-4 border-t border-gray-200">
                    <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase">Admin</p>
                    <router-link v-for="item in adminNav" :key="item.to" :to="item.to"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
                        :class="$route.path === item.to ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'">
                        {{ item.label }}
                    </router-link>
                </div>
            </nav>

            <!-- Usage Card -->
            <div class="absolute bottom-4 left-3 right-3 p-4 bg-gray-50 rounded-xl border">
                <p class="text-xs font-semibold text-gray-500 mb-2">Usage This Month</p>
                <div v-for="(item, key) in usageBars" :key="key" class="mb-2">
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>{{ item.label }}</span>
                        <span>{{ item.used }}/{{ item.limit === -1 ? '∞' : item.limit }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all" :class="item.color" :style="{ width: item.percent + '%' }"></div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main -->
        <div class="lg:pl-64">
            <header class="sticky top-0 z-20 h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-4 ml-auto">
                    <span class="text-sm text-gray-600">{{ auth.user?.name }}</span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full" :class="auth.currentPlan ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600'">
                        {{ auth.currentPlan?.name || 'Free' }}
                    </span>
                    <button @click="auth.logout().then(() => $router.push('/login'))" class="text-sm text-gray-500 hover:text-gray-700">Logout</button>
                </div>
            </header>

            <main class="p-6">
                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../../store/auth';

const auth = useAuthStore();
const sidebarOpen = ref(true);

onMounted(() => { if (!auth.user) auth.fetchUser(); });

const navItems = [
    { to: '/', label: 'Dashboard', icon: 'span' },
    { to: '/text', label: 'Text Generator', icon: 'span' },
    { to: '/image', label: 'Image Generator', icon: 'span' },
    { to: '/code', label: 'Code Generator', icon: 'span' },
    { to: '/templates', label: 'Templates', icon: 'span' },
    { to: '/history', label: 'History', icon: 'span' },
    { to: '/billing', label: 'Billing', icon: 'span' },
];

const adminNav = [
    { to: '/admin', label: 'Dashboard' },
    { to: '/admin/users', label: 'Users' },
    { to: '/admin/plans', label: 'Plans' },
    { to: '/admin/templates', label: 'Templates' },
];

const usageBars = computed(() => {
    const u = auth.usage;
    if (!u) return [];
    return [
        { label: 'Text', used: u.text.used, limit: u.text.limit, color: 'bg-indigo-500', percent: u.text.limit === -1 ? 5 : Math.min(100, (u.text.used / u.text.limit) * 100) },
        { label: 'Image', used: u.image.used, limit: u.image.limit, color: 'bg-pink-500', percent: u.image.limit === -1 ? 5 : Math.min(100, (u.image.used / u.image.limit) * 100) },
        { label: 'Code', used: u.code.used, limit: u.code.limit, color: 'bg-emerald-500', percent: u.code.limit === -1 ? 5 : Math.min(100, (u.code.used / u.code.limit) * 100) },
    ];
});
</script>
