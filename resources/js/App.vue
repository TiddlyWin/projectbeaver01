<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import ContainerLayout from './layout/ContainerLayout.vue'
import SidebarLayout from './layout/SidebarLayout.vue'

const route = useRoute()
const layout = computed(() => {
    const layoutName = route.meta.layout || 'ContainerLayout'
    return layoutName === 'ContainerLayout' ? ContainerLayout : SidebarLayout
})
</script>

<template>
    <component :is="layout">
        <router-view></router-view>
    </component>
</template>
