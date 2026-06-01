import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../store/auth';

const routes = [
    // Auth
    { path: '/login', name: 'login', component: () => import('../components/pages/auth/Login.vue'), meta: { guest: true } },
    { path: '/register', name: 'register', component: () => import('../components/pages/auth/Register.vue'), meta: { guest: true } },

    // App
    {
        path: '/',
        component: () => import('../components/layouts/AppLayout.vue'),
        meta: { auth: true },
        children: [
            { path: '', name: 'dashboard', component: () => import('../components/pages/dashboard/Dashboard.vue') },
            { path: 'text', name: 'text-generator', component: () => import('../components/pages/generators/TextGenerator.vue') },
            { path: 'image', name: 'image-generator', component: () => import('../components/pages/generators/ImageGenerator.vue') },
            { path: 'code', name: 'code-generator', component: () => import('../components/pages/generators/CodeGenerator.vue') },
            { path: 'templates', name: 'templates', component: () => import('../components/pages/generators/Templates.vue') },
            { path: 'templates/:id', name: 'template-generator', component: () => import('../components/pages/generators/TemplateGenerator.vue') },
            { path: 'history', name: 'history', component: () => import('../components/pages/history/History.vue') },
            { path: 'billing', name: 'billing', component: () => import('../components/pages/billing/Billing.vue') },

            // Admin
            { path: 'admin', name: 'admin-dashboard', component: () => import('../components/pages/admin/AdminDashboard.vue'), meta: { admin: true } },
            { path: 'admin/users', name: 'admin-users', component: () => import('../components/pages/admin/Users.vue'), meta: { admin: true } },
            { path: 'admin/plans', name: 'admin-plans', component: () => import('../components/pages/admin/Plans.vue'), meta: { admin: true } },
            { path: 'admin/templates', name: 'admin-templates', component: () => import('../components/pages/admin/AdminTemplates.vue'), meta: { admin: true } },
        ],
    },
];

const router = createRouter({ history: createWebHistory(), routes });

router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore();

    if (to.meta.auth && !auth.isAuthenticated) return next('/login');
    if (to.meta.guest && auth.isAuthenticated) return next('/');
    if (to.meta.admin && !auth.isAdmin) return next('/');

    if (auth.isAuthenticated && !auth.user) {
        await auth.fetchUser();
    }

    next();
});

export default router;
