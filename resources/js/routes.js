import { createRouter, createWebHistory } from 'vue-router';

export const routes = [
    {
        path: '/',
        name: 'home', component: () => import('./pages/HomeView.vue'),
        meta: {
            requiresAuth: false,
            layout: 'ContainerLayout'
        }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('./pages/DashboardView.vue'),
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
        component: () => import('./pages/RegisterView.vue'),
        meta: {
            requiresAuth: true,
            layout: 'ContainerLayout'
        }
    },

];

const router = createRouter({ history: createWebHistory('/'), routes });

let bootstrapped = false;
let isAuthenticated = false;

async function bootstrapSession() {
    if (bootstrapped) return;
    await fetch('/sanctum/csrf-cookie', { credentials: 'include' });
    const response = await fetch('/api/me', { credentials: 'include', headers: { Accept: 'application/json' } });
    isAuthenticated = response.ok;
    bootstrapped = true;
}

router.beforeEach(async (to) => {
    await bootstrapSession();

    // if logout hit the laravel logout do the logout and then redirect to home
    if (to.path === '/logout') {
        await fetch('/logout', { credentials: 'include' });
        window.location.href = '/';
        return false;
    }

    if (to.meta.requiresAuth && !isAuthenticated) {
        const returnTo = encodeURIComponent(to.fullPath);

        // HARD NAVIGATION – leave the SPA to Laravel
        window.location.href = `/auth/eve/redirect?return=${returnTo}`;
        return false;
    }

    return true;
});

export default router;
