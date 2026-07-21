<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="rounded-3xl border border-dashed bg-[var(--card)] p-10 text-center shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-[length:var(--font-3xl)]">
            ⚠️
        </div>

        <h2 class="mt-5 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
            No se encontró el tratamiento
        </h2>

        <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
            El tratamiento que buscas no existe o fue eliminado.
        </p>

        <Link
            :href="route('tratamiento.index')"
            class="mt-6 inline-flex rounded-2xl bg-[var(--text)] px-6 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90"
        >
            Regresar al catálogo
        </Link>
    </div>
</template>
