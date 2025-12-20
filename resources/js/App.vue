<script setup>
import {computed, onMounted} from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/user.js'
import bg from '../assets/images/background.png'

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
    <div class="app-shell is-flex is-flex-direction-column" :style="{ backgroundImage: `url(${bg})` }">
        <component :is="layout">
            <router-view></router-view>
        </component>
    </div>
</template>

<style>
.app-shell {
    height: 100vh;
    overflow: hidden;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
</style>
