<script setup lang="ts">
import { computed, nextTick, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Permiso } from '@/types/Permiso';
import type { Modulo } from '@/types/Modulo';

import PermisoHeader from '@/components/Permiso/PermisoHeader.vue';
import PermisoForm from '@/components/Permiso/PermisoForm.vue';
import PermisoTable from '@/components/Permiso/PermisoTable.vue';
import PermisoEmptyState from '@/components/Permiso/PermisoEmptyState.vue';

const props = defineProps<{
    permisos: Array<Permiso>;
    modulos: Array<Modulo>;
}>();

const search = ref('');
const moduloSeleccionado = ref('');
const showForm = ref(false);
const editMode = ref(false);
const selectedPermisoId = ref<number | null>(null);
const formSection = ref<HTMLElement | null>(null);

const permisosFiltrados = computed(() => {
    return props.permisos.filter((permiso) => {
        const coincideNombre = permiso.nombre
            .toLowerCase()
            .includes(search.value.toLowerCase());

        const coincideModulo = moduloSeleccionado.value === ''
            || permiso.modulo_id == Number(moduloSeleccionado.value);

        return coincideNombre && coincideModulo;
    });
});

const selectedPermiso = computed(() => {
    if (!selectedPermisoId.value) return null;
    return props.permisos.find((p) => p.id === selectedPermisoId.value) ?? null;
});

const desplazarAlFormulario = async () => {
    await nextTick();
    formSection.value?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
};

const editPermiso = async (permiso: Permiso) => {
    selectedPermisoId.value = permiso.id;
    editMode.value = true;
    showForm.value = true;
    await desplazarAlFormulario();
};

const handleToggleForm = async () => {
    if (showForm.value) {
        cerrarFormulario();
    } else {
        selectedPermisoId.value = null;
        editMode.value = false;
        showForm.value = true;
        await desplazarAlFormulario();
    }
};

const eliminarPermiso = (permisoId: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este permiso?')) {
        router.delete(route('permiso.destroy', permisoId), {
            preserveScroll: true,
        });
    }
};

const cerrarFormulario = () => {
    showForm.value = false;
    editMode.value = false;
    selectedPermisoId.value = null;
};
</script>

<template>
    <Head title="Permisos" />

    <AuthenticatedLayout>
        <template #title>
            Permisos
        </template>

        <section ref="formSection" class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Header con búsqueda y filtros -->
            <PermisoHeader
                :search="search"
                :modulo-seleccionado="moduloSeleccionado"
                :results-count="permisosFiltrados.length"
                :show-form="showForm"
                :modulos="modulos"
                @update:search="(val) => search = val"
                @update:modulo-seleccionado="(val) => moduloSeleccionado = val"
                @toggle-form="handleToggleForm"
            />

            <!-- Formulario -->
            <PermisoForm
                v-if="showForm"
                :permiso="selectedPermiso"
                :edit-mode="editMode"
                :modulos="modulos"
                @saved="cerrarFormulario"
                @cancel="cerrarFormulario"
            />

            <!-- Tabla / Cards -->
            <PermisoTable
                v-if="permisosFiltrados.length"
                :permisos="permisosFiltrados"
                @edit="editPermiso"
                @delete="eliminarPermiso"
            />

            <!-- Estado vacío -->
            <PermisoEmptyState
                v-else
                :search="search"
            />
        </section>
    </AuthenticatedLayout>
</template>