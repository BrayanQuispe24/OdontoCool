<script setup lang="ts">
import { Servicio } from '@/types/Servicio';
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { PageProps } from '@/types';
import ServicioHeader from '@/components/Servicio/IndexComponents/ServicioHeader.vue';
import ServicioForm from '@/components/Servicio/IndexComponents/ServicioForm.vue';
import ServicioCardList from '@/components/Servicio/IndexComponents/ServicioCardList.vue';
import ServicioEmptyState from '@/components/Servicio/IndexComponents/ServicioEmptyState.vue';

const props = defineProps<{
    servicios: Array<Servicio>;
}>();
const page = usePage<PageProps>();

const authUser = computed(() => page.props.auth.user);
const permisos = computed<string[]>(() => page.props.auth.permisos ?? []);
const tieneRol = (rol: string) => {
    return authUser.value?.rol?.nombre === rol;
};
const tienePermiso = (permiso: string): boolean => {
    return permisos.value.includes(permiso);
};

const search = ref('');
const showForm = ref(false);
const editMode = ref(false);
const selectedServicioId = ref<number | null>(null);
const formSection = ref<HTMLElement | null>(null);

const editServicio = (servicio: Servicio) => {
    editMode.value = true;
    selectedServicioId.value = servicio.id;
    showForm.value = true;

    form.nombre = servicio.nombre;
    form.descripcion = servicio.descripcion;
    form.tipo = servicio.tipo;
    form.estado = servicio.estado;

    const activeAsig = servicio.asignaciones_precio?.find(a => a.estado === 'ACTIVO');
    if (activeAsig) {
        form.monto = activeAsig.precio?.monto !== undefined ? String(activeAsig.precio.monto) : '';
        form.moneda = activeAsig.precio?.moneda || 'BOB';
        form.fecha_inicio = activeAsig.fecha_inicio || new Date().toISOString().split('T')[0];
        form.fecha_fin = activeAsig.fecha_fin || '2035-12-31';
    } else {
        form.monto = '';
        form.moneda = 'BOB';
        form.fecha_inicio = new Date().toISOString().split('T')[0];
        form.fecha_fin = '2035-12-31';
    }

    goToForm();
};

const form = useForm({
    nombre: '',
    descripcion: '',
    tipo: '',
    estado: 'ACTIVO',
    monto: '',
    moneda: 'BOB',
    fecha_inicio: new Date().toISOString().split('T')[0],
    fecha_fin: '2035-12-31',
});

const serviciosFiltrados = computed(() => {
    return props.servicios.filter(
        (servicio) =>
            servicio.nombre
                .toLowerCase()
                .includes(search.value.toLowerCase()) ||
            servicio.tipo.toLowerCase().includes(search.value.toLowerCase()),
    );
});

const submit = () => {
    if (editMode.value && selectedServicioId.value) {
        form.put(route('servicio.update', selectedServicioId.value), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                showForm.value = false;
                editMode.value = false;
                selectedServicioId.value = null;
            },
        });
        return;
    }

    form.post(route('servicio.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showForm.value = false;
        },
    });
};

const goToForm = () => {
    showForm.value = true;
    setTimeout(() => {
        formSection.value?.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    }, 100);
};

const resetForm = () => {
    form.reset();
    showForm.value = false;
    editMode.value = false;
    selectedServicioId.value = null;
};

const eliminarServicio = (servicioId: number) => {
    if (confirm('¿Estás seguro de que deseas inactivar este servicio? El registro pasará a estar INACTIVO.')) {
        form.delete(route('servicio.destroy', servicioId), {
            preserveScroll: true,
        });
    }
};

const restaurarServicio = (servicioId: number) => {
    if (confirm('¿Estás seguro de que deseas restaurar este servicio a estado ACTIVO?')) {
        form.patch(route('servicio.restore', servicioId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Servicios" />

    <AuthenticatedLayout>
        <template #title> Servicios </template>

        <section ref="formSection" class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Header Card -->
            <ServicioHeader
                v-model:search="search"
                :results-count="serviciosFiltrados.length"
                :show-form="showForm"
                :is-paciente="tieneRol('Paciente')"
                :can-create="tienePermiso('servicio.store')"
                :can-edit="tienePermiso('servicio.update')"
                @toggle-form="showForm ? resetForm() : goToForm()"
            />

            <!-- Form Card -->
            <ServicioForm
                v-if="showForm"
                :form="form"
                :edit-mode="editMode"
                @submit="submit"
                @cancel="resetForm"
            />

            <!-- Grid List -->
            <ServicioCardList
                v-if="serviciosFiltrados.length"
                :servicios="serviciosFiltrados"
                :can-update="tienePermiso('servicio.update')"
                :can-destroy="tienePermiso('servicio.destroy')"
                @edit="editServicio"
                @delete="eliminarServicio"
                @restore="restaurarServicio"
            />

            <!-- Empty State -->
            <ServicioEmptyState v-else />
        </section>
    </AuthenticatedLayout>
</template>