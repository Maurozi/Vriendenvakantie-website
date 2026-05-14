<script setup>
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const password = ref('');
const loading = ref(false);
const error = ref(null);

async function authenticate() {
    error.value = null;
    loading.value = true;
    try {
        const res = await axios.post('/authenticate', { key: password.value });
        if (res.data?.access === true) {
            router.visit('/home');
        } else {
            error.value = res.data?.message || 'Authenticatie mislukt';
        }
    } catch (e) {
        error.value = e.response?.data?.message || e.message || 'Netwerkfout';
    } finally {
        loading.value = false;
    }
}

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
        <form @submit.prevent="authenticate" class="w-full max-w-2xl mx-auto text-left px-6 flex flex-col items-center">
            <p class="text-lg text-slate-100 mb-4">Tip: welke quote staat bekend bij de vakantie?</p>

            <input v-model="password" @keyup.enter="authenticate" type="password"
                placeholder="Vul hier het wachtwoord in"
                class="text-xl px-4 py-2 text-center rounded-3xl w-full text-slate-900 outline-none border-none focus:ring-0 focus:outline-none" />

            <div class="mt-3">
                <button type="submit" class="px-4 py-2 rounded-3xl hover:bg-emerald-600 transition-colors text-white" :disabled="loading">
                    {{ loading ? 'Verifiëren...' : 'Verifieer' }}
                </button>
            </div>
        </form>
        <p class="text-sm text-slate-black">
            Gemaakt en onderhouden door Mauro, 2026
        </p>
    </main>
</template>
