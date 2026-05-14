<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Hello World" />
    <main class="flex min-h-screen items-center justify-center bg-slate-950 px-6 text-slate-100">
        <div class="max-w-xl rounded-3xl border border-white/10 bg-white/5 px-10 py-12 text-center shadow-2xl shadow-black/30 backdrop-blur">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300">
                Laravel + Vue
            </p>
            <h1 class="mt-5 text-5xl font-semibold tracking-tight sm:text-6xl">
                Hello, world.
            </h1>
            <p class="mt-4 text-base leading-7 text-slate-300">
                This is a minimal Inertia welcome page. The auth scaffold and demo starter pages have been removed so you can build your own flow from here.
            </p>
        </div>
    </main>
</template>
