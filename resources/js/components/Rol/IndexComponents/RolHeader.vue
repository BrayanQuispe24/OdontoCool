<script setup lang="ts">
defineProps<{
    showForm: boolean;
}>();

const emit = defineEmits<{
    'toggle-form': [];
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Administración
                </p>

                <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                    Gestión de roles
                </h1>

                <p class="mt-2 max-w-2xl text-[length:var(--font-sm)] leading-6 text-[var(--muted)]">
                    Busca, visualiza y registra roles del sistema OdontoCool.
                </p>
            </div>

            <button
                type="button"
                @click="emit('toggle-form')"
                class="rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-[0_15px_35px_rgba(0,169,157,0.25)] transition hover:-translate-y-1 hover:bg-[var(--primary-hover)]"
            >
                {{ showForm ? 'Cerrar formulario' : '+ Nuevo rol' }}
            </button>
        </div>
    </div>
</template>
