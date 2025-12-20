<script>
import { defineComponent } from 'vue'
import dbmLogo from "../../../assets/images/dbmLogo.png";

export default defineComponent({
    computed: {
        dbmLogo() {
            return dbmLogo;
        }
    },
    data() {
        return {
            showMore: false,
            // Primary tabs visible on mobile (max 4 + "More" slot)
            primaryMenus: [
                { id: 1, name: "Home",     route: "/",         icon: "fa-solid fa-house" },
                { id: 2, name: "Profile",  route: "/profile",  icon: "fa-solid fa-user" },
                { id: 3, name: "Messages", route: "/messages", icon: "fa-solid fa-comments" },
                { id: 4, name: "Settings", route: "/settings", icon: "fa-solid fa-gears" },
            ],
            // Overflow items shown in the "More" drawer on mobile,
            // but displayed inline on tablet
            overflowMenus: [
                { id: 5, name: "Help",     route: "/help",     icon: "fa-solid fa-circle-question" },
                { id: 6, name: "Password", route: "/password", icon: "fa-solid fa-key" },
                { id: 7, name: "Sign Out", route: "/logout",   icon: "fa-solid fa-right-from-bracket" },
            ],
        };
    },
    computed: {
        allMenus() {
            return [...this.primaryMenus, ...this.overflowMenus];
        },
        isOverflowActive() {
            return this.overflowMenus.some(m => m.route === this.$route.path);
        },
    },
    methods: {
        closeMore() {
            this.showMore = false;
        }
    },
    watch: {
        '$route'() {
            this.showMore = false;
        }
    }
});
</script>

<template>
    <!-- More drawer backdrop -->
    <Transition name="fade">
        <div v-if="showMore" class="bottom-nav-backdrop" @click="closeMore" />
    </Transition>

    <!-- More drawer (mobile only) -->
    <Transition name="slide-up">
        <div v-if="showMore" class="bottom-nav-drawer">
            <div class="drawer-handle" />
            <router-link
                v-for="menu in overflowMenus"
                :key="menu.id"
                :to="menu.route"
                class="drawer-item"
                @click="closeMore"
            >
                <span class="drawer-icon"><i :class="menu.icon"></i></span>
                <span class="drawer-label">{{ menu.name }}</span>
                <i class="fa-solid fa-chevron-right drawer-chevron"></i>
            </router-link>
        </div>
    </Transition>

    <!-- Bottom nav bar -->
    <nav class="bottom-nav">
        <!-- Mobile: primary items only -->
        <router-link
            v-for="menu in primaryMenus"
            :key="menu.id"
            :to="menu.route"
            class="bottom-nav-tab is-mobile-only"
        >
            <i :class="menu.icon"></i>
            <span>{{ menu.name }}</span>
        </router-link>

        <!-- Mobile: More tab -->
        <button
            class="bottom-nav-tab is-mobile-only"
            :class="{ 'router-link-active': isOverflowActive || showMore }"
            @click="showMore = !showMore"
        >
            <i class="fa-solid fa-ellipsis"></i>
            <span>More</span>
        </button>

        <!-- Tablet: all items inline -->
        <router-link
            v-for="menu in allMenus"
            :key="`tablet-${menu.id}`"
            :to="menu.route"
            class="bottom-nav-tab is-tablet-only"
        >
            <i :class="menu.icon"></i>
            <span>{{ menu.name }}</span>
        </router-link>
    </nav>
</template>

<style lang="scss">
$nav-bg: var(--bulma-scheme-main);
$nav-height-mobile: 60px;
$nav-height-tablet: 68px;
$active-color: var(--bulma-link);
$inactive-color: var(--bulma-text-weak);

.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 100;

    display: flex;
    background: $nav-bg;
    border-top: 1px solid var(--bulma-border);

    // Safe area for notched phones
    padding-bottom: env(safe-area-inset-bottom);
}

.bottom-nav-tab {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;

    height: $nav-height-mobile;
    border: none;
    background: none;
    cursor: pointer;
    text-decoration: none;
    color: $inactive-color;
    transition: color 0.15s;
    position: relative;

    i {
        font-size: 1.2rem;
    }

    span {
        font-size: 0.65rem;
        font-weight: 500;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    &.router-link-active,
    &.router-link-exact-active {
        color: $active-color;

        &::after {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 32px;
            height: 2px;
            background: $active-color;
            border-radius: 0 0 4px 4px;
        }
    }

    &:hover:not(.router-link-active) {
        color: var(--bulma-text);
    }

    // Show/hide logic
    &.is-mobile-only  { display: flex; }
    &.is-tablet-only  { display: none; }

    @media (min-width: 768px) {
        height: $nav-height-tablet;

        i    { font-size: 1.35rem; }
        span { font-size: 0.72rem; }

        &.is-mobile-only { display: none; }
        &.is-tablet-only { display: flex; }
    }
}

// More drawer
.bottom-nav-backdrop {
    position: fixed;
    inset: 0;
    z-index: 98;
    background: rgba(0, 0, 0, 0.4);
}

.bottom-nav-drawer {
    position: fixed;
    left: 0;
    right: 0;
    bottom: calc(#{$nav-height-mobile} + env(safe-area-inset-bottom));
    z-index: 99;

    background: $nav-bg;
    border-top: 1px solid var(--bulma-border);
    border-radius: 16px 16px 0 0;
    padding: 8px 0 4px;

    @media (min-width: 768px) {
        display: none; // never shown on tablet
    }
}

.drawer-handle {
    width: 36px;
    height: 4px;
    border-radius: 2px;
    background: var(--bulma-border);
    margin: 0 auto 12px;
}

.drawer-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    text-decoration: none;
    color: var(--bulma-text);
    transition: background 0.1s;

    &:hover,
    &.router-link-active {
        background: var(--bulma-scheme-main-bis);
        color: $active-color;
    }
}

.drawer-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: var(--bulma-scheme-main-bis);
    display: flex;
    align-items: center;
    justify-content: center;

    i { font-size: 1rem; }
}

.drawer-label {
    flex: 1;
    font-size: 0.95rem;
    font-weight: 500;
}

.drawer-chevron {
    font-size: 0.75rem;
    color: var(--bulma-text-weak);
}

// Transitions
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }

.slide-up-enter-active, .slide-up-leave-active { transition: transform 0.25s cubic-bezier(.4,0,.2,1); }
.slide-up-enter-from, .slide-up-leave-to       { transform: translateY(100%); }
</style>
