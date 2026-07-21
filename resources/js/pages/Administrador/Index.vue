<script setup lang="ts">
import { computed, nextTick, ref } from 'vue';
import { Head } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';



import type { Propietario } from '@/types/Propietario';
import AdministradorHeader from '@/components/Administrador/AdministradorHeader.vue';
import AdministradorForm from '@/components/Administrador/AdministradorForm.vue';
import AdministradorCardList from '@/components/Administrador/AdministradorCardList.vue';
import AdministradorTable from '@/components/Administrador/AdministradorTable.vue';
import AdministradorEmptyState from '@/components/Administrador/AdministradorEmptyState.vue';

const props = defineProps<{
    propietarios: Propietario[];
}>();

const search = ref('');
const showForm = ref(false);
const selectedPropietario = ref<Propietario | null>(null);
const formSection = ref<HTMLElement | null>(null);

const propietariosFiltrados = computed(() => {
    const termino = search.value.trim().toLowerCase();

    if (!termino) {
        return props.propietarios;
    }

    return props.propietarios.filter((propietario) => {
        const usuario = propietario.persona?.usuarios?.[0];

        const textoBusqueda = [
            propietario.codigo_propietario,
            propietario.persona_id,
            propietario.persona?.carnet_identidad,
            propietario.persona?.nombre,
            propietario.persona?.apellido,
            propietario.persona?.telefono,
            usuario?.email,
            usuario?.estado,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase();

        return textoBusqueda.includes(termino);
    });
});

const desplazarAlFormulario = async () => {
    await nextTick();

    formSection.value?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
};

const nuevoPropietario = async () => {
    selectedPropietario.value = null;
    showForm.value = true;

    await desplazarAlFormulario();
};

const editarPropietario = async (propietario: Propietario) => {
    selectedPropietario.value = propietario;
    showForm.value = true;

    await desplazarAlFormulario();
};

const cerrarFormulario = () => {
    showForm.value = false;
    selectedPropietario.value = null;
};

const formularioGuardado = () => {
    cerrarFormulario();
};
</script>

<template>
    <Head title="Propietarios" />

    <AuthenticatedLayout>
        <template #title>
            Propietarios
        </template>

        <section
            class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300"
        >
            <AdministradorHeader
                v-model:search="search"
                :show-form="showForm"
                @create="nuevoPropietario"
                @close="cerrarFormulario"
            />

            <div ref="formSection">
                <AdministradorForm
                    v-if="showForm"
                    :propietario="selectedPropietario"
                    @saved="formularioGuardado"
                    @cancel="cerrarFormulario"
                />
            </div>

            <template v-if="propietariosFiltrados.length > 0">
                <AdministradorTable
                    :propietarios="propietariosFiltrados"
                    @edit="editarPropietario"
                />

                <AdministradorCardList
                    :propietarios="propietariosFiltrados"
                    @edit="editarPropietario"
                />
            </template>

            <AdministradorEmptyState v-else />
        </section>
    </AuthenticatedLayout>
</template>