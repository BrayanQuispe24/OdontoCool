<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { HistorialClinico } from '@/types/HistorialClinico';
import type { Paciente } from '@/types/Paciente';

import HistorialClinicoForm from '@/components/HistorialClinico/IndexComponents/HistorialClinicoForm.vue';
import HistorialClinicoTable from '@/components/HistorialClinico/IndexComponents/HistorialClinicoTable.vue';
import HistorialClinicoCardList from '@/components/HistorialClinico/IndexComponents/HistorialClinicoCardList.vue';
import HistorialClinicoEmptyState from '@/components/HistorialClinico/IndexComponents/HistorialClinicoEmptyState.vue';

const props = defineProps<{
    historiales: Array<HistorialClinico>;
    pacientesDisponibles: Array<Paciente>;
    pacientesTodos: Array<Paciente>;
}>();

const search = ref('');
const showForm = ref(false);
const editMode = ref(false);
const selectedHistorialId = ref<number | null>(null);

const selectedHistorial = computed(() => {
    if (!selectedHistorialId.value) return null;
    return props.historiales.find((item) => item.codigo_historial === selectedHistorialId.value) ?? null;
});

const selectPacientes = computed(() => {
    if (editMode.value) {
        return props.pacientesTodos;
    }
    return props.pacientesDisponibles;
});

const openNewHistorial = () => {
    selectedHistorialId.value = null;
    editMode.value = false;
    showForm.value = true;
};

const editHistorial = (item: HistorialClinico) => {
    selectedHistorialId.value = item.codigo_historial;
    editMode.value = true;
    showForm.value = true;
};

const resetForm = () => {
    showForm.value = false;
    editMode.value = false;
    selectedHistorialId.value = null;
};

const deleteHistorial = (codigo: number) => {
    if (confirm('¿Estás seguro de que deseas inactivar este historial clínico? El paciente mantendrá su registro pero el historial pasará a estado INACTIVO.')) {
        router.delete(route('historial_clinico.destroy', codigo), {
            preserveScroll: true,
        });
    }
};

const historialesFiltrados = computed(() => {
    return props.historiales.filter((item) => {
        const query = search.value.toLowerCase();
        const nomCompleto = `${item.paciente?.persona?.nombre} ${item.paciente?.persona?.apellido}`.toLowerCase();

        return (
            nomCompleto.includes(query) ||
            item.paciente_ci.toLowerCase().includes(query) ||
            item.motivo_apertura.toLowerCase().includes(query) ||
            (item.alergias?.toLowerCase() || '').includes(query) ||
            (item.enfermedades_base?.toLowerCase() || '').includes(query)
        );
    });
});
</script>

<template>
    <Head title="Historiales Clínicos" />

    <AuthenticatedLayout>
        <template #title> Historiales Clínicos </template>

        <div class="space-y-6 p-4 text-[var(--text)] transition-colors duration-300 sm:p-6 lg:p-8">
            <!-- Alert message for empty patients available -->
            <div v-if="pacientesDisponibles.length === 0 && !editMode"
                class="flex items-center gap-3 rounded-2xl border p-4 text-[length:var(--font-sm)] bg-amber-500/5 text-amber-600 border-amber-500/20 dark:border-amber-900/40">
                <span>⚠️</span>
                <span>
                    Todos los pacientes registrados ya cuentan con un historial clínico. No hay más pacientes disponibles para nuevos historiales.
                </span>
            </div>

            <!-- Header Card (Premium Layout matching the screenshot) -->
            <div class="flex flex-col gap-4 rounded-3xl border p-6 shadow-[0_15px_40px_rgba(0,169,157,0.04)] border-[var(--border)] bg-[var(--card)] lg:flex-row lg:items-center lg:justify-between transition-colors duration-300">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[var(--primary)]">
                        Historiales Clínicos
                    </p>
                    <h1 class="text-xl font-black text-[var(--title)] mt-1">
                        Listado de Pacientes y Fichas
                    </h1>
                    <p class="text-[length:var(--font-xs)] text-[var(--muted)] mt-1">
                        Administra las fichas de antecedentes médicos, alergias y cronograma clínico.
                    </p>
                </div>

                <!-- Filters & Action Buttons -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Search bar -->
                    <div class="relative w-full max-w-[300px] sm:w-auto">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                            ⌕
                        </span>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar historial..."
                            class="w-full rounded-2xl border bg-[var(--section-soft)] py-3 pl-10 pr-5 text-[length:var(--font-sm)] text-[var(--title)] border-[var(--border)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                            style="--tw-ring-color: var(--primary-soft)"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-2xl bg-[var(--section-soft)] px-4 py-3 text-xs font-bold text-[var(--muted)] border border-[var(--border)]">
                            {{ historialesFiltrados.length }} resultado(s)
                        </span>

                        <button v-if="!showForm && pacientesDisponibles.length > 0" type="button" @click="openNewHistorial"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-lg shadow-teal-200 transition hover:bg-[var(--primary-hover)] hover:shadow-none dark:shadow-none active:scale-95 duration-150">
                            + Nuevo Historial Clínico
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form: Collapsible Card -->
            <HistorialClinicoForm
                v-if="showForm"
                :historial="selectedHistorial"
                :edit-mode="editMode"
                :pacientes="selectPacientes"
                @saved="resetForm"
                @cancel="resetForm"
            />

            <!-- List Table / Desktop Table & Mobile Cards -->
            <div v-if="historialesFiltrados.length" class="space-y-6">
                <HistorialClinicoTable
                    :historiales="historialesFiltrados"
                    @edit="editHistorial"
                    @delete="deleteHistorial"
                />

                <HistorialClinicoCardList
                    :historiales="historialesFiltrados"
                    @edit="editHistorial"
                    @delete="deleteHistorial"
                />
            </div>

            <!-- Empty State -->
            <HistorialClinicoEmptyState
                v-else
                :show-form="showForm"
                :pacientes-disponibles-count="pacientesDisponibles.length"
                @register-first="openNewHistorial"
            />
        </div>
    </AuthenticatedLayout>
</template>