<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">User Management</h1>
        <div class="bg-white rounded-2xl border">
            <div class="px-6 py-4 border-b">
                <input v-model="search" @input="load" placeholder="Search users..." class="w-full max-w-sm px-4 py-2 border rounded-lg text-sm" />
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr><th class="px-6 py-3 text-left font-medium text-gray-500">Name</th><th class="px-6 py-3 text-left font-medium text-gray-500">Email</th><th class="px-6 py-3 text-left font-medium text-gray-500">Plan</th><th class="px-6 py-3 text-left font-medium text-gray-500">Role</th><th class="px-6 py-3 text-left font-medium text-gray-500">Status</th><th class="px-6 py-3"></th></tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium">{{ user.name }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ user.email }}</td>
                        <td class="px-6 py-3"><span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs">{{ user.plan?.name || 'Free' }}</span></td>
                        <td class="px-6 py-3">
                            <select :value="user.role" @change="updateUser(user, { role: $event.target.value })" class="px-2 py-1 border rounded text-xs">
                                <option value="user">User</option><option value="admin">Admin</option>
                            </select>
                        </td>
                        <td class="px-6 py-3">
                            <button @click="updateUser(user, { is_active: !user.is_active })" class="px-2 py-1 rounded text-xs" :class="user.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                {{ user.is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-400">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../../services/api';

const users = ref([]);
const search = ref('');

async function load() {
    const res = await api.get('/admin/users', { params: { search: search.value } });
    users.value = res.data.data;
}

async function updateUser(user, data) {
    const res = await api.put(`/admin/users/${user.id}`, data);
    Object.assign(user, res.data);
}

onMounted(load);
</script>
