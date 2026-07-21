<script setup lang="ts">
defineProps<{
    showForm: boolean;
    pacientesDisponiblesCount: number;
}>();

const emit = defineEmits<{
    'register-first': [];
}>();
</script>

<template>
    <!-- Estado Vacío -->
    <div class="flex flex-col items-center justify-center rounded-3xl border bg-[var(--card)] p-12 text-center shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 border-[var(--border)]">
        <span class="text-4xl mb-4">📋</span>

        <h3 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
            No se encontraron historiales clínicos
        </h3>

        <p class="mt-2 max-w-sm text-[length:var(--font-sm)] text-[var(--muted)]">
            Ajusta los términos de búsqueda o registra un nuevo historial clínico para un paciente.
        </p>

        <button v-if="!showForm && pacientesDisponiblesCount > 0" type="button" @click="emit('register-first')"
            class="mt-6 rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-lg shadow-teal-200 hover:shadow-none dark:shadow-none transition hover:bg-[var(--primary-hover)] active:scale-95 duration-150">
            Registrar Primer Historial
        </button>
    </div>
</template>
