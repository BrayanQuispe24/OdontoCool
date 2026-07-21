<script setup lang="ts">
import { computed, nextTick, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Paciente } from '@/types/Paciente';
import type { PageProps } from '@/types';

import PacienteHeader from '@/components/Paciente/IndexComponents/PacienteHeader.vue';
import PacienteForm from '@/components/Paciente/IndexComponents/PacienteForm.vue';
import PacienteTable from '@/components/Paciente/IndexComponents/PacienteTable.vue';
import PacienteCardList from '@/components/Paciente/IndexComponents/PacienteCardList.vue';
import PacienteEmptyState from '@/components/Paciente/IndexComponents/PacienteEmptyState.vue';

const props = defineProps<{
    pacientes: Array<Paciente>;
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
const selectedPacienteId = ref<string | null>(null);
const formSection = ref<HTMLElement | null>(null);

const selectedPaciente = computed(() => {
    if (!selectedPacienteId.value) return null;
    return props.pacientes.find((p) => p.codigo_paciente === selectedPacienteId.value) ?? null;
});

const pacientesFiltrados = computed(() => {
    return props.pacientes.filter((paciente) => {
        const usuario = paciente.persona?.usuarios?.[0];
        const texto = `
            ${paciente.codigo_paciente}
            ${paciente.persona?.nombre ?? ''}
            ${paciente.persona?.apellido ?? ''}
            ${paciente.persona_id}
            ${usuario?.email ?? ''}
            ${usuario?.estado ?? ''}
        `;
        return texto.toLowerCase().includes(search.value.toLowerCase());
    });
});

const desplazarAlFormulario = async () => {
    await nextTick();
    formSection.value?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
};

const editPaciente = async (paciente: Paciente) => {
    selectedPacienteId.value = paciente.codigo_paciente;
    editMode.value = true;
    showForm.value = true;
    await desplazarAlFormulario();
};

const handleToggleForm = async () => {
    if (showForm.value) {
        cerrarFormulario();
    } else {
        selectedPacienteId.value = null;
        editMode.value = false;
        showForm.value = true;
        await desplazarAlFormulario();
    }
};

const eliminarPaciente = (codigo: string) => {
    if (confirm('¿Estás seguro de que deseas eliminar este paciente?')) {
        router.delete(route('paciente.destroy', codigo), {
            preserveScroll: true,
        });
    }
};

const cerrarFormulario = () => {
    showForm.value = false;
    editMode.value = false;
    selectedPacienteId.value = null;
};
</script>

<template>
    <Head title="Pacientes" />

    <AuthenticatedLayout>
        <template #title>
            Pacientes
        </template>

        <section ref="formSection" class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Premium Header -->
            <PacienteHeader
                :search="search"
                :results-count="pacientesFiltrados.length"
                :show-form="showForm"
                :can-create="tienePermiso('paciente.store') || tienePermiso('paciente.update')"
                @update:search="(val) => search = val"
                @toggle-form="handleToggleForm"
            />

            <!-- Form -->
            <PacienteForm
                v-if="showForm && tienePermiso('paciente.store') || (editMode && tienePermiso('paciente.update'))"
                :paciente="selectedPaciente"
                :edit-mode="editMode"
                :tiene-rol="tieneRol"
                @saved="cerrarFormulario"
                @cancel="cerrarFormulario"
            />

            <!-- Desktop & Mobile Lists -->
            <div v-if="pacientesFiltrados.length" class="space-y-6">
                <PacienteTable
                    :pacientes="pacientesFiltrados"
                    :tiene-permiso="tienePermiso"
                    @edit="editPaciente"
                />

                <PacienteCardList
                    :pacientes="pacientesFiltrados"
                    :tiene-permiso="tienePermiso"
                    @edit="editPaciente"
                    @delete="eliminarPaciente"
                />
            </div>

            <!-- Empty State -->
            <PacienteEmptyState
                v-else
                :search="search"
            />
        </section>
    </AuthenticatedLayout>
</template>