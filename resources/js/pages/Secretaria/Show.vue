<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { SecretariaShow } from '@/types/Secretaria';
import type { Turno } from '@/types/Turno';
import type { PageProps } from '@/types';

import SecretariaShowHeader from '@/components/Secretaria/ShowComponents/SecretariaShowHeader.vue';
import SecretariaShowDetails from '@/components/Secretaria/ShowComponents/SecretariaShowDetails.vue';
import SecretariaShowTurnos from '@/components/Secretaria/ShowComponents/SecretariaShowTurnos.vue';

const props = defineProps<{
    secretaria: SecretariaShow;
    turnos: Turno[];
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

const editando = ref(false);

const diasSemana = [
    { value: 'lunes', label: 'Lunes' },
    { value: 'martes', label: 'Martes' },
    { value: 'miercoles', label: 'Miércoles' },
    { value: 'jueves', label: 'Jueves' },
    { value: 'viernes', label: 'Viernes' },
    { value: 'sabado', label: 'Sábado' },
    { value: 'domingo', label: 'Domingo' },
];

const usuario = computed(() => {
    return props.secretaria.persona?.usuarios?.[0] ?? null;
});

const nombreCompleto = computed(() => {
    const nombre = props.secretaria.persona?.nombre ?? '';
    const apellido = props.secretaria.persona?.apellido ?? '';
    return `${nombre} ${apellido}`.trim();
});

const iniciales = computed(() => {
    const nombre = props.secretaria.persona?.nombre?.charAt(0) ?? '';
    const apellido = props.secretaria.persona?.apellido?.charAt(0) ?? '';
    return `${nombre}${apellido}`.toUpperCase();
});

const previewFoto = ref<string | null>(usuario.value?.url ?? null);
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

    carnet_identidad: props.secretaria.persona?.carnet_identidad ?? '',
    nombre: props.secretaria.persona?.nombre ?? '',
    apellido: props.secretaria.persona?.apellido ?? '',
    telefono: props.secretaria.persona?.telefono ?? '',
    fecha_nacimiento: props.secretaria.persona?.fecha_nacimiento ?? '',
    direccion: props.secretaria.persona?.direccion ?? '',
    genero: props.secretaria.persona?.genero ?? '',

    fecha_contratacion: props.secretaria.fecha_contratacion ?? '',
});

const turnosAsignados = computed<Turno[]>(() => {
    return (props.secretaria.turnos ?? []) as Turno[];
});

const turnosActualesPayload = computed(() => {
    return turnosAsignados.value
        .map((turno) => ({
            id: turno.id,
            dia_semana: turno.pivot?.dia_semana ?? '',
        }))
        .filter((turno) => turno.dia_semana !== '');
});

const turnoForm = useForm({
    turno_id: '',
    dias_semana: [] as string[],
    turnos: [] as Array<{
        id: number | string;
        dia_semana: string;
    }>,
});

const activarEdicion = () => {
    editando.value = true;
};

const cancelarEdicion = () => {
    form.reset();
    form.clearErrors();
    form.foto_perfil = null;
    previewFoto.value = usuario.value?.url ?? null;

    turnoForm.reset();
    turnoForm.clearErrors();

    editando.value = false;
};

const guardarCambios = () => {
    form.post(route('secretaria.update', props.secretaria.codigo_secretaria), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            editando.value = false;
            form.foto_perfil = null;
        },
    });
};

const guardarTurno = () => {
    const nuevosTurnos = turnoForm.dias_semana.map((dia) => ({
        id: turnoForm.turno_id,
        dia_semana: dia,
    }));

    const turnosSinDuplicados = nuevosTurnos.filter((nuevoTurno) => {
        return !turnosActualesPayload.value.some((turnoActual) => {
            return (
                Number(turnoActual.id) === Number(nuevoTurno.id) &&
                turnoActual.dia_semana === nuevoTurno.dia_semana
            );
        });
    });

    const turnosActualizados = [
        ...turnosActualesPayload.value,
        ...turnosSinDuplicados,
    ];

    turnoForm
        .transform(() => ({
            turnos: turnosActualizados,
        }))
        .post(route('secretaria.asignar-turno', props.secretaria.codigo_secretaria), {
            preserveScroll: true,
            onSuccess: () => {
                turnoForm.reset();
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
    <Head title="Detalle de la secretaria" />

    <AuthenticatedLayout>
        <template #title>
            Detalle de la secretaria
        </template>

        <section class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Header Banner -->
            <SecretariaShowHeader
                :secretaria="secretaria"
                :editando="editando"
                :preview-foto="previewFoto"
                :iniciales="iniciales"
                :tiene-rol="tieneRol"
                :tiene-permiso="tienePermiso"
                :asset-url="assetUrl"
                :processing="form.processing"
                :foto-error="form.errors.foto_perfil"
                @toggle-edit="activarEdicion"
                @cancel-edit="cancelarEdicion"
                @save="guardarCambios"
                @change-photo="cambiarFoto"
            />

            <!-- Form details and Shifts -->
            <form class="space-y-6" @submit.prevent="guardarCambios">
                <SecretariaShowDetails
                    :form="form"
                    :password-form="passwordForm"
                    :editando="editando"
                    :secretaria="secretaria"
                    :usuario="usuario"
                    :iniciales="iniciales"
                    :preview-foto="previewFoto"
                    :tiene-rol="tieneRol"
                    :asset-url="assetUrl"
                    @change-photo="cambiarFoto"
                    @submit-password="cambiarPassword"
                />

                <SecretariaShowTurnos
                    :turnos-asignados="turnosAsignados"
                    :turnos="turnos"
                    :turno-form="turnoForm"
                    :editando="editando"
                    :dias-semana="diasSemana"
                    @submit-turno="guardarTurno"
                />
            </form>
        </section>
    </AuthenticatedLayout>
</template>