<script setup>
import { useRouter } from 'vue-router'
import { useUserStore } from "@/stores/user.js";
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
</script>

<template>
    <section class="dashboard-section">
        <div class="dashboard">

            <div v-if="userStore.loading" class="dashboard__loading">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <span>Loading...</span>
            </div>

            <div v-else class="dashboard__content">

                <div class="welcome-block">
                    <div class="welcome-block__text">
                        <h2 class="welcome-block__name">
                            Welcome, {{ userStore?.user.name || 'Pilot' }}!
                        </h2>
                        <p v-if="userStore.mainCharacter" class="welcome-block__main">
                            Main: <strong>{{ userStore.mainCharacter.name }}</strong>
                        </p>
                    </div>
                </div>

                <div class="characters-section">
                    <div class="characters-section__header">
                        <h3 class="characters-section__title">
                            Characters
                            <span class="characters-section__count">
                                {{ userStore.characters.length }}
                            </span>
                        </h3>
                        <button @click="linkMoreCharacters" class="btn btn--primary btn--sm">
                            <i class="fa-solid fa-plus"></i>
                            Link
                        </button>
                    </div>

                    <div v-if="userStore.characters.length === 0" class="characters-empty">
                        <i class="fa-solid fa-user-astronaut characters-empty__icon"></i>
                        <p>No characters linked yet.</p>
                        <button @click="linkMoreCharacters" class="btn btn--primary">
                            Link Your First Character
                        </button>
                    </div>

                    <div v-else class="characters-grid">
                        <div
                            v-for="character in userStore.characters"
                            :key="character.id"
                            class="character-card"
                            :class="{ 'character-card--main': character.id === userStore.mainCharacter?.id }"
                        >
                            <div class="character-card__info">
                                <img
                                    v-if="character.portrait_url"
                                    :src="character.portrait_url"
                                    :alt="character.name"
                                    class="character-card__portrait"
                                />
                                <div class="character-card__details">
                                    <h4 class="character-card__name">{{ character.name }}</h4>
                                    <span
                                        v-if="character.id === userStore.mainCharacter?.id"
                                        class="badge badge--main"
                                    >
                                        Main
                                    </span>
                                </div>
                            </div>

                            <button
                                v-if="character.id !== userStore.mainCharacter?.id"
                                @click="userStore.setMainCharacter(character.id)"
                                class="btn btn--ghost btn--sm"
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

<style lang="scss" scoped>
// ── Tokens ───────────────────────────────────────────────
$bottom-nav-height-mobile: 60px;
$bottom-nav-height-tablet: 50px;
$radius-sm:  0.375rem;
$radius-md:  0.5rem;
$radius-lg:  0.75rem;
$gap:        1rem;

// ── Layout fix: stop content hiding behind bottom nav ────
.dashboard-section {
    display: flex;
    flex-direction: column;
    flex-grow: 1;

    // This is the key fix — pad the bottom so content never
    // scrolls behind the fixed bottom nav
    padding-bottom: calc(#{$bottom-nav-height-mobile} + env(safe-area-inset-bottom));

    @media (min-width: 768px) {
        padding-bottom: calc(#{$bottom-nav-height-tablet} + env(safe-area-inset-bottom));
    }
}

.dashboard {
    padding: $gap;
    max-width: 960px;
    margin: 0 auto;
    width: 100%;

    @media (min-width: 768px) {
        padding: 1.5rem;
    }
}

// ── Loading ───────────────────────────────────────────────
.dashboard__loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 4rem 0;
    color: var(--bulma-text-weak);

    i { font-size: 1.5rem; }
}

// ── Welcome block ─────────────────────────────────────────
.welcome-block {
    margin-bottom: 1.25rem;

    &__name {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--bulma-text);
        margin: 0 0 0.25rem;
    }

    &__main {
        font-size: 0.9rem;
        color: var(--bulma-text-weak);
        margin: 0;
    }
}

// ── Characters section ────────────────────────────────────
.characters-section {
    background: var(--bulma-scheme-main);
    border: 1px solid var(--bulma-border);
    border-radius: $radius-lg;
    padding: $gap;

    &__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    &__title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--bulma-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    &__count {
        font-size: 0.75rem;
        font-weight: 600;
        background: var(--bulma-scheme-main-bis);
        color: var(--bulma-text-weak);
        border-radius: 20px;
        padding: 2px 8px;
    }
}

// ── Empty state ───────────────────────────────────────────
.characters-empty {
    text-align: center;
    padding: 2.5rem 1rem;
    color: var(--bulma-text-weak);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;

    &__icon {
        font-size: 2.5rem;
        opacity: 0.3;
    }

    p { margin: 0; font-size: 0.9rem; }
}

// ── Characters grid ───────────────────────────────────────
.characters-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: $gap;

    @media (min-width: 480px) {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

// ── Character card ────────────────────────────────────────
.character-card {
    border: 1px solid var(--bulma-border);
    border-radius: $radius-md;
    padding: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    transition: border-color 0.15s;

    &--main {
        border-color: var(--bulma-success);
        background: var(--bulma-success-light);
    }

    &__info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0; // prevents text overflow breaking flex
    }

    &__portrait {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    &__details {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    &__name {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--bulma-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

// ── Badge ─────────────────────────────────────────────────
.badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;

    &--main {
        background: var(--bulma-success);
        color: var(--bulma-success-invert);
    }
}

// ── Buttons ───────────────────────────────────────────────
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: none;
    border-radius: $radius-sm;
    cursor: pointer;
    font-weight: 500;
    padding: 0.55rem 1rem;
    font-size: 0.9rem;
    transition: background 0.15s, opacity 0.15s;
    text-decoration: none;
    white-space: nowrap;

    &--primary {
        background: var(--bulma-link);
        color: var(--bulma-link-invert);
        &:hover { opacity: 0.88; }
    }

    &--ghost {
        background: var(--bulma-scheme-main-bis);
        color: var(--bulma-text);
        border: 1px solid var(--bulma-border);
        &:hover { background: var(--bulma-scheme-main-ter); }
    }

    &--sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
    }
}
</style>
