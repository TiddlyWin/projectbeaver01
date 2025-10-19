<script setup>
import dbmLogo from '../../assets/images/dbmLogo.png'

async function login() {
    window.location.href = '/auth/eve/redirect';
}

</script>

<template>
    <section class="hero is-fullheight">
        <div class="hero-body has-text-centered">
            <div class="container is-flex is-justify-content-center">
                <div class="login">
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

<style scoped>
/* Vue-scoped tweaks (optional) */
.container {
    max-width: 400px;
}
</style>
