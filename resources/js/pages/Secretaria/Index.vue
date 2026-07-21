<script setup lang="ts">
import { computed, nextTick, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Secretaria } from '@/types/Secretaria';
import type { PageProps } from '@/types';

import SecretariaHeader from '@/components/Secretaria/IndexComponents/SecretariaHeader.vue';
import SecretariaForm from '@/components/Secretaria/IndexComponents/SecretariaForm.vue';
import SecretariaTable from '@/components/Secretaria/IndexComponents/SecretariaTable.vue';
import SecretariaCardList from '@/components/Secretaria/IndexComponents/SecretariaCardList.vue';
import SecretariaEmptyState from '@/components/Secretaria/IndexComponents/SecretariaEmptyState.vue';

const props = defineProps<{
    secretarias: Array<Secretaria>;
}>();

const search = ref('');
const showForm = ref(false);
const editMode = ref(false);
const selectedSecretariaId = ref<string | null>(null);
const formSection = ref<HTMLElement | null>(null);

const page = usePage<PageProps>();
const authUser = computed(() => page.props.auth.user);
const permisos = computed<string[]>(() => page.props.auth.permisos ?? []);

const tienePermiso = (permiso: string) => {
    return permisos.value.includes(permiso);
};

const selectedSecretaria = computed(() => {
    if (!selectedSecretariaId.value) return null;
    return props.secretarias.find((s) => s.codigo_secretaria === selectedSecretariaId.value) ?? null;
});

const secretariasFiltradas = computed(() => {
    return props.secretarias.filter((secretaria) => {
        const usuario = secretaria.persona?.usuarios?.[0];
        const texto = `
            ${secretaria.codigo_secretaria}
            ${secretaria.persona?.nombre ?? ''}
            ${secretaria.persona?.apellido ?? ''}
            ${secretaria.persona_id}
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

const editSecretaria = async (secretaria: Secretaria) => {
    selectedSecretariaId.value = secretaria.codigo_secretaria;
    editMode.value = true;
    showForm.value = true;
    await desplazarAlFormulario();
};

const handleToggleForm = async () => {
    if (showForm.value) {
        cerrarFormulario();
    } else {
        selectedSecretariaId.value = null;
        editMode.value = false;
        showForm.value = true;
        await desplazarAlFormulario();
    }
};

const eliminarSecretaria = (codigo: string) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta secretaria?')) {
        router.delete(route('secretaria.destroy', codigo), {
            preserveScroll: true,
        });
    }
};

const cerrarFormulario = () => {
    showForm.value = false;
    editMode.value = false;
    selectedSecretariaId.value = null;
};
</script>

<template>
    <Head title="Secretarias" />

    <AuthenticatedLayout>
        <template #title>
            Secretarias
        </template>

        <section ref="formSection" class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Premium Header -->
            <SecretariaHeader
                :search="search"
                :results-count="secretariasFiltradas.length"
                :show-form="showForm"
                :can-create="tienePermiso('secretaria.update')"
                @update:search="(val) => search = val"
                @toggle-form="handleToggleForm"
            />

            <!-- Form -->
            <SecretariaForm
                v-if="showForm"
                :secretaria="selectedSecretaria"
                :edit-mode="editMode"
                @saved="cerrarFormulario"
                @cancel="cerrarFormulario"
            />

            <!-- Desktop & Mobile Lists -->
            <div v-if="secretariasFiltradas.length" class="space-y-6">
                <SecretariaTable
                    :secretarias="secretariasFiltradas"
                    :tiene-permiso="tienePermiso"
                    @edit="editSecretaria"
                />

                <SecretariaCardList
                    :secretarias="secretariasFiltradas"
                    :tiene-permiso="tienePermiso"
                    @edit="editSecretaria"
                    @delete="eliminarSecretaria"
                />
            </div>

            <!-- Empty State -->
            <SecretariaEmptyState
                v-else
                :search="search"
            />
        </section>
    </AuthenticatedLayout>
</template>