<script setup>
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted } from 'vue';

const owner = ref(String);

onMounted(() => {
    axios
        .get('/owner')
        .then((response) => {
            owner.value = response.data.owner;
        })
        .catch(error => console.error(error));
});
</script>

<template>

    <Head title="Hallo vriend" />
    <main
        class="flex flex-col h-full min-h-screen bg-[url('/images/Welcomepagebg.png')] items-center justify-around px-6 text-slate-100">
        <div>
            <h1 class="text-8xl font-bold">Welkom vriend (of niet)</h1>
            <p class="mt-4 text-3xl text-slate-100/40 font-semibold">
                Bewijs dat je een vriend bent.
            </p>
        </div>
        <div class="w-1/3 flex flex-col items-start justify-center">
            <p class="text-lg text-slate-100 mb-4">
                Tip: welke quote staat bekend bij de vakantie?
            </p>
            <input type="password" placeholder="Vul hier het wachtwoord in"
            class="text-xl px-4 py-2 text-center rounded-3xl w-full text-slate-900 outline-none border-none focus:ring-0 focus:outline-none" />
        </div>
        <p class="text-sm text-slate-black">
            Gemaakt en onderhouden door {{ owner }}, 2026
        </p>
    </main>
</template>
