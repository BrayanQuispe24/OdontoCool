<script setup lang="ts">
import { Diente } from '@/types/Diente';
import { Tratamiento } from '@/types/Tratamiento';
import { Diagnostico } from '@/types/Diagnostico';
import { HistorialClinico } from '@/types/HistorialClinico';
import { Servicio } from '@/types/Servicio';
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { PageProps } from '@/types';
import TratamientoHeader from '@/components/Tratamiento/IndexComponents/TratamientoHeader.vue';
import TratamientoForm from '@/components/Tratamiento/IndexComponents/TratamientoForm.vue';
import TratamientoCardList from '@/components/Tratamiento/IndexComponents/TratamientoCardList.vue';
import TratamientoEmptyState from '@/components/Tratamiento/IndexComponents/TratamientoEmptyState.vue';

const props = defineProps<{
    tratamientos: Array<Tratamiento>;
    dientes: Array<Diente>;
    diagnosticosDisponibles: Array<Diagnostico>;
    diagnosticosTodos: Array<Diagnostico>;
    historiales: Array<HistorialClinico>;
    servicios: Array<Servicio>;
}>();

const page = usePage<PageProps>();

const authUser = computed(() => page.props.auth.user);
const permisos = computed<string[]>(() => page.props.auth.permisos ?? []);

const tienePermiso = (permiso: string): boolean => {
    return permisos.value.includes(permiso);
};

const tieneRol = (rol: string) => {
    return authUser.value?.rol?.nombre === rol;
};

const search = ref('');
const showForm = ref(false);
const editMode = ref(false);
const selectedTratamientoId = ref<number | null>(null);
const formSection = ref<HTMLElement | null>(null);

// Form Tabs navigation
const activeFormTab = ref<'general' | 'dientes' | 'receta' | 'servicios'>('general');
const activeToothIndex = ref(0);

const form = useForm({
    objetivo_tratamiento: '',
    observacion: '',
    estado: 'ACTIVO',
    fecha_inicio: new Date().toISOString().split('T')[0],
    fecha_fin_estimada: new Date().toISOString().split('T')[0],
    fecha_fin_real: '',
    diagnostico_id: '',
    codigo_historial: '',
    dientes_tratamientos: [] as Array<{
        id?: number;
        diente_id: number;
        cara_dental: string;
        tratamiento_planificado: string;
        observacion: string;
    }>,
    incluir_receta: false,
    receta: {
        observacion_general: '',
        detalles: [] as Array<{
            id?: number;
            descripcion: string;
            dosis: string;
            duracion: string;
            frecuencia: string;
        }>,
    },
    servicios_prestados: [] as Array<{
        id?: number;
        servicio_id: number;
        cantidad: number;
        precio: number;
        descuento: number;
        fecha_servicio: string;
        observacion: string;
    }>,
});

const diagnosticosSelect = computed(() => {
    if (editMode.value) {
        return props.diagnosticosTodos;
    }

    return props.diagnosticosDisponibles;
});

const tratamientosFiltrados = computed(() => {
    return props.tratamientos.filter((t) => {
        const query = search.value.toLowerCase();

        const matchesObjective = t.objetivo_tratamiento
            .toLowerCase()
            .includes(query);

        const matchesObs =
            t.observacion?.toLowerCase().includes(query) || false;

        const pacienteNombre = t.historial_clinico?.paciente?.persona
            ? `${t.historial_clinico.paciente.persona.nombre} ${t.historial_clinico.paciente.persona.apellido}`.toLowerCase()
            : '';

        const matchesPaciente = pacienteNombre.includes(query);

        const matchesTooth =
            t.tratamientos_dientes?.some(
                (td) =>
                    td.diente?.nombre.toLowerCase().includes(query) ||
                    td.diente?.numero.toString() === query ||
                    td.cara_dental.toLowerCase().includes(query) ||
                    td.tratamiento_planificado.toLowerCase().includes(query),
            ) || false;

        return matchesObjective || matchesObs || matchesTooth || matchesPaciente;
    });
});

const editTratamiento = (tratamiento: Tratamiento) => {
    editMode.value = true;
    selectedTratamientoId.value = tratamiento.id;
    showForm.value = true;

    form.objetivo_tratamiento = tratamiento.objetivo_tratamiento;
    form.observacion = tratamiento.observacion || '';
    form.estado = tratamiento.estado;
    form.fecha_inicio = tratamiento.fecha_inicio;
    form.fecha_fin_estimada = tratamiento.fecha_fin_estimada;
    form.fecha_fin_real = tratamiento.fecha_fin_real || '';
    form.codigo_historial = tratamiento.codigo_historial ? tratamiento.codigo_historial.toString() : '';
    form.diagnostico_id = tratamiento.diagnostico?.id ? tratamiento.diagnostico.id.toString() : '';

    if (
        tratamiento.tratamientos_dientes &&
        tratamiento.tratamientos_dientes.length > 0
    ) {
        form.dientes_tratamientos = tratamiento.tratamientos_dientes.map(
            (td) => ({
                id: td.id,
                diente_id: td.diente_id,
                cara_dental: td.cara_dental,
                tratamiento_planificado: td.tratamiento_planificado,
                observacion: td.observacion || '',
            }),
        );
    } else {
        form.dientes_tratamientos = [];
    }

    if (tratamiento.receta_recomendaciones) {
        form.incluir_receta = true;
        form.receta.observacion_general = tratamiento.receta_recomendaciones.observacion_general || '';
        form.receta.detalles = (tratamiento.receta_recomendaciones.detalles || []).map((d) => ({
            id: d.id,
            descripcion: d.descripcion,
            dosis: d.dosis || '',
            duracion: d.duracion || '',
            frecuencia: d.frecuencia || '',
        }));
    } else {
        form.incluir_receta = false;
        form.receta.observacion_general = '';
        form.receta.detalles = [];
    }

    if ((tratamiento as any).servicio_prestado && (tratamiento as any).servicio_prestado.length > 0) {
        form.servicios_prestados = (tratamiento as any).servicio_prestado.map((sp: any) => ({
            id: sp.id,
            servicio_id: sp.servicio_id,
            cantidad: sp.cantidad,
            precio: parseFloat(sp.precio),
            descuento: parseFloat(sp.descuento || 0),
            fecha_servicio: sp.fecha_servicio,
            observacion: sp.observacion || '',
        }));
    } else {
        form.servicios_prestados = [];
    }

    activeFormTab.value = 'general';
    activeToothIndex.value = 0;

    goToForm();
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
    form.clearErrors();
    showForm.value = false;
    editMode.value = false;
    selectedTratamientoId.value = null;
    activeFormTab.value = 'general';
    activeToothIndex.value = 0;
};

const submit = () => {
    const originalReceta = form.receta;

    if (!form.incluir_receta) {
        form.receta = null as any;
    }

    if (editMode.value && selectedTratamientoId.value) {
        form.put(route('tratamiento.update', selectedTratamientoId.value), {
            preserveScroll: true,
            onSuccess: () => {
                resetForm();
            },
            onFinish: () => {
                form.receta = originalReceta;
            }
        });
        return;
    }

    form.post(route('tratamiento.store'), {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
        },
        onFinish: () => {
            form.receta = originalReceta;
        }
    });
};

const eliminarTratamiento = (id: number) => {
    if (
        confirm(
            '¿Estás seguro de que deseas inactivar este tratamiento? El registro se mantendrá pero pasará a estar INACTIVO.',
        )
    ) {
        form.delete(route('tratamiento.destroy', id), {
            preserveScroll: true,
        });
    }
};

const restaurarTratamiento = (id: number) => {
    if (
        confirm(
            '¿Estás seguro de que deseas restaurar este tratamiento a estado ACTIVO?',
        )
    ) {
        form.patch(route('tratamiento.restore', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Tratamientos" />

    <AuthenticatedLayout>
        <template #title> Tratamientos </template>

        <section ref="formSection"
            class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Header Card -->
            <TratamientoHeader
                v-model:search="search"
                :results-count="tratamientosFiltrados.length"
                :show-form="showForm"
                :can-create="tienePermiso('tratamiento.store')"
                :can-edit="tienePermiso('tratamiento.update')"
                :edit-mode="editMode"
                :is-doctor-or-admin="tieneRol('Doctor') || tieneRol('Administrador')"
                @toggle-form="showForm ? resetForm() : goToForm()"
            />

            <!-- Form Card -->
            <TratamientoForm
                v-if="showForm"
                :form="form"
                :edit-mode="editMode"
                :dientes="dientes"
                :diagnosticos-select="diagnosticosSelect"
                :historiales="historiales"
                :servicios="servicios"
                v-model:activeFormTab="activeFormTab"
                v-model:activeToothIndex="activeToothIndex"
                :tiene-rol="tieneRol"
                @submit="submit"
                @cancel="resetForm"
            />

            <!-- Treatments List -->
            <TratamientoCardList
                v-if="tratamientosFiltrados.length"
                :tratamientos="tratamientosFiltrados"
                :can-show="tienePermiso('tratamiento.show')"
                :can-update="tienePermiso('tratamiento.update')"
                :can-destroy="tienePermiso('tratamiento.destroy')"
                :is-doctor="tieneRol('Doctor')"
                @edit="editTratamiento"
                @delete="eliminarTratamiento"
                @restore="restaurarTratamiento"
            />

            <!-- Empty State -->
            <TratamientoEmptyState
                v-else
                @go-to-form="goToForm"
            />
        </section>
    </AuthenticatedLayout>
</template>