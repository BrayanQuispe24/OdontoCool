<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Diagnostico } from '@/types/Diagnostico';
import type { Cita } from '@/types/Cita';
import type { Diente } from '@/types/Diente';

import DiagnosticoHeader from '@/components/Diagnostico/DiagnosticoHeader.vue';
import DiagnosticoForm from '@/components/Diagnostico/DiagnosticoForm.vue';
import DiagnosticoTable from '@/components/Diagnostico/DiagnosticoTable.vue';
import DiagnosticoEmptyState from '@/components/Diagnostico/DiagnosticoEmptyState.vue';

const props = defineProps<{
    diagnosticos: Array<Diagnostico>;
    citasDisponibles: Array<Cita>;
    citasTodas: Array<Cita>;
    dientes: Array<Diente>;
}>();

const search = ref('');
const showForm = ref(false);
const editMode = ref(false);
const selectedDiagnostico = ref<Diagnostico | null>(null);

const citasSelect = computed(() => {
    if (editMode.value) {
        return props.citasTodas;
    }
    return props.citasDisponibles;
});

const openNewForm = () => {
    selectedDiagnostico.value = null;
    editMode.value = false;
    showForm.value = true;
};

const editDiagnostico = (item: Diagnostico) => {
    selectedDiagnostico.value = item;
    editMode.value = true;
    showForm.value = true;
};

const resetForm = () => {
    showForm.value = false;
    editMode.value = false;
    selectedDiagnostico.value = null;
};

const deleteDiagnostico = (id: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este diagnóstico? Los detalles e intervenciones dentales asociadas se eliminarán permanentemente.')) {
        router.delete(route('diagnostico.destroy', id), {
            preserveScroll: true,
            onSuccess: () => resetForm()
        });
    }
};

const diagnosticosFiltrados = computed(() => {
    return props.diagnosticos.filter((item) => {
        const query = search.value.toLowerCase();
        const nomCompleto = `${item.cita?.paciente?.persona?.nombre} ${item.cita?.paciente?.persona?.apellido}`.toLowerCase();

        return (
            nomCompleto.includes(query) ||
            item.tipo_diagnostico.toLowerCase().includes(query) ||
            item.sintomas.toLowerCase().includes(query) ||
            item.gravedad.toLowerCase().includes(query) ||
            (item.observaciones?.toLowerCase() || '').includes(query)
        );
    });
});
</script>

<template>
    <Head title="Diagnósticos" />

    <AuthenticatedLayout>
        <template #title>
            Diagnósticos
        </template>

        <div class="space-y-6 p-4 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300 sm:p-6 lg:p-8">
            <DiagnosticoHeader
                :citas-disponibles-count="citasDisponibles.length"
                :edit-mode="editMode"
                :show-form="showForm"
                :search="search"
                :results-count="diagnosticosFiltrados.length"
                @update:search="(val) => search = val"
                @open-form="openNewForm"
            />

            <DiagnosticoForm
                v-if="showForm"
                :diagnostico="selectedDiagnostico"
                :citas-select="citasSelect"
                :dientes="dientes"
                @saved="resetForm"
                @cancel="resetForm"
            />

            <!-- Table List or Empty State -->
            <DiagnosticoTable
                v-if="diagnosticosFiltrados.length > 0"
                :diagnosticos="diagnosticosFiltrados"
                @edit="editDiagnostico"
                @delete="deleteDiagnostico"
            />

            <DiagnosticoEmptyState v-else />
        </div>
    </AuthenticatedLayout>
</template>