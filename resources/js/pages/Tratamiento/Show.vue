<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Tratamiento } from '@/types/Tratamiento';
import { RecetaRecomendaciones } from '@/types/RecetaRecomendaciones';
import { SolicitudAnalisis } from '@/types/SolicitudAnalisi';
import { ServicioPrestado } from '@/types/ServicioPrestado';
import TratamientoShowHeader from '@/components/Tratamiento/ShowComponents/TratamientoShowHeader.vue';
import TratamientoShowDetails from '@/components/Tratamiento/ShowComponents/TratamientoShowDetails.vue';
import TratamientoShowPaciente from '@/components/Tratamiento/ShowComponents/TratamientoShowPaciente.vue';
import TratamientoShowDientes from '@/components/Tratamiento/ShowComponents/TratamientoShowDientes.vue';
import TratamientoShowReceta from '@/components/Tratamiento/ShowComponents/TratamientoShowReceta.vue';
import TratamientoShowServicios from '@/components/Tratamiento/ShowComponents/TratamientoShowServicios.vue';
import TratamientoShowAnalisis from '@/components/Tratamiento/ShowComponents/TratamientoShowAnalisis.vue';
import TratamientoShowEmptyState from '@/components/Tratamiento/ShowComponents/TratamientoShowEmptyState.vue';

const props = defineProps<{
    tratamiento: (Tratamiento & {
        receta_recomendaciones?: RecetaRecomendaciones;
        solicitud_analisis?: SolicitudAnalisis[];
        servicio_prestado?: ServicioPrestado[];
    }) | null;
    error?: string;
}>();

const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';

const formatPath = (path?: string) => {
    if (!path) return '';
    const base = publicBase.replace(/\/+$/, '');
    const cleanPath = path.replace('public/', '').replace(/^\/+/, '');
    return `${base}/storage/${cleanPath}`;
};
</script>

<template>
    <Head title="Detalle del Tratamiento" />

    <AuthenticatedLayout>
        <template #title> Detalle del Tratamiento </template>

        <div
            v-if="error"
            class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-[length:var(--font-sm)] font-bold text-red-500"
        >
            Error: {{ error }}
        </div>

        <section
            v-if="tratamiento"
            class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300"
        >
            <!-- Header Card -->
            <TratamientoShowHeader :objetivo="tratamiento.objetivo_tratamiento" />

            <!-- Detail Grid -->
            <TratamientoShowDetails :tratamiento="tratamiento" />

            <!-- Datos del Paciente Card -->
            <TratamientoShowPaciente :tratamiento="tratamiento" />

            <!-- Tooth Intervention Sheet -->
            <TratamientoShowDientes :tratamiento="tratamiento" />

            <!-- Recetas / Recomendaciones Section -->
            <TratamientoShowReceta :receta="tratamiento.receta_recomendaciones" />

            <!-- Servicios Clinicos Prestados Section -->
            <TratamientoShowServicios :servicios-prestados="tratamiento.servicio_prestado" />

            <!-- Solicitudes de Análisis Clinicos Section -->
            <TratamientoShowAnalisis
                :solicitudes="tratamiento.solicitud_analisis"
                :format-path="formatPath"
            />
        </section>

        <!-- Empty State in case no service is passed -->
        <section
            v-else
            class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300"
        >
            <TratamientoShowEmptyState />
        </section>
    </AuthenticatedLayout>
</template>