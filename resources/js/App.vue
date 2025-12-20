<script setup>
import {computed, onMounted} from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/user.js'
import ContainerLayout from './layout/ContainerLayout.vue'
import SidebarLayout from './layout/SidebarLayout.vue'

const route = useRoute()
const userStore = useUserStore()

const layout = computed(() => {
    const layoutName = route.meta.layout || 'ContainerLayout'
    return layoutName === 'ContainerLayout' ? ContainerLayout : SidebarLayout
})

onMounted(async () => {
    await userStore.fetchUser()
})
</script>

<template>
    <component :is="layout">
        <router-view></router-view>
    </component>
</template>
