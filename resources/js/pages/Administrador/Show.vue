```vue
<script setup lang="ts">
import { computed, onBeforeUnmount, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { AdministradorShow } from '@/types/Propietario';
import type { PageProps } from '@/types';
import AdministradorProfileHeader from '@/components/Administrador/AdministradorProfileHeader.vue';
import AdministradorUserData from '@/components/Administrador/AdministradorUserData.vue';
import AdministradorBusinessData from '@/components/Administrador/AdministradorBusinessData.vue';
import Administrador from '../Usuario/Administrador.vue';
import AdministradorPasswordForm from '@/components/Administrador/AdministradorPasswordForm.vue';

const props = defineProps<{
    administrador: AdministradorShow;
}>();

const page = usePage<PageProps>();

const authUser = computed(() => page.props.auth.user);

const editando = ref(false);

const usuario = computed(() => {
    return props.administrador.persona?.usuarios?.[0] ?? null;
});

const nombreCompleto = computed(() => {
    const nombre = props.administrador.persona?.nombre ?? '';
    const apellido = props.administrador.persona?.apellido ?? '';

    return `${nombre} ${apellido}`.trim();
});

const iniciales = computed(() => {
    const nombre = props.administrador.persona?.nombre?.charAt(0) ?? '';
    const apellido = props.administrador.persona?.apellido?.charAt(0) ?? '';

    return `${nombre}${apellido}`.toUpperCase();
});

const tieneRol = (rol: string): boolean => {
    return authUser.value?.rol?.nombre === rol;
};

const previewFoto = ref<string | null>(
    usuario.value?.url ?? null,
);

const previewTemporal = ref<string | null>(null);

const form = useForm({
    _method: 'put',

    // Usuario
    email: usuario.value?.email ?? '',
    estado: usuario.value?.estado ?? 'activo',
    url: usuario.value?.url ?? null,
    foto_perfil: null as File | null,

    // Persona
    carnet_identidad:
        props.administrador.persona?.carnet_identidad ?? '',
    nombre: props.administrador.persona?.nombre ?? '',
    apellido: props.administrador.persona?.apellido ?? '',
    telefono: props.administrador.persona?.telefono ?? '',
    fecha_nacimiento:
        props.administrador.persona?.fecha_nacimiento ?? '',
    direccion: props.administrador.persona?.direccion ?? '',
    genero: props.administrador.persona?.genero ?? '',

    // Propietario
    fecha_inicio: props.administrador.fecha_inicio ?? '',
    porcentaje_participacion:
        props.administrador.porcentaje_participacion ?? '',
});

const liberarPreviewTemporal = () => {
    if (!previewTemporal.value) {
        return;
    }

    URL.revokeObjectURL(previewTemporal.value);
    previewTemporal.value = null;
};

const activarEdicion = () => {
    form.clearErrors();
    editando.value = true;
};

const cancelarEdicion = () => {
    liberarPreviewTemporal();

    form.reset();
    form.clearErrors();

    form.foto_perfil = null;
    previewFoto.value = usuario.value?.url ?? null;

    editando.value = false;
};

const cambiarFoto = (file: File | null) => {
    form.clearErrors('foto_perfil');

    if (!file) {
        liberarPreviewTemporal();

        form.foto_perfil = null;
        previewFoto.value = usuario.value?.url ?? null;

        return;
    }

    const maxSizeBytes = 2 * 1024 * 1024;

    if (file.size > maxSizeBytes) {
        form.setError(
            'foto_perfil',
            'La imagen no puede superar los 2 MB.',
        );

        form.foto_perfil = null;
        return;
    }

    const tiposPermitidos = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
    ];

    if (!tiposPermitidos.includes(file.type)) {
        form.setError(
            'foto_perfil',
            'El archivo debe ser una imagen JPG, PNG o WEBP.',
        );

        form.foto_perfil = null;
        return;
    }

    liberarPreviewTemporal();

    previewTemporal.value = URL.createObjectURL(file);

    form.foto_perfil = file;
    previewFoto.value = previewTemporal.value;
};

const guardarCambios = () => {
    if (form.processing) {
        return;
    }

    form.post(
        route(
            'administrador.update',
            props.administrador.codigo_propietario,
        ),
        {
            forceFormData: true,
            preserveScroll: true,

            onSuccess: () => {
                liberarPreviewTemporal();

                form.foto_perfil = null;
                form.clearErrors();

                editando.value = false;
            },

            onError: (errors) => {
                console.error(
                    'No se pudo actualizar el propietario:',
                    errors,
                );
            },
        },
    );
};

onBeforeUnmount(() => {
    liberarPreviewTemporal();
});
</script>

<template>

    <Head title="Detalle del propietario" />

    <AuthenticatedLayout>
        <template #title>
            Detalle del propietario
        </template>

        <section class="space-y-6 text-[var(--text)] transition-colors duration-300">
            <AdministradorProfileHeader :usuario="usuario" :nombre-completo="nombreCompleto" :iniciales="iniciales"
                :preview-foto="previewFoto" :editando="editando" :processing="form.processing"
                :mostrar-volver="!tieneRol('Propietario')" :error-foto="form.errors.foto_perfil" @edit="activarEdicion"
                @cancel="cancelarEdicion" @save="guardarCambios" @photo-change="cambiarFoto" />

            <div class="grid gap-5 md:grid-cols-2">
                <AdministradorUserData v-model:email="form.email" v-model:estado="form.estado" :usuario="usuario"
                    :url="form.url" :preview-foto="previewFoto" :iniciales="iniciales" :editando="editando"
                    :errors="form.errors" @photo-change="cambiarFoto" />

                <AdministradorPasswordForm v-if="editando && usuario?.codigo_usuario"
                    :codigo-usuario="usuario.codigo_usuario" />
            </div>
            <AdministradorBusinessData v-model:fecha-inicio="form.fecha_inicio"
                v-model:porcentaje-participacion="form.porcentaje_participacion"
                :codigo-propietario="administrador.codigo_propietario" :editando="editando" :errors="form.errors" />
        </section>
    </AuthenticatedLayout>
</template>
```
