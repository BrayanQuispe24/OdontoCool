<script setup lang="ts">
defineProps<{
    search: string;
    resultsCount: number;
    showForm: boolean;
    isPaciente: boolean;
    canCreate: boolean;
    canEdit: boolean;
}>();

const emit = defineEmits<{
    'update:search': [value: string];
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
                    Catálogo
                </p>

                <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                    {{ isPaciente ? 'Servicios disponibles' : 'Gestionar servicios' }}
                </h1>

                <p class="mt-2 max-w-2xl text-[length:var(--font-sm)] leading-6 text-[var(--muted)]">
                    {{ isPaciente ? 'Explora los servicios que ofrecemos y encuentra el tratamiento adecuado para ti.' : 'Aquí puedes registrar, editar o eliminar servicios, así como asignarles precios y vigencias.' }}
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
                    placeholder="Buscar servicio por nombre o tipo..."
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                />
            </div>

            <div class="rounded-2xl bg-[var(--primary-soft)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)]" v-if="!canCreate || !canEdit">
                {{ resultsCount }} resultado(s)
            </div>

            <button v-if="canCreate"
                type="button"
                @click="emit('toggle-form')"
                class="rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-[0_15px_35px_rgba(0,169,157,0.25)] transition hover:-translate-y-1 hover:bg-[var(--primary-hover)]"
            >
                {{ showForm ? 'Cerrar formulario' : '+ Nuevo servicio' }}
            </button>
        </div>
    </div>
</template>
