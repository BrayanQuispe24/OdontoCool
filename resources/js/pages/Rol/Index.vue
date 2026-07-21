<script setup lang="ts">
import { Rol } from '@/types/Rol';
import { computed, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RolHeader from '@/components/Rol/IndexComponents/RolHeader.vue';
import RolForm from '@/components/Rol/IndexComponents/RolForm.vue';
import RolCardList from '@/components/Rol/IndexComponents/RolCardList.vue';
import RolEmptyState from '@/components/Rol/IndexComponents/RolEmptyState.vue';

const props = defineProps<{
    roles: Array<Rol>;
}>();

const search = ref('');
const showForm = ref(false);
const editMode = ref(false);
const selectedRolId = ref<number | null>(null);
const formSection = ref<HTMLElement | null>(null);

const form = useForm({
    nombre: '',
    estado: 'activo',
    description: '',
});

const rolesFiltrados = computed(() => {
    return props.roles.filter((rol) =>
        rol.nombre.toLowerCase().includes(search.value.toLowerCase())
    );
});

const editRol = (rol: Rol) => {
    editMode.value = true;
    selectedRolId.value = rol.id;
    showForm.value = true;

    form.nombre = rol.nombre;
    form.estado = rol.estado;
    form.description = rol.description;

    goToForm();
};

const cerrarFormulario = () => {
    form.reset();
    form.clearErrors();
    showForm.value = false;
    editMode.value = false;
    selectedRolId.value = null;
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
    if (editMode.value && selectedRolId.value) {
        form.put(route('rol.update', selectedRolId.value), {
            preserveScroll: true,
            onSuccess: () => {
                cerrarFormulario();
            },
        });
        return;
    }

    form.post(route('rol.store'), {
        preserveScroll: true,
        onSuccess: () => {
            cerrarFormulario();
        },
    });
};

const eliminarRol = (rolId: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este rol?')) {
        form.delete(route('rol.destroy', rolId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Roles" />

    <AuthenticatedLayout>
        <template #title> Roles </template>

        <section
            ref="formSection"
            class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300"
        >
            <!-- Header Card -->
            <RolHeader
                :show-form="showForm"
                @toggle-form="showForm ? cerrarFormulario() : goToForm()"
            />

            <!-- Form Card -->
            <RolForm
                v-if="showForm"
                :form="form"
                :edit-mode="editMode"
                @submit="submit"
                @cancel="cerrarFormulario"
            />

            <!-- Search Card -->
            <div
                class="flex flex-col gap-4 rounded-3xl border bg-[var(--card)] p-5 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 md:flex-row md:items-center md:justify-between"
                style="border-color: var(--border)"
            >
                <div class="relative w-full md:max-w-md">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]"
                    >
                        ⌕
                    </span>

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar rol..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                    />
                </div>

                <div
                    class="rounded-2xl bg-[var(--primary-soft)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)]"
                >
                    {{ rolesFiltrados.length }} resultado(s)
                </div>
            </div>

            <!-- Card List -->
            <RolCardList
                v-if="rolesFiltrados.length"
                :roles="rolesFiltrados"
                @edit="editRol"
                @delete="eliminarRol"
            />

            <!-- Empty State -->
            <RolEmptyState v-else />
        </section>
    </AuthenticatedLayout>
</template>