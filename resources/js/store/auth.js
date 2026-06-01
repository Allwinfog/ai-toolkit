import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token'),
        usage: null,
        loading: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role === 'admin',
        currentPlan: (state) => state.user?.plan,
    },

    actions: {
        async register(data) {
            const res = await api.post('/register', data);
            this.setAuth(res.data);
            return res.data;
        },

        async login(data) {
            const res = await api.post('/login', data);
            this.setAuth(res.data);
            return res.data;
        },

        async logout() {
            try { await api.post('/logout'); } catch {}
            this.clearAuth();
        },

        async fetchUser() {
            try {
                const res = await api.get('/me');
                this.user = res.data.user;
                this.usage = res.data.usage;
            } catch {
                this.clearAuth();
            }
        },

        setAuth({ user, token, usage }) {
            this.user = user;
            this.token = token;
            this.usage = usage;
            localStorage.setItem('token', token);
        },

        clearAuth() {
            this.user = null;
            this.token = null;
            this.usage = null;
            localStorage.removeItem('token');
        },
    },
});
