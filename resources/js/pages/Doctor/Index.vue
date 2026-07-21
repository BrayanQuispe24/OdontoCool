<script setup lang="ts">
import { computed, nextTick, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Doctor } from '@/types/doctor';
import type { PageProps } from '@/types';

import DoctorHeader from '@/components/Doctor/DoctorHeader.vue';
import DoctorForm from '@/components/Doctor/DoctorForm.vue';
import DoctorTable from '@/components/Doctor/DoctorTable.vue';
import DoctorCardList from '@/components/Doctor/DoctorCardList.vue';
import DoctorEmptyState from '@/components/Doctor/DoctorEmptyState.vue';

const props = defineProps<{
    doctores: Array<Doctor>;
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
const selectedDoctorId = ref<string | null>(null);
const formSection = ref<HTMLElement | null>(null);

const selectedDoctor = computed(() => {
    if (!selectedDoctorId.value) return null;
    return props.doctores.find((d) => d.codigo_doctor === selectedDoctorId.value) ?? null;
});

const doctoresFiltrados = computed(() => {
    return props.doctores.filter((doctor) => {
        const usuario = doctor.persona?.usuarios?.[0];

        const texto = `
            ${doctor.codigo_doctor}
            ${doctor.persona?.nombre ?? ''}
            ${doctor.persona?.apellido ?? ''}
            ${doctor.persona_id}
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

const editDoctor = async (doctor: Doctor) => {
    selectedDoctorId.value = doctor.codigo_doctor;
    editMode.value = true;
    showForm.value = true;
    await desplazarAlFormulario();
};

const handleToggleForm = async () => {
    if (showForm.value) {
        cerrarFormulario();
    } else {
        selectedDoctorId.value = null;
        editMode.value = false;
        showForm.value = true;
        await desplazarAlFormulario();
    }
};

const eliminarDoctor = (codigo: string) => {
    if (confirm('¿Estás seguro de que deseas eliminar este doctor?')) {
        router.delete(route('doctor.destroy', codigo), {
            preserveScroll: true,
        });
    }
};

const cerrarFormulario = () => {
    showForm.value = false;
    editMode.value = false;
    selectedDoctorId.value = null;
};
</script>

<template>
    <Head title="Doctores" />

    <AuthenticatedLayout>
        <template #title>
            Doctores
        </template>

        <section ref="formSection" class="space-y-6 text-[var(--text)] transition-colors duration-300">
            <!-- Unified Premium Header Card -->
            <DoctorHeader
                :search="search"
                :results-count="doctoresFiltrados.length"
                :show-form="showForm"
                :tiene-permiso-crear-or-editar="tienePermiso('doctor.create') || tienePermiso('doctor.update')"
                :es-doctor-or-admin="tieneRol('Doctor') || tieneRol('Administrador')"
                @update:search="(val) => search = val"
                @toggle-form="handleToggleForm"
            />

            <!-- Form -->
            <DoctorForm
                v-if="showForm"
                :doctor="selectedDoctor"
                :edit-mode="editMode"
                @saved="cerrarFormulario"
                @cancel="cerrarFormulario"
            />

            <!-- Lists (Desktop Table / Mobile Cards / Empty State) -->
            <div v-if="doctoresFiltrados.length" class="space-y-6">
                <!-- Desktop Table component -->
                <DoctorTable
                    :doctores="doctoresFiltrados"
                    :tiene-permiso-update="tienePermiso('doctor.update')"
                    :tiene-permiso-show="tienePermiso('doctor.show')"
                    @edit="editDoctor"
                />

                <!-- Mobile cards component -->
                <DoctorCardList
                    :doctores="doctoresFiltrados"
                    :tiene-permiso-update="tienePermiso('doctor.update')"
                    :tiene-permiso-destroy="tienePermiso('doctor.destroy')"
                    :tiene-permiso-show="tienePermiso('doctor.show')"
                    @edit="editDoctor"
                    @delete="eliminarDoctor"
                />
            </div>

            <!-- Empty search placeholder -->
            <DoctorEmptyState v-else />
        </section>
    </AuthenticatedLayout>
</template>