<script setup lang="ts">
const emit = defineEmits<{
    'go-to-form': [];
}>();
</script>

<template>
    <div
        class="flex flex-col items-center justify-center rounded-3xl border bg-[var(--card)] p-12 text-center shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <span class="text-[length:var(--font-3xl)]">📋</span>

        <h3 class="mt-4 text-[length:var(--font-xl)] font-black text-[var(--title)]">
            No se encontraron tratamientos
        </h3>

        <p class="mt-2 max-w-sm text-[length:var(--font-sm)] text-[var(--muted)]">
            Ajusta los términos de búsqueda o registra un nuevo
            tratamiento dental en el sistema.
        </p>

        <button
            type="button"
            @click="emit('go-to-form')"
            class="mt-6 rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-md transition hover:bg-[var(--primary-hover)]"
        >
            Registrar Primer Tratamiento
        </button>
    </div>
</template>
