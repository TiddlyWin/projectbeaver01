import { ref, onMounted, onUnmounted } from 'vue'

const KEY = 'theme' // 'light' | 'dark' | null (auto)
const mode = ref('auto') // reactive: 'light' | 'dark' | 'auto'
let mql // media query list for prefers-color-scheme

function apply(theme) {
    const html = document.documentElement
    if (!theme) {
        html.removeAttribute('data-theme') // lets Bulma use prefers-color-scheme
    } else {
        html.setAttribute('data-theme', theme)
    }
}

function readInitial() {
    const saved = localStorage.getItem(KEY)
    if (saved === 'light' || saved === 'dark') {
        mode.value = saved
        apply(saved)
    } else {
        mode.value = 'auto'
        apply(null)
    }
}

function setTheme(next /* 'light' | 'dark' | 'auto' */) {
    if (next === 'auto') {
        localStorage.removeItem(KEY)
        mode.value = 'auto'
        apply(null)
    } else if (next === 'light' || next === 'dark') {
        localStorage.setItem(KEY, next)
        mode.value = next
        apply(next)
    }
}

function toggleTheme() {
    const effective =
        mode.value === 'auto'
            ? (mql?.matches ? 'dark' : 'light')
            : mode.value
    setTheme(effective === 'dark' ? 'light' : 'dark')
}

export function useTheme() {
    onMounted(() => {
        readInitial()
        mql = window.matchMedia('(prefers-color-scheme: dark)')
        const onChange = () => {
            // only react to OS changes when in auto mode
            if (mode.value === 'auto') apply(null)
        }
        mql.addEventListener('change', onChange)
        // store for cleanup
        useTheme._onChange = onChange
    })

    onUnmounted(() => {
        if (mql && useTheme._onChange) {
            mql.removeEventListener('change', useTheme._onChange)
        }
    })

    return { mode, setTheme, toggleTheme }
}
