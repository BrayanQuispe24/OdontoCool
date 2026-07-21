<script setup lang="ts">
defineProps<{
    activeTab: 'catalogo' | 'solicitudes' | 'resultados';
}>();
</script>

<template>
    <div class="rounded-3xl border border-dashed bg-[var(--card)] p-12 text-center shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-[length:var(--font-3xl)]">
            🔎
        </div>

        <h2 class="mt-5 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
            No se encontraron registros
        </h2>

        <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
            Ajusta los términos de búsqueda o registra nuevos registros para este módulo de laboratorio.
        </p>
    </div>
</template>
