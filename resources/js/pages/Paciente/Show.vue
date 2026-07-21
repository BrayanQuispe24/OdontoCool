<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { PacienteShow } from '@/types/Paciente';
import type { PageProps } from '@/types';

import PacienteShowHeader from '@/components/Paciente/ShowComponents/PacienteShowHeader.vue';
import PacienteShowDetails from '@/components/Paciente/ShowComponents/PacienteShowDetails.vue';

const props = defineProps<{
    paciente: PacienteShow;
}>();

const page = usePage<PageProps>();
const authUser = computed(() => page.props.auth.user);
const permisos = computed<string[]>(() => page.props.auth.permisos ?? []);

const tienePermiso = (permiso: string) => {
    return permisos.value.includes(permiso);
};

const tieneRol = (rol: string) => {
    return authUser.value?.rol?.nombre === rol;
};

const editando = ref(false);
const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';

const assetUrl = (path: string | null | undefined): string | undefined => {
    if (!path) return undefined;
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('blob:')) {
        return path;
    }
    const base = publicBase.replace(/\/+$/, '');
    const cleanPath = path.replace(/^\/+/, '');
    return `${base}/${cleanPath}`;
};

const usuario = computed(() => {
    return props.paciente.persona?.usuarios?.[0] ?? null;
});

const nombreCompleto = computed(() => {
    const nombre = props.paciente.persona?.nombre ?? '';
    const apellido = props.paciente.persona?.apellido ?? '';
    return `${nombre} ${apellido}`.trim();
});

const iniciales = computed(() => {
    const nombre = props.paciente.persona?.nombre?.charAt(0) ?? '';
    const apellido = props.paciente.persona?.apellido?.charAt(0) ?? '';
    return `${nombre}${apellido}`.toUpperCase();
});

const previewFoto = ref<string | null>(usuario.value?.url ?? null);

const cambiarFoto = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] || null;

    if (!file) {
        form.foto_perfil = null;
        return;
    }

    const maxSizeBytes = 2 * 1024 * 1024;
    if (file.size > maxSizeBytes) {
        form.errors.foto_perfil = 'La imagen es demasiado grande. El límite máximo permitido es de 2 MB.';
        form.foto_perfil = null;
        input.value = '';
        return;
    }

    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        form.errors.foto_perfil = 'Formato no permitido. Usa JPG, PNG o WEBP.';
        form.foto_perfil = null;
        input.value = '';
        return;
    }

    form.clearErrors('foto_perfil');
    form.foto_perfil = file;
    previewFoto.value = URL.createObjectURL(file);
};

const form = useForm({
    _method: 'put',
    email: usuario.value?.email ?? '',
    estado: usuario.value?.estado ?? 'activo',
    url: usuario.value?.url ?? null,
    foto_perfil: null as File | null,

    carnet_identidad: props.paciente.persona?.carnet_identidad ?? '',
    nombre: props.paciente.persona?.nombre ?? '',
    apellido: props.paciente.persona?.apellido ?? '',
    telefono: props.paciente.persona?.telefono ?? '',
    fecha_nacimiento: props.paciente.persona?.fecha_nacimiento ?? '',
    direccion: props.paciente.persona?.direccion ?? '',
    genero: props.paciente.persona?.genero ?? '',

    contacto_emergencia: props.paciente.contacto_emergencia ?? '',
    telefono_emergencia: props.paciente.telefono_emergencia ?? '',
});

const activarEdicion = () => {
    editando.value = true;
};

const cancelarEdicion = () => {
    form.reset();
    form.clearErrors();
    previewFoto.value = usuario.value?.url ?? null;
    editando.value = false;
};

const guardarCambios = () => {
    form.post(route('paciente.update', props.paciente.codigo_paciente), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            editando.value = false;
            form.foto_perfil = null;
        },
    });
};

const passwordForm = useForm({
    password: '',
    password_confirmation: '',
});

const cambiarPassword = () => {
    if (!usuario.value?.codigo_usuario) return;
    passwordForm.patch(route('usuario.cambiar-password', usuario.value.codigo_usuario), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Detalle del paciente" />

    <AuthenticatedLayout>
        <template #title>
            Detalle del paciente
        </template>

        <section class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Header Banner -->
            <PacienteShowHeader
                :paciente="paciente"
                :editando="editando"
                :preview-foto="previewFoto"
                :iniciales="iniciales"
                :tiene-rol="tieneRol"
                :asset-url="assetUrl"
                :processing="form.processing"
                :foto-error="form.errors.foto_perfil"
                @toggle-edit="activarEdicion"
                @cancel-edit="cancelarEdicion"
                @save="guardarCambios"
                @change-photo="cambiarFoto"
            />

            <!-- Form and Details Sections -->
            <form class="space-y-6" @submit.prevent="guardarCambios">
                <PacienteShowDetails
                    :form="form"
                    :password-form="passwordForm"
                    :editando="editando"
                    :paciente="paciente"
                    :usuario="usuario"
                    :iniciales="iniciales"
                    :preview-foto="previewFoto"
                    :tiene-rol="tieneRol"
                    :asset-url="assetUrl"
                    @change-photo="cambiarFoto"
                    @submit-password="cambiarPassword"
                />
            </form>
        </section>
    </AuthenticatedLayout>
</template>