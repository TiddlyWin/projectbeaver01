<script setup>
import {onMounted} from 'vue'
import {useRouter} from 'vue-router'
import {useUserStore} from "@/stores/user.js";
import axios from "axios";

const router = useRouter()
const userStore = useUserStore()

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

onMounted(async () => {
    await userStore.fetchUser()
})

</script>

<template>
    <section class="is-flex-grow-1">
        <div class="dashboard">
            <div class="header">
                <h1>Dashboard</h1>
                <button @click="logout" class="logout-btn">Logout</button>
            </div>

            <div v-if="userStore.loading" class="loading">
                Loading...
            </div>

            <div v-else class="content">
                <div class="welcome">
                    <h2>Welcome, {{ userStore?.user || 'Pilot' }}!</h2>
                    <p v-if="userStore.mainCharacter">Main Character: <strong>{{
                            userStore.mainCharacter.name
                        }}</strong></p>
                </div>

                <div class="characters-section">
                    <div class="section-header">
                        <h3>Your Characters ({{ userStore.characters.length }})</h3>
                        <button @click="linkMoreCharacters" class="link-btn">
                            Link More Characters
                        </button>
                    </div>

                    <div v-if="userStore.characters.length === 0" class="no-characters">
                        <p>No characters linked yet.</p>
                        <button @click="linkMoreCharacters" class="link-btn primary">
                            Link Your First Character
                        </button>
                    </div>

                    <div v-else class="characters-grid">
                        <div
                            v-for="character in userStore.characters"
                            :key="character.id"
                            class="character-card"
                            :class="{ 'main-character': character.id === userStore.mainCharacter?.id }"
                        >
                            <!-- Character card content -->
                            <div class="character-info">
                                <img
                                    v-if="character.portrait_url"
                                    :src="character.portrait_url"
                                    :alt="character.name"
                                    class="character-portrait"
                                />
                                <div class="character-details">
                                    <h4>{{ character.name }}</h4>
                                    <p v-if="character.id === userStore.mainCharacter?.id" class="main-badge">
                                        Main Character
                                    </p>
                                </div>
                            </div>

                            <button
                                v-if="character.id !== userStore.mainCharacter?.id"
                                @click="userStore.setMainCharacter(character.id)"
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
