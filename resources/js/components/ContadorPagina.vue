<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type PageProps = {
    contadorPaginaActual?: number;
    paginaActual?: string;
};

const page = usePage<PageProps>();

const contador = computed(() => page.props.contadorPaginaActual ?? 0);
const pagina = computed(() => {
    const actual = page.props.paginaActual ?? '';
    return actual.split('/').filter(Boolean).pop() ?? 'inicio';
});
</script>

<template>
    <div
        class="inline-flex h-10 max-h-10 items-center gap-2 rounded-full border bg-[var(--card)] px-3 shadow-sm"
        style="border-color: var(--border)"
    >
        <span
            class="hidden max-w-24 truncate whitespace-nowrap text-[length:var(--font-xs)] font-bold text-[var(--muted)] sm:block"
        >
            {{ pagina }}
        </span>

        <span
            class="inline-flex h-7 items-center rounded-full bg-emerald-100 px-3 text-[length:var(--font-xs)] font-black leading-none text-emerald-700 whitespace-nowrap"
        >
            {{ contador }} visitas
        </span>
    </div>
</template>