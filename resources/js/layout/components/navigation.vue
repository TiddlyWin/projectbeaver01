<template>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand m-1">
            <img :src="dbmLogo" class="image is-64x64" alt="">
            <a class="navbar-item title">DBM Tools</a>
        </div>
        <div class="navbar-menu">
            <RouterLink class="navbar-item" to="/">Home</RouterLink>
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

<script setup>
import {useTheme} from '@/composables/useTheme.js'

import dbmLogo from '../../../assets/images/dbmLogo.png'

const {mode, setTheme } = useTheme()
</script>

<style scoped>
/* Optional: make the selected theme button visually distinct */
.button.is-selected {
    outline: 2px solid currentColor;
}
</style>
