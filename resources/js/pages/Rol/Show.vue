<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Permiso } from '@/types/Permiso';
import { rolWithPermisos } from '@/types/Rol';
import { computed, ref } from 'vue';
import { Modulo } from '@/types/Modulo';
import RolShowHeader from '@/components/Rol/ShowComponents/RolShowHeader.vue';
import RolShowDetails from '@/components/Rol/ShowComponents/RolShowDetails.vue';
import RolShowPermisosAsignar from '@/components/Rol/ShowComponents/RolShowPermisosAsignar.vue';
import RolShowPermisosTabla from '@/components/Rol/ShowComponents/RolShowPermisosTabla.vue';
import RolShowPermisosEmptyState from '@/components/Rol/ShowComponents/RolShowPermisosEmptyState.vue';

const props = defineProps<{
    rolPermisos: rolWithPermisos;
    permisos: Permiso[];
    modulos: Modulo[];
}>();

const form = useForm({});
const moduloSeleccionado = ref('');

const asignarForm = useForm({
    permiso_id: '',
});

const permisosFiltradosPorModulo = computed(() => {
    if (!moduloSeleccionado.value) {
        return props.permisos;
    }

    return props.permisos.filter((permiso) =>
        permiso.modulo_id === Number(moduloSeleccionado.value)
    );
});

const eliminarPermiso = (permisoId: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este permiso del rol?')) {
        form.delete(route('rol.eliminarPermiso', { rol_id: props.rolPermisos.id, permiso_id: permisoId }));
    }
};

const asignarPermiso = () => {
    asignarForm.post(route('rol.asignarPermiso', props.rolPermisos.id), {
        preserveScroll: true,
        onSuccess: () => {
            asignarForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Detalle del rol" />

    <AuthenticatedLayout>
        <template #title> Detalle del rol </template>

        <section class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Header Card -->
            <RolShowHeader :nombre="rolPermisos.nombre" />

            <!-- Details Card -->
            <RolShowDetails :rol-permisos="rolPermisos" />

            <!-- Permisos Card -->
            <div
                class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
                style="border-color: var(--border)"
            >
                <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                            Permisos
                        </p>

                        <h2 class="mt-1 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                            Permisos asignados al rol
                        </h2>

                        <p class="mt-1 text-[length:var(--font-sm)] text-[var(--muted)]">
                            Lista de permisos vinculados a este rol dentro del sistema.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--primary-soft)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        {{ rolPermisos.permisos?.length || 0 }} permiso(s)
                    </div>
                </div>

                <!-- Form Asignar Permiso -->
                <RolShowPermisosAsignar
                    :modulos="modulos"
                    :permisos-filtrados="permisosFiltradosPorModulo"
                    v-model:modulo-seleccionado="moduloSeleccionado"
                    :asignar-form="asignarForm"
                    @submit="asignarPermiso"
                />

                <!-- Permisos List / Table -->
                <RolShowPermisosTabla
                    v-if="rolPermisos.permisos && rolPermisos.permisos.length"
                    :permisos="rolPermisos.permisos"
                    @delete="eliminarPermiso"
                />

                <!-- Empty State -->
                <RolShowPermisosEmptyState v-else />
            </div>
        </section>
    </AuthenticatedLayout>
</template>