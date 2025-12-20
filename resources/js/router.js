import { createRouter, createWebHistory } from 'vue-router';
import { useUserStore } from './stores/user';
import axios from 'axios'

export const routes = [
    {
        path: '/',
        name: 'home', component: () => import('./pages/home/index.vue'),
        meta: {
            requiresAuth: false,
            layout: 'ContainerLayout'
        }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('./pages/dashboard/index.vue'),
        meta: {
            requiresAuth: true,
            layout: 'ContainerLayout'
        }
    },
    {
        path: '/dashboard/profile',
        name: 'profile',
        component: () => import('./pages/profile/index.vue'),
        meta: {
            layout: 'SidebarLayout',
            requiresAuth: true,
        }
    },
    {
        path: '/account/register',
        name: 'register',
        component: () => import('./pages/register/index.vue'),
        meta: {
            requiresAuth: false,
            layout: 'ContainerLayout'
        }
    },

];

const router = createRouter({ history: createWebHistory('/'), routes });

router.beforeEach(async (to) => {
   const userStore = useUserStore();
   await userStore.authBootstrap();

    if (to.path === '/logout') {
        await axios.post('/logout', {}, { withCredentials: true });
        userStore.$reset();
        window.location.href = '/';
        return false;
    }

    if (to.meta.requiresAuth && !userStore.isAuthenticated) {
        const returnTo = encodeURIComponent(to.fullPath);

        // HARD NAVIGATION – leave the SPA to Laravel
        window.location.href = `/auth/eve/redirect?return=${returnTo}`;
        return false;
    }

    return true;
});

export default router;
