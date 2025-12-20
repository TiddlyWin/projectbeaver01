<script setup>
import { ref, onMounted } from 'vue';
import dbmLogo from "../../../assets/images/dbmLogo.png";

const errorMessage = ref('');
const hasError = ref(false);

async function login() {
    window.location.href = '/auth/eve/redirect';
}

onMounted(() => {
    // Parse URL parameters
    const params = new URLSearchParams(window.location.search);
    if (params.has('error')) {
        hasError.value = true;
        errorMessage.value = params.get('message') || 'Authentication failed';

        // Optional: Clear URL parameters after reading them
        if (window.history.replaceState) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
});
</script>

<template>
    <section class="hero is-fullheight">
        <div class="hero-body has-text-centered">
            <div class="container is-flex is-justify-content-center">
                <div class="login">
                    <!-- Error notification -->
                    <div v-if="hasError" class="notification is-danger mb-4">
                        {{ errorMessage }}
                    </div>

                    <form class="">
                        <div class="field is-flex is-justify-content-center">
                            <img :src="dbmLogo" class="image is-128x128" alt="Corp logo for Drunken Beaver Mining"/>
                        </div>
                        <button type="button" class="button is-primary" @click="login" rel="nofollow">
                            Sign in with EVE Online
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
