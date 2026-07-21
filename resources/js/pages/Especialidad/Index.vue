<script setup lang="ts">
import { computed, nextTick, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Especialidad } from '@/types/Especialidad';

import EspecialidadHeader from '@/components/Especialidad/EspecialidadHeader.vue';
import EspecialidadForm from '@/components/Especialidad/EspecialidadForm.vue';
import EspecialidadCardList from '@/components/Especialidad/EspecialidadCardList.vue';
import EspecialidadEmptyState from '@/components/Especialidad/EspecialidadEmptyState.vue';

const props = defineProps<{
    especialidades: Array<Especialidad>;
}>();

const search = ref('');
const showForm = ref(false);
const editMode = ref(false);
const selectedEspecialidadId = ref<number | null>(null);
const formSection = ref<HTMLElement | null>(null);
const estados = ref(['activo', 'inactivo', 'suspendido']);

const selectedEspecialidad = computed(() => {
    if (!selectedEspecialidadId.value) return null;
    return props.especialidades.find((e) => e.id === selectedEspecialidadId.value) ?? null;
});

const especialidadesFiltradas = computed(() => {
    return props.especialidades.filter((especialidad) =>
        especialidad.nombre.toLowerCase().includes(search.value.toLowerCase())
    );
});

const desplazarAlFormulario = async () => {
    await nextTick();
    formSection.value?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
};

const editEspecialidad = async (especialidad: Especialidad) => {
    selectedEspecialidadId.value = especialidad.id;
    editMode.value = true;
    showForm.value = true;
    await desplazarAlFormulario();
};

const handleToggleForm = async () => {
    if (showForm.value) {
        cerrarFormulario();
    } else {
        selectedEspecialidadId.value = null;
        editMode.value = false;
        showForm.value = true;
        await desplazarAlFormulario();
    }
};

const eliminarEspecialidad = (especialidadId: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta especialidad?')) {
        router.delete(route('especialidad.destroy', especialidadId), {
            preserveScroll: true,
        });
    }
};

const cerrarFormulario = () => {
    showForm.value = false;
    editMode.value = false;
    selectedEspecialidadId.value = null;
};
</script>

<template>
    <Head title="Especialidades" />

    <AuthenticatedLayout>
        <template #title>
            Módulos
        </template>

        <section ref="formSection" class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Premium Header Card -->
            <EspecialidadHeader
                :search="search"
                :results-count="especialidadesFiltradas.length"
                :show-form="showForm"
                @update:search="(val) => search = val"
                @toggle-form="handleToggleForm"
            />

            <!-- Form -->
            <EspecialidadForm
                v-if="showForm"
                :especialidad="selectedEspecialidad"
                :edit-mode="editMode"
                :estados="estados"
                @saved="cerrarFormulario"
                @cancel="cerrarFormulario"
            />

            <!-- Grid or Empty State -->
            <EspecialidadCardList
                v-if="especialidadesFiltradas.length"
                :especialidades="especialidadesFiltradas"
                @edit="editEspecialidad"
                @delete="eliminarEspecialidad"
            />

            <EspecialidadEmptyState v-else />
        </section>
    </AuthenticatedLayout>
</template>