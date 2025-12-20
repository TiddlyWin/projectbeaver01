<script setup>
import { onMounted } from 'vue';
import {useUserStore} from "@/stores/user.js";

const userStore = useUserStore();

</script>

<template>

    <section class="section is-flex-grow-1 paddingless">
        <div class="container column is-9">
                <div class="card">
                    <div class="card-content">
                        <div class="content">
                            <p>
                                Hey <strong>{{ userStore?.user.name || 'Pilot' }}!</strong>, welcome to DBM Tools,
                                your EVE Online corporation management suite. It may not look like much
                                now, but we're working to bring you new features and improvements to
                                enhance your experience. Stay tuned for updates!
                            </p>
                            <p>
                                Right now, we are actively developing a feature that will allow you track your
                                moon mining. If you have any suggestions or feedback, please don't hesitate to
                                reach out to us on Discord.
                                Your input is invaluable in helping us shape the future of DBM

                            </p>
                        </div>
                    </div>
                </div>

        </div>
    </section>

</template>

<style scoped>

</style>
