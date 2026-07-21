<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ServicioWithAsignaciones } from '@/types/Servicio';
import { Precio } from '@/types/Precio';
import ServicioShowHeader from '@/components/Servicio/ShowComponents/ServicioShowHeader.vue';
import ServicioShowDetails from '@/components/Servicio/ShowComponents/ServicioShowDetails.vue';
import ServicioShowPrecios from '@/components/Servicio/ShowComponents/ServicioShowPrecios.vue';
import ServicioShowEmptyState from '@/components/Servicio/ShowComponents/ServicioShowEmptyState.vue';

const props = defineProps<{
    servicio: ServicioWithAsignaciones | null;
    precios: Precio[];
    error?: string;
}>();

const form = useForm({});

const asignarForm = useForm({
    precio_id: '',
    fecha_inicio: '',
    fecha_fin: '',
    estado: 'ACTIVO',
});

const eliminarAsignacion = (asignacionId: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta asignación de precio del servicio?')) {
        form.delete(route('servicio.eliminarPrecio', {
            servicio_id: props.servicio?.id,
            asignacion_id: asignacionId
        }), {
            preserveScroll: true,
            onSuccess: () => {
                console.log(`Asignación con ID ${asignacionId} eliminada.`);
            },
            onError: (errors) => {
                console.error('Error al eliminar la asignación:', errors);
            },
        });
    }
};

const asignarPrecio = () => {
    asignarForm.post(route('servicio.asignarPrecio', props.servicio?.id), {
        preserveScroll: true,
        onSuccess: () => {
            asignarForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Detalle del servicio" />

    <AuthenticatedLayout>
        <template #title>
            Detalle del Servicio
        </template>

        <div
            v-if="error"
            class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-[length:var(--font-sm)] font-bold text-red-500"
        >
            Error: {{ error }}
        </div>

        <section
            v-if="servicio"
            class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300"
        >
            <!-- Header Card -->
            <ServicioShowHeader :nombre="servicio.nombre" />

            <!-- Service Details Grid -->
            <ServicioShowDetails :servicio="servicio" />

            <!-- Price Assignment Section -->
            <ServicioShowPrecios
                :asignaciones="servicio.asignaciones_precio"
                :precios="precios"
                :asignar-form="asignarForm"
                @submit-asignar="asignarPrecio"
                @eliminar-asignacion="eliminarAsignacion"
            />
        </section>

        <!-- Empty State in case no service is passed -->
        <section
            v-else
            class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300"
        >
            <ServicioShowEmptyState />
        </section>
    </AuthenticatedLayout>
</template>