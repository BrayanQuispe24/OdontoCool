<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Turno } from '@/types/Turno';
import TurnoHeader from '@/components/Turno/IndexComponents/TurnoHeader.vue';
import TurnoForm from '@/components/Turno/IndexComponents/TurnoForm.vue';
import TurnoCardList from '@/components/Turno/IndexComponents/TurnoCardList.vue';
import TurnoEmptyState from '@/components/Turno/IndexComponents/TurnoEmptyState.vue';

const props = defineProps<{
    turnos: Array<Turno>;
}>();

const search = ref('');
const showForm = ref(false);
const editMode = ref(false);
const selectedTurnoId = ref<number | null>(null);
const formSection = ref<HTMLElement | null>(null);
const estados = ref(['activo', 'inactivo', 'suspendido']);

const form = useForm({
    nombre: '',
    estado: 'activo',
    hora_inicio: '',
    hora_fin: '',
});

const turnosFiltrados = computed(() => {
    return props.turnos.filter((turno) =>
        turno.nombre.toLowerCase().includes(search.value.toLowerCase())
    );
});

const editTurno = (turno: Turno) => {
    editMode.value = true;
    selectedTurnoId.value = turno.id;
    showForm.value = true;

    form.nombre = turno.nombre;
    form.hora_inicio = turno.hora_inicio;
    form.hora_fin = turno.hora_fin;
    form.estado = turno.estado;

    goToForm();
};

const cerrarFormulario = () => {
    form.reset();
    form.clearErrors();
    showForm.value = false;
    editMode.value = false;
    selectedTurnoId.value = null;
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

const submit = () => {
    if (editMode.value && selectedTurnoId.value) {
        form.put(route('turnos.update', selectedTurnoId.value), {
            preserveScroll: true,
            onSuccess: () => {
                cerrarFormulario();
            },
        });
        return;
    }

    form.post(route('turnos.store'), {
        preserveScroll: true,
        onSuccess: () => {
            cerrarFormulario();
        },
    });
};

const eliminarTurno = (turnoId: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este turno?')) {
        form.delete(route('turno.destroy', turnoId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Turnos" />

    <AuthenticatedLayout>
        <template #title>
            Turnos
        </template>

        <section
            ref="formSection"
            class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300"
        >
            <!-- Header Card -->
            <TurnoHeader
                v-model:search="search"
                :show-form="showForm"
                @toggle-form="showForm ? cerrarFormulario() : goToForm()"
            />

            <!-- Form Card -->
            <TurnoForm
                v-if="showForm"
                :form="form"
                :edit-mode="editMode"
                :estados="estados"
                @submit="submit"
                @cancel="cerrarFormulario"
            />

            <!-- Card List -->
            <TurnoCardList
                v-if="turnosFiltrados.length"
                :turnos="turnosFiltrados"
                @edit="editTurno"
                @delete="eliminarTurno"
            />

            <!-- Empty State -->
            <TurnoEmptyState v-else />
        </section>
    </AuthenticatedLayout>
</template>