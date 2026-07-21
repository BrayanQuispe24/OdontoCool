<script setup lang="ts">
import { computed, nextTick, ref, onMounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import type { Analisis } from '@/types/Analisis';
import type { Tratamiento } from '@/types/Tratamiento';
import type { SolicitudAnalisis } from '@/types/SolicitudAnalisi';
import type { Doctor } from '@/types/doctor';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AnalisisHeader from '@/components/Analisis/AnalisisHeader.vue';
import AnalisisCatalogForm from '@/components/Analisis/AnalisisCatalogForm.vue';
import AnalisisSolicitudForm from '@/components/Analisis/AnalisisSolicitudForm.vue';
import AnalisisResultadoForm from '@/components/Analisis/AnalisisResultadoForm.vue';
import AnalisisCatalogList from '@/components/Analisis/AnalisisCatalogList.vue';
import AnalisisSolicitudTable from '@/components/Analisis/AnalisisSolicitudTable.vue';
import AnalisisResultadoList from '@/components/Analisis/AnalisisResultadoList.vue';
import AnalisisEmptyState from '@/components/Analisis/AnalisisEmptyState.vue';

const props = defineProps<{
    analisis: Array<Analisis>;
    solicitudes: Array<SolicitudAnalisis>;
    tratamientos: Array<Tratamiento>;
    doctores: Array<Doctor>;
}>();

// Usuario logueado
const page = usePage<PageProps>();
const authUser = computed(() => page.props.auth.user);
const permisos = computed<string[]>(() => page.props.auth.permisos ?? []);

const tienePermiso = (permiso: string) => {
    return permisos.value.includes(permiso);
};

const tieneRol = (rol: string) => {
    return authUser.value?.rol?.nombre === rol;
};

// Tabs state
const activeTab = ref<'catalogo' | 'solicitudes' | 'resultados'>('catalogo');
const search = ref('');
const formSection = ref<HTMLElement | null>(null);

const desplazarAlFormulario = async () => {
    await nextTick();
    formSection.value?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
};

// 1. Catalog Form State
const showCatalogForm = ref(false);
const selectedCatalog = ref<Analisis | null>(null);

const nuevoCatalog = async () => {
    selectedCatalog.value = null;
    showCatalogForm.value = true;
    await desplazarAlFormulario();
};

const editarCatalog = async (item: Analisis) => {
    selectedCatalog.value = item;
    showCatalogForm.value = true;
    await desplazarAlFormulario();
};

const cerrarCatalogForm = () => {
    showCatalogForm.value = false;
    selectedCatalog.value = null;
};

const catalogFormGuardado = () => {
    cerrarCatalogForm();
};

// 2. Request Form State
const showSolicitudForm = ref(false);
const selectedSolicitud = ref<SolicitudAnalisis | null>(null);

const nuevaSolicitud = async () => {
    selectedSolicitud.value = null;
    showSolicitudForm.value = true;
    await desplazarAlFormulario();
};

const editarSolicitud = async (item: SolicitudAnalisis) => {
    selectedSolicitud.value = item;
    showSolicitudForm.value = true;
    await desplazarAlFormulario();
};

const cerrarSolicitudForm = () => {
    showSolicitudForm.value = false;
    selectedSolicitud.value = null;
};

const solicitudFormGuardado = () => {
    cerrarSolicitudForm();
};

// 3. Result Form State
const showResultadoForm = ref(false);
const selectedSolicitudForResultado = ref<any | null>(null);

const registrarResultado = async (solicitud: any) => {
    selectedSolicitudForResultado.value = solicitud;
    showResultadoForm.value = true;
    await desplazarAlFormulario();
};

const editarResultado = async (solicitud: any) => {
    selectedSolicitudForResultado.value = solicitud;
    showResultadoForm.value = true;
    await desplazarAlFormulario();
};

const cerrarResultadoForm = () => {
    showResultadoForm.value = false;
    selectedSolicitudForResultado.value = null;
};

const resultadoFormGuardado = () => {
    cerrarResultadoForm();
};

// Acciones de eliminación / restauración
const eliminarCatalog = (id: number) => {
    if (
        confirm('¿Estás seguro de que deseas inactivar este tipo de análisis? El registro se mantendrá pero pasará a estar INACTIVO.')
    ) {
        router.delete(route('analisis.destroy', id), {
            preserveScroll: true,
        });
    }
};

const restaurarCatalog = (id: number) => {
    if (
        confirm('¿Estás seguro de que deseas restaurar este tipo de análisis a estado ACTIVO?')
    ) {
        router.patch(route('analisis.restore', id), {
            preserveScroll: true,
        });
    }
};

const eliminarSolicitud = (id: number) => {
    if (
        confirm('¿Estás seguro de que deseas eliminar esta solicitud de análisis?')
    ) {
        router.delete(route('solicitud_analisis.destroy', id), {
            preserveScroll: true,
        });
    }
};

const eliminarResultado = (id: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este resultado?')) {
        router.delete(route('resultado_analisis.destroy', id), {
            preserveScroll: true,
        });
    }
};

// Filtrado de listas
const catalogFiltrados = computed(() => {
    return props.analisis.filter((item) => {
        const query = search.value.toLowerCase();
        return (
            item.nombre.toLowerCase().includes(query) ||
            (item.descripcion?.toLowerCase() || '').includes(query)
        );
    });
});

const analisisActivos = computed(() => {
    return props.analisis.filter(item => {
        if (item.estado === 'ACTIVO') return true;
        if (selectedSolicitud.value && String(item.id) === String(selectedSolicitud.value.analisis?.id)) return true;
        return false;
    });
});

const solicitudesFiltradas = computed(() => {
    return props.solicitudes.filter((item) => {
        const query = search.value.toLowerCase();
        return (
            item.id.toString() === query ||
            item.analisis?.nombre.toLowerCase().includes(query) ||
            item.tratamiento?.objetivo_tratamiento
                .toLowerCase()
                .includes(query) ||
            item.estado.toLowerCase().includes(query) ||
            (item.motivo?.toLowerCase() || '').includes(query)
        );
    });
});

const resultadosFiltrados = computed(() => {
    return props.solicitudes
        .filter((s) => s.resultado_analisis !== null)
        .map((s) => ({
            ...s.resultado_analisis,
            solicitud: s,
        }))
        .filter((res: any) => {
            const query = search.value.toLowerCase();
            return (
                res.solicitud_analisis_id.toString() === query ||
                res.solicitud?.analisis?.nombre.toLowerCase().includes(query) ||
                res.resultado.toLowerCase().includes(query) ||
                (res.observaciones?.toLowerCase() || '').includes(query) ||
                (res.interpretacion?.toLowerCase() || '').includes(query)
            );
        });
});

const loggedInDoctorCi = computed(() => {
    const loggedInDoctor = props.doctores.find(doc => doc.persona_id === authUser.value?.persona_id);
    return loggedInDoctor ? loggedInDoctor.codigo_doctor : '';
});

const selectTab = (tabName: 'catalogo' | 'solicitudes' | 'resultados') => {
    activeTab.value = tabName;
    search.value = '';
    cerrarCatalogForm();
    cerrarSolicitudForm();
    cerrarResultadoForm();
};

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const tabParam = params.get('tab');
    const searchParam = params.get('search');

    if (tabParam === 'catalogo' || tabParam === 'solicitudes' || tabParam === 'resultados') {
        activeTab.value = tabParam;
    }

    if (searchParam) {
        search.value = searchParam;
    }
});
</script>

<template>
    <Head title="Laboratorio e Informes" />

    <AuthenticatedLayout>
        <template #title> Laboratorio y Análisis </template>

        <section ref="formSection"
            class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">

            <AnalisisHeader
                v-model:search="search"
                :active-tab="activeTab"
                :show-catalog-form="showCatalogForm"
                :show-solicitud-form="showSolicitudForm"
                :catalog-count="catalogFiltrados.length"
                :solicitud-count="solicitudesFiltradas.length"
                :resultado-count="resultadosFiltrados.length"
                :solicitudes-total="solicitudes.length"
                :resultados-total="resultadosFiltrados.length"
                :tiene-rol-paciente="tieneRol('Paciente')"
                :tiene-rol-doctor-or-admin="tieneRol('Doctor') || tieneRol('Administrador')"
                :tiene-permiso-store-analisis="tienePermiso('analisis.store')"
                :tiene-permiso-store-solicitud="tienePermiso('solicitud_analisis.store')"
                @select-tab="selectTab"
                @toggle-catalog-form="showCatalogForm ? cerrarCatalogForm() : nuevoCatalog()"
                @toggle-solicitud-form="showSolicitudForm ? cerrarSolicitudForm() : nuevaSolicitud()"
            />

            <!-- Form: Catálogo -->
            <AnalisisCatalogForm
                v-if="activeTab === 'catalogo' && showCatalogForm"
                :analisis="selectedCatalog"
                @saved="catalogFormGuardado"
                @cancel="cerrarCatalogForm"
            />

            <!-- Form: Solicitud -->
            <AnalisisSolicitudForm
                v-if="activeTab === 'solicitudes' && showSolicitudForm"
                :solicitud="selectedSolicitud"
                :analisis-activos="analisisActivos"
                :tratamientos="tratamientos"
                :doctores="doctores"
                :logged-in-doctor-ci="loggedInDoctorCi"
                @saved="solicitudFormGuardado"
                @cancel="cerrarSolicitudForm"
            />

            <!-- Form: Resultado -->
            <AnalisisResultadoForm
                v-if="showResultadoForm"
                :solicitud="selectedSolicitudForResultado"
                @saved="resultadoFormGuardado"
                @cancel="cerrarResultadoForm"
            />

            <!-- LIST REGIONS -->
            <template v-if="activeTab === 'catalogo'">
                <AnalisisCatalogList
                    v-if="catalogFiltrados.length"
                    :analisis="catalogFiltrados"
                    :tiene-permiso-update="tienePermiso('analisis.update')"
                    :tiene-permiso-destroy="tienePermiso('analisis.destroy')"
                    @edit="editarCatalog"
                    @restore="restaurarCatalog"
                    @delete="eliminarCatalog"
                />
                <AnalisisEmptyState v-else :active-tab="activeTab" />
            </template>

            <template v-else-if="activeTab === 'solicitudes'">
                <AnalisisSolicitudTable
                    v-if="solicitudesFiltradas.length"
                    :solicitudes="solicitudesFiltradas"
                    :tiene-rol-doctor-or-admin="tieneRol('Doctor') || tieneRol('Administrador')"
                    :tiene-permiso-update="tienePermiso('solicitud_analisis.update')"
                    :tiene-permiso-destroy="tienePermiso('solicitud_analisis.destroy')"
                    @edit="editarSolicitud"
                    @delete="eliminarSolicitud"
                    @registrar-resultado="registrarResultado"
                    @edit-resultado="editarResultado"
                />
                <AnalisisEmptyState v-else :active-tab="activeTab" />
            </template>

            <template v-else-if="activeTab === 'resultados'">
                <AnalisisResultadoList
                    v-if="resultadosFiltrados.length"
                    :resultados="resultadosFiltrados"
                    @edit="editarResultado"
                    @delete="eliminarResultado"
                />
                <AnalisisEmptyState v-else :active-tab="activeTab" />
            </template>

        </section>
    </AuthenticatedLayout>
</template>