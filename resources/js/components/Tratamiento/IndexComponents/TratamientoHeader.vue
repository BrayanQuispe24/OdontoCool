<script setup lang="ts">
defineProps<{
    search: string;
    resultsCount: number;
    showForm: boolean;
    canCreate: boolean;
    canEdit: boolean;
    editMode: boolean;
    isDoctorOrAdmin: boolean;
}>();

const emit = defineEmits<{
    'update:search': [value: string];
    'toggle-form': [];
}>();
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Clínica
                </p>

                <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                    Gestión de Tratamientos
                </h1>

                <p class="mt-2 max-w-2xl text-[length:var(--font-sm)] leading-6 text-[var(--muted)]">
                    Registra tratamientos generales y asocia procedimientos específicos a cada diente.
                </p>
            </div>
            <div class="relative w-full md:max-w-md">
                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                    ⌕
                </span>

                <input
                    :value="search"
                    @input="emit('update:search', ($event.target as HTMLInputElement).value)"
                    type="text"
                    placeholder="Buscar por objetivo, obs, número de diente..."
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                />
            </div>

            <div v-if="!isDoctorOrAdmin"
                class="rounded-2xl bg-[var(--primary-soft)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                {{ resultsCount }} resultado(s)
            </div>

            <button type="button" @click="emit('toggle-form')"
                v-if="canCreate || (editMode && canEdit)"
                class="rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-[0_15px_35px_rgba(0,169,157,0.25)] transition hover:-translate-y-1 hover:bg-[var(--primary-hover)]">
                {{ showForm ? 'Cerrar Formulario' : '+ Nuevo Tratamiento' }}
            </button>
        </div>
    </div>
</template>
