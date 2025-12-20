<script setup>
import { ref, watch, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useTheme } from '@/composables/useTheme.js'
import { useUserStore } from '@/stores/user.js'

const { mode, setTheme } = useTheme()
const userStore = useUserStore()
const route = useRoute()

const menuOpen = ref(false)

// Close drawer on route change
watch(() => route.path, () => { menuOpen.value = false })

const initials = computed(() => {
    const name = userStore.user?.name || ''
    return name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase() || '?'
})
</script>

<template>
    <nav class="topnav" role="navigation" aria-label="main navigation">

        <div class="topnav__brand">
            <span class="topnav__logo">DBM</span>
            <span class="topnav__name">DBM Tools</span>
        </div>

        <div class="topnav__end">
            <div class="theme-toggle" role="group" aria-label="Theme">
                <button class="theme-toggle__btn" :class="{ 'is-active': mode === 'auto' }"  @click="setTheme('auto')"  title="Auto">
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>
                <button class="theme-toggle__btn" :class="{ 'is-active': mode === 'light' }" @click="setTheme('light')" title="Light">
                    <i class="fa-solid fa-sun"></i>
                </button>
                <button class="theme-toggle__btn" :class="{ 'is-active': mode === 'dark' }"  @click="setTheme('dark')"  title="Dark">
                    <i class="fa-solid fa-moon"></i>
                </button>
            </div>

            <div v-if="userStore.isAuthenticated" class="topnav__divider" />

            <div v-if="userStore.isAuthenticated" class="topnav__avatar" :title="userStore.user?.name">
                {{ initials }}
            </div>

            <div class="topnav__divider" />

            <button
                class="topnav__burger"
                :class="{ 'is-open': menuOpen }"
                :aria-expanded="menuOpen"
                aria-label="Toggle navigation"
                @click="menuOpen = !menuOpen"
            >
                <span /><span /><span />
            </button>
        </div>
    </nav>

    <!-- Slide-down drawer -->
    <Transition name="drawer">
        <div v-if="menuOpen" class="nav-drawer" role="menu">
            <template v-if="!userStore.isAuthenticated">
                <RouterLink class="nav-drawer__link" to="/" role="menuitem">
                    <i class="fa-solid fa-house"></i>
                    <span>Home</span>
                </RouterLink>
            </template>
            <template v-else>
                <RouterLink class="nav-drawer__link" to="/dashboard" role="menuitem">
                    <i class="fa-solid fa-gauge"></i>
                    <span>Dashboard</span>
                </RouterLink>
                <RouterLink class="nav-drawer__link" to="/dashboard/profile" role="menuitem">
                    <i class="fa-solid fa-user"></i>
                    <span>Profile</span>
                </RouterLink>
                <div class="nav-drawer__divider" />
                <button class="nav-drawer__link" role="menuitem" @click="logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Sign out</span>
                </button>
            </template>
        </div>
    </Transition>
</template>

<style scoped lang="scss">
.topnav {
    position: relative;
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 56px;
    padding: 0 1rem;
    background: var(--bulma-scheme-main);
    border-bottom: 1px solid var(--bulma-border);

    &__brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    &__logo {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--bulma-link);
        color: var(--bulma-link-invert);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    &__name {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--bulma-text);
    }

    &__end {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    &__divider {
        width: 1px;
        height: 20px;
        background: var(--bulma-border);
    }

    &__avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--bulma-link);
        color: var(--bulma-link-invert);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    // ── Burger ──────────────────────────────────────────
    &__burger {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        background: none;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 0;

        span {
            display: block;
            width: 18px;
            height: 1.5px;
            background: var(--bulma-text-weak);
            border-radius: 2px;
            transform-origin: center;
            transition: transform 0.25s ease, opacity 0.25s ease;
        }

        &:hover span { background: var(--bulma-text); }

        &.is-open {
            span:nth-child(1) { transform: translateY(6.5px) rotate(45deg); }
            span:nth-child(2) { opacity: 0; transform: scaleX(0); }
            span:nth-child(3) { transform: translateY(-6.5px) rotate(-45deg); }
        }
    }
}

// ── Theme toggle ─────────────────────────────────────────
.theme-toggle {
    display: flex;
    align-items: center;
    background: var(--bulma-scheme-main-bis);
    border-radius: 8px;
    padding: 3px;
    gap: 2px;

    &__btn {
        width: 28px;
        height: 26px;
        border-radius: 6px;
        border: none;
        background: none;
        color: var(--bulma-text-weak);
        cursor: pointer;
        font-size: 0.8rem;
        transition: color 0.15s, background 0.15s;

        &:hover { color: var(--bulma-text); }

        &.is-active {
            background: var(--bulma-scheme-main);
            color: var(--bulma-text);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }
    }
}

// ── Drawer ───────────────────────────────────────────────
.nav-drawer {
    position: absolute; // relative to .topnav's z-index stacking context
    left: 0;
    right: 0;
    z-index: 49;
    background: var(--bulma-scheme-main);
    border-bottom: 1px solid var(--bulma-border);
    padding: 8px 12px 12px;
    display: flex;
    flex-direction: column;
    gap: 2px;

    &__link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--bulma-text-weak);
        border: none;
        background: none;
        cursor: pointer;
        width: 100%;
        text-align: left;
        transition: color 0.15s, background 0.15s;

        i { width: 16px; text-align: center; }

        &:hover { color: var(--bulma-text); background: var(--bulma-scheme-main-bis); }

        &.router-link-active {
            color: var(--bulma-text);
            background: var(--bulma-scheme-main-ter);
        }
    }

    &__divider {
        height: 1px;
        background: var(--bulma-border);
        margin: 4px 0;
    }
}

// ── Drawer transition ────────────────────────────────────
.drawer-enter-active,
.drawer-leave-active {
    transition: opacity 0.2s ease, transform 0.25s cubic-bezier(.4, 0, .2, 1);
}
.drawer-enter-from,
.drawer-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
