<script setup lang="ts">
import { computed, nextTick, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Modulo } from '@/types/Modulo';

import ModuloHeader from '@/components/Modulo/ModuloHeader.vue';
import ModuloForm from '@/components/Modulo/ModuloForm.vue';
import ModuloCardList from '@/components/Modulo/ModuloCardList.vue';
import ModuloEmptyState from '@/components/Modulo/ModuloEmptyState.vue';

const props = defineProps<{
    modulos: Array<Modulo>;
}>();

const search = ref('');
const showForm = ref(false);
const editMode = ref(false);
const selectedModuloId = ref<number | null>(null);
const formSection = ref<HTMLElement | null>(null);

const selectedModulo = computed(() => {
    if (!selectedModuloId.value) return null;
    return props.modulos.find((modulo) => modulo.id === selectedModuloId.value) ?? null;
});

const modulosFiltrados = computed(() => {
    return props.modulos.filter((modulo) =>
        modulo.nombre.toLowerCase().includes(search.value.toLowerCase())
    );
});

const desplazarAlFormulario = async () => {
    await nextTick();
    formSection.value?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
};

const editModulo = async (modulo: Modulo) => {
    selectedModuloId.value = modulo.id;
    editMode.value = true;
    showForm.value = true;
    await desplazarAlFormulario();
};

const handleToggleForm = async () => {
    if (showForm.value) {
        cerrarFormulario();
    } else {
        selectedModuloId.value = null;
        editMode.value = false;
        showForm.value = true;
        await desplazarAlFormulario();
    }
};

const eliminarModulo = (moduloId: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este módulo?')) {
        router.delete(route('modulo.destroy', moduloId), {
            preserveScroll: true,
        });
    }
};

const cerrarFormulario = () => {
    showForm.value = false;
    editMode.value = false;
    selectedModuloId.value = null;
};
</script>

<template>
    <Head title="Módulos" />

    <AuthenticatedLayout>
        <template #title>
            Módulos
        </template>

        <section ref="formSection" class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Premium Header Card -->
            <ModuloHeader
                :search="search"
                :results-count="modulosFiltrados.length"
                :show-form="showForm"
                @update:search="(val) => search = val"
                @toggle-form="handleToggleForm"
            />

            <!-- Form -->
            <ModuloForm
                v-if="showForm"
                :modulo="selectedModulo"
                :edit-mode="editMode"
                @saved="cerrarFormulario"
                @cancel="cerrarFormulario"
            />

            <!-- Card list or Empty state -->
            <ModuloCardList
                v-if="modulosFiltrados.length"
                :modulos="modulosFiltrados"
                @edit="editModulo"
                @delete="eliminarModulo"
            />

            <ModuloEmptyState v-else />
        </section>
    </AuthenticatedLayout>
</template>