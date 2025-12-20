<script setup>
import {useTheme} from '@/composables/useTheme.js'

import dbmLogo from '../../../assets/images/dbmLogo.png'
import {useUserStore} from "@/stores/user.js";

const {mode, setTheme } = useTheme()
const userStore = useUserStore()

</script>

<template>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand m-1">
            <img :src="dbmLogo" class="image is-64x64" alt="">
            <a class="navbar-item title">DBM Tools</a>
        </div>
        <div v-if="!userStore.isAuthenticated" class="navbar-menu">
                <RouterLink class="navbar-item" to="/">Home</RouterLink>
        </div>
        <div v-else class="navbar-menu">
            <RouterLink class="navbar-item" to="/dashboard">Dashboard</RouterLink>
            <RouterLink class="navbar-item" to="/dashboard/profile">Profile</RouterLink>
<!--            <RouterLink class="navbar-item" to="/characters">Characters Scopes</RouterLink>-->
<!--            <RouterLink class="navbar-item" to="/settings">Settings</RouterLink>-->
        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <button
                        class="button"
                        :class="{'is-light': true, 'is-selected': mode === 'auto'}"
                        aria-pressed="true"
                        @click="setTheme('auto')"
                        title="Follow system setting"
                    >
                        Auto
                    </button>
                    <button
                        class="button"
                        :class="{'is-primary': true, 'is-selected': mode === 'light'}"
                        @click="setTheme('light')"
                        title="Force light mode"
                    >
                        Light
                    </button>
                    <button
                        class="button"
                        :class="{'is-dark': true, 'is-selected': mode === 'dark'}"
                        @click="setTheme('dark')"
                        title="Force dark mode"
                    >
                        Dark
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

<style scoped>
/* Optional: make the selected theme button visually distinct */
.button.is-selected {
    outline: 2px solid currentColor;
}
</style>
