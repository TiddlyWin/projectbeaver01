<script setup>
import { ref, computed } from "vue";
import dbmLogo from "../../assets/images/dbmLogo.png";
import axios from "axios";
import router from "@/routes.js";

const email = ref("");
const submitted = ref(false);

const validEmail = computed(() => {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email.value);
});

const emailError = computed(() => {
    if (submitted.value && !validEmail.value) {
        return "Please enter a valid email address.";
    }
    return "";
});

function handleSubmit(event) {
    event.preventDefault();
    submitted.value = true;

    if (validEmail.value) {
       axios.post('/api/account/register', {
              email: email.value
       }).then(() => {
           router.push('/dashboard')
       })
    }
}

</script>
<template>
    <section class="hero is-fullheight">
        <div class="hero-body has-text-centered">
            <div class="container is-flex is-justify-content-center">
                <div class="login box">
                    <form class="" @submit="handleSubmit">
                        <div class="field is-flex is-justify-content-center">
                            <img :src="dbmLogo" class="image is-128x128" alt="Corp logo for Drunken Beaver Mining"/>
                        </div>
<!--                        Need a input for capturing an email-->
                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control">
                                <input
                                    class="input"
                                    :class="{ 'is-danger': submitted && !validEmail }"
                                    type="email"
                                    v-model="email"
                                    placeholder="you@example.com"
                                    aria-label="Email address"
                                    required
                                />
                            </div>
                            <p class="help is-danger" v-if="emailError">{{ emailError }}</p>
                        </div>

                        <div class="field is-grouped is-justify-content-center">
                            <div class="control">
                                <button
                                    type="submit"
                                    class="button is-primary"
                                    :disabled="submitted && !validEmail"
                                >
                                    Register with Email
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
