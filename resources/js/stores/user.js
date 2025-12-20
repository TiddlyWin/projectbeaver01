import { ref, computed } from 'vue'
import { defineStore } from "pinia";
import axios from "axios";

axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

export const useUserStore = defineStore('user', () => {
    const authBootstrapped = ref(false)
    const isAuthenticated = ref(false)
    const user = ref(null)
    const characters = ref([])
    const mainCharacter = ref(null)
    const loading = ref(false)
    const error = ref(null)

    async function authBootstrap() {
        if (authBootstrapped.value) return

        try {
            await axios.get('/sanctum/csrf-cookie')
            await fetchUser()
        } catch (error) {
            isAuthenticated.value = false
        }
        finally {
            authBootstrapped.value = true
        }
    }

    // Fetch user data from API
    async function fetchUser() {
        loading.value = true
        error.value = null

        try {
            const response = await axios.get('/api/me')
            user.value = response.data
            characters.value = response.data.characters
            mainCharacter.value = response.data.main_character || null

            isAuthenticated.value = true
        } catch (err) {
            error.value = err
            isAuthenticated.value = false
        } finally {
            loading.value = false
        }
    }

    // Set main character
    async function setMainCharacter(characterId) {
        loading.value = true
        error.value = null

        try {
            const response = await axios.post(`/api/characters/${characterId}/set-main`)
            characters.value = response.data.characters
            mainCharacter.value = response.data.main_character || null
        } catch (err) {
            error.value = err
            console.error('Failed to set main character:', err)
        } finally {
            loading.value = false
        }
    }

    return {
        isAuthenticated: computed(() => isAuthenticated.value),
        user: computed(() => user.value),
        characters: computed(() => characters.value),
        mainCharacter: computed(() => mainCharacter.value),
        loading: computed(() => loading.value),
        error: computed(() => error.value),
        authBootstrap,
        fetchUser,
        setMainCharacter
    }
})
