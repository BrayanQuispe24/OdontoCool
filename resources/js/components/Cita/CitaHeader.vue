<script setup lang="ts">
defineProps<{
    searchPaciente: string;
    estadoFiltro: string;
    showForm: boolean;
    estadosDisponibles: string[];
    citasFiltradasCount: number;
    citasTotalCount: number;
    tieneRolDoctorOrPaciente: boolean;
    puedeBuscarPaciente: boolean;
    tienePermisoStoreOrUpdate: boolean;
}>();

const emit = defineEmits<{
    'update:searchPaciente': [value: string];
    'update:estadoFiltro': [value: string];
    toggleForm: [];
    limpiarFiltros: [];
}>();
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <div class="grid gap-6 xl:grid-cols-[1fr_auto] xl:items-center">
            <div>
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Agenda clínica
                </p>

                <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                    {{ tieneRolDoctorOrPaciente ? 'Mis citas' : 'Citas médicas' }}
                </h1>

                <p class="mt-2 max-w-2xl text-[length:var(--font-sm)] leading-6 text-[var(--muted)]">
                    {{ tieneRolDoctorOrPaciente ? 'Aquí puedes ver tus citas médicas, filtrar por estado y buscar por paciente.' : 'Aquí puedes ver todas las citas médicas, filtrar por estado y buscar por paciente.' }}
                </p>
            </div>

            <div class="flex w-full flex-col gap-3 xl:w-auto">
                <div class="flex w-full flex-col gap-3 md:flex-row xl:w-auto xl:items-center">
                    <div v-if="puedeBuscarPaciente"
                        class="relative w-full md:min-w-[320px] xl:min-w-[360px]">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                            ⌕
                        </span>

                        <input :value="searchPaciente"
                            @input="emit('update:searchPaciente', ($event.target as HTMLInputElement).value)"
                            type="text"
                            placeholder="Buscar paciente..."
                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />
                    </div>

                    <select :value="estadoFiltro"
                        @change="emit('update:estadoFiltro', ($event.target as HTMLSelectElement).value)"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-7 py-4 text-[length:var(--font-sm)] font-bold text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4 md:w-[220px]"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                        <option value="">Todos los estados</option>
                        <option v-for="estado in estadosDisponibles" :key="estado" :value="estado">
                            {{ estado }}
                        </option>
                    </select>

                    <button v-if="tienePermisoStoreOrUpdate"
                        type="button"
                        @click="emit('toggleForm')"
                        class="rounded-2xl bg-[var(--primary)] px-6 py-4 text-[length:var(--font-sm)] font-black text-white shadow-[0_15px_35px_rgba(0,169,157,0.25)] transition hover:-translate-y-1 hover:bg-[var(--primary-hover)] md:whitespace-nowrap">
                        {{ showForm ? 'Cerrar formulario' : '+ Nueva cita' }}
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-3">
            <div class="rounded-2xl bg-[var(--primary-soft)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                {{ citasFiltradasCount }} resultado(s)
            </div>

            <div class="rounded-2xl bg-[var(--section-soft)] px-5 py-3 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                Total registrado: {{ citasTotalCount }}
            </div>

            <div v-if="estadoFiltro"
                class="rounded-2xl bg-[var(--section-soft)] px-5 py-3 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                Estado: {{ estadoFiltro }}
            </div>

            <button v-if="searchPaciente || estadoFiltro"
                type="button"
                @click="emit('limpiarFiltros')"
                class="rounded-2xl border bg-[var(--card)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)]"
                style="border-color: var(--border)">
                Limpiar filtros
            </button>
        </div>
    </div>
</template>
