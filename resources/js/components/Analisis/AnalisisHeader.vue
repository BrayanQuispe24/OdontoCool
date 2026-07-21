<script setup lang="ts">
defineProps<{
    search: string;
    activeTab: 'catalogo' | 'solicitudes' | 'resultados';
    showCatalogForm: boolean;
    showSolicitudForm: boolean;
    catalogCount: number;
    solicitudCount: number;
    resultadoCount: number;
    solicitudesTotal: number;
    resultadosTotal: number;
    tieneRolPaciente: boolean;
    tieneRolDoctorOrAdmin: boolean;
    tienePermisoStoreAnalisis: boolean;
    tienePermisoStoreSolicitud: boolean;
}>();

const emit = defineEmits<{
    'update:search': [value: string];
    selectTab: [tab: 'catalogo' | 'solicitudes' | 'resultados'];
    toggleCatalogForm: [];
    toggleSolicitudForm: [];
}>();
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Laboratorio Clínico
                </p>

                <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                    {{ tieneRolPaciente ? 'Mis análisis' : 'Laboratorio Clínico' }}
                </h1>

                <p class="mt-2 max-w-2xl text-[length:var(--font-sm)] leading-6 text-[var(--muted)]">
                    {{
                        tieneRolPaciente ? 'Consulta tus solicitudes de análisis y resultados de laboratorio.' :
                            'Gestiona el catálogo de análisis, solicitudes y resultados de laboratorio clínico.'
                    }}
                </p>
            </div>

            <!-- SEARCH REGION -->
            <div class="relative w-full md:max-w-md">
                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                    ⌕
                </span>

                <input :value="search"
                    @input="emit('update:search', ($event.target as HTMLInputElement).value)"
                    type="text"
                    :placeholder="activeTab === 'catalogo'
                        ? 'Buscar por nombre o descripción...'
                        : activeTab === 'solicitudes'
                            ? 'Buscar por análisis, tratamiento o estado...'
                            : 'Buscar resultado por hallazgo u observaciones...'
                    "
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />
            </div>

            <div class="rounded-2xl border bg-[var(--primary-soft)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)]"
                v-if="tienePermisoStoreAnalisis"
                style="border-color: var(--border)">
                {{
                    activeTab === 'catalogo'
                        ? catalogCount
                        : activeTab === 'solicitudes'
                            ? solicitudCount
                            : resultadoCount
                }}
                resultado(s)
            </div>

            <div class="flex flex-wrap gap-2">
                <button v-if="activeTab === 'catalogo' && tienePermisoStoreAnalisis"
                    type="button"
                    @click="emit('toggleCatalogForm')"
                    class="rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-md transition hover:-translate-y-0.5 hover:bg-[var(--primary-hover)]">
                    {{ showCatalogForm ? 'Cerrar Formulario' : '+ Nuevo Tipo Análisis' }}
                </button>

                <button v-if="activeTab === 'solicitudes' && tienePermisoStoreSolicitud"
                    type="button"
                    @click="emit('toggleSolicitudForm')"
                    class="rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-md transition hover:-translate-y-0.5 hover:bg-[var(--primary-hover)]">
                    {{ showSolicitudForm ? 'Cerrar Formulario' : '+ Emitir Solicitud' }}
                </button>
            </div>
        </div>

        <!-- Tabs navigation -->
        <div class="mt-8 flex flex-wrap border-b" style="border-color: var(--border)">
            <button type="button"
                @click="emit('selectTab', 'catalogo')"
                class="border-b-2 px-5 py-3 text-[length:var(--font-sm)] font-black transition"
                :class="activeTab === 'catalogo'
                    ? 'border-[var(--primary)] text-[var(--primary)]'
                    : 'border-transparent text-[var(--muted)] hover:text-[var(--title)]'
                ">
                🔬 Catálogo de Análisis
            </button>

            <button type="button"
                @click="emit('selectTab', 'solicitudes')"
                v-if="tieneRolDoctorOrAdmin"
                class="flex items-center gap-1.5 border-b-2 px-5 py-3 text-[length:var(--font-sm)] font-black transition"
                :class="activeTab === 'solicitudes'
                    ? 'border-[var(--primary)] text-[var(--primary)]'
                    : 'border-transparent text-[var(--muted)] hover:text-[var(--title)]'
                ">
                📋 Solicitudes Emitidas
                <span class="rounded border bg-[var(--section-soft)] px-1.5 py-0.5 text-[length:var(--font-xs)] font-bold text-[var(--muted)]"
                    style="border-color: var(--border)">
                    {{ solicitudesTotal }}
                </span>
            </button>

            <button type="button"
                @click="emit('selectTab', 'resultados')"
                v-if="tieneRolDoctorOrAdmin"
                class="flex items-center gap-1.5 border-b-2 px-5 py-3 text-[length:var(--font-sm)] font-black transition"
                :class="activeTab === 'resultados'
                    ? 'border-[var(--primary)] text-[var(--primary)]'
                    : 'border-transparent text-[var(--muted)] hover:text-[var(--title)]'
                ">
                ✅ Resultados Cargados
                <span class="rounded border bg-[var(--primary-soft)] px-1.5 py-0.5 text-[length:var(--font-xs)] font-bold text-[var(--primary)]"
                    style="border-color: var(--border)">
                    {{ resultadosTotal }}
                </span>
            </button>
        </div>
    </div>
</template>
