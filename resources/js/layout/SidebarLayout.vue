<script setup>

import Navigation from "@/layout/components/navigation.vue";
import Sidebar from "@/layout/components/sidebar.vue";
</script>

<template>
<Navigation />
    <div class="is-flex">
        <Sidebar />
        <main class="bulma-is-flex-grow-1">
            <router-view></router-view>
        </main>
    </div>
</template>

<style scoped>

</style>
