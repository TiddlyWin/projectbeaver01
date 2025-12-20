<script setup>
import Sidebar from "@/layout/components/sidebar.vue";
import BottomNav from "@/layout/components/bottomNav.vue";
</script>

<template>
    <div class="sidebar-layout">

        <!-- Desktop sidebar, hidden on touch -->
        <aside class="sidebar-layout__sidebar is-hidden-touch">
            <Sidebar />
        </aside>

        <!-- Scrollable main content area -->
        <main class="sidebar-layout__main">
            <slot />
        </main>

        <!-- Fixed bottom nav, touch only — lives outside flow -->
        <BottomNav class="is-hidden-desktop" />

    </div>
</template>

<style scoped lang="scss">
.sidebar-layout {
    display: flex;
    height: 100vh;
    overflow: hidden;

    &__sidebar {
        flex-shrink: 0;
        // Match whatever width your sidebar expands to
        width: 68px; // closed state — sidebar manages its own open width
    }

    &__main {
        flex: 1;
        overflow-y: auto;
        // Pad bottom so content clears the fixed bottom nav on touch
        padding-bottom: calc(60px + env(safe-area-inset-bottom));

        @media (min-width: 1024px) {
            padding-bottom: 0; // no bottom nav on desktop
        }
    }
}
</style>
