<script setup>
import {ref, onMounted, watchEffect} from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { fetch } from "@/composables/fetch.js";

/**
 * @typedef {Object} Character
 * @property {number} id
 * @property {string} name
 * @property {string} [portrait_url]
 */

/**
 * @typedef {Object} UserData
 * @property {string} name
 * @property {Character[]} characters
 * @property {Character|null} main_character
 */


// Configure axios to include CSRF token
axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

const router = useRouter()
const user = ref(null)
const characters = ref([])
const mainCharacter = ref(null)
const loading = ref(true)


async function fetchUserData() {
    loading.value = true
    const { data, error } = fetch('/api/me')

    watchEffect(() => {
        if (data.value) {
            user.value = data.value
            characters.value = data.value.characters || []
            mainCharacter.value = data.value.main_character
            loading.value = false
        }

        if (error.value) {
            console.error('Failed to fetch user data:', error.value)
            loading.value = false
        }
    })
}



async function setMainCharacter(characterId) {
    const { data, error } = fetch(`/api/characters/${characterId}/set-main`, {
        method: 'POST'
    })

    watchEffect(() => {
        if (data.value) {
            characters.value = data.value.characters
            mainCharacter.value = data.value.main_character
        }

        if (error.value) {
            console.error('Failed to set main character:', error.value)
        }
    })
}


function linkMoreCharacters() {
    window.location.href = '/auth/eve/redirect'
}

async function logout() {
    try {
        await axios.post('/auth/logout')
        await router.push('/logout')
    } catch (error) {
        console.error('Logout failed:', error)
    }
}

onMounted(() => {
    fetchUserData()
})
</script>

<template>
   <section>
       <div class="dashboard">
           <div class="header">
               <h1>Dashboard</h1>
               <button @click="logout" class="logout-btn">Logout</button>
           </div>

           <div v-if="loading" class="loading">
               Loading...
           </div>

           <div v-else class="content">
               <div class="welcome">
                   <h2>Welcome, {{ user?.name || 'Pilot' }}!</h2>
                   <p v-if="mainCharacter">Main Character: <strong>{{ mainCharacter.name }}</strong></p>
               </div>

               <div class="characters-section">
                   <div class="section-header">
                       <h3>Your Characters ({{ characters.length }})</h3>
                       <button @click="linkMoreCharacters" class="link-btn">
                           Link More Characters
                       </button>
                   </div>

                   <div v-if="characters.length === 0" class="no-characters">
                       <p>No characters linked yet.</p>
                       <button @click="linkMoreCharacters" class="link-btn primary">
                           Link Your First Character
                       </button>
                   </div>

                   <div v-else class="characters-grid">
                       <div
                           v-for="character in characters"
                           :key="character.id"
                           class="character-card"
                           :class="{ 'main-character': character.id === mainCharacter?.id }"
                       >
                           <div class="character-info">
                               <img
                                   v-if="character.portrait_url"
                                   :src="character.portrait_url"
                                   :alt="character.name"
                                   class="character-portrait"
                               />
                               <div class="character-details">
                                   <h4>{{ character.name }}</h4>
                                   <p v-if="character.id === mainCharacter?.id" class="main-badge">
                                       Main Character
                                   </p>
                               </div>
                           </div>

                           <button
                               v-if="character.id !== mainCharacter?.id"
                               @click="setMainCharacter(character.id)"
                               class="set-main-btn"
                           >
                               Set as Main
                           </button>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </section>
</template>

<style scoped>
.dashboard {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.logout-btn {
    background: #ef4444;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    font-weight: 500;
}

.logout-btn:hover {
    background: #dc2626;
}

.loading {
    text-align: center;
    padding: 2rem;
    font-size: 1.125rem;
}

.welcome {
    margin-bottom: 2rem;
}

.welcome h2 {
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.welcome p {
    color: #6b7280;
    font-size: 1.125rem;
}

.characters-section {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-header h3 {
    margin: 0;
    color: #1f2937;
}

.link-btn {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
}

.link-btn:hover {
    background: #2563eb;
}

.link-btn.primary {
    background: #10b981;
}

.link-btn.primary:hover {
    background: #059669;
}

.no-characters {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.characters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}

.character-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.character-card.main-character {
    border-color: #10b981;
    background: #f0fdf4;
}

.character-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.character-portrait {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
}

.character-details h4 {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
}

.main-badge {
    background: #10b981;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    margin: 0;
    display: inline-block;
}

.set-main-btn {
    background: #6b7280;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    font-weight: 500;
    align-self: flex-start;
}

.set-main-btn:hover {
    background: #4b5563;
}
</style>
