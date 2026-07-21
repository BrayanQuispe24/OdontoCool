<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { DoctorShow } from '@/types/doctor';
import type { Especialidad } from '@/types/Especialidad';
import type { Turno } from '@/types/Turno';
import type { PageProps } from '@/types';

import DoctorShowHeader from '@/components/Doctor/ShowComponents/DoctorShowHeader.vue';
import DoctorShowDetails from '@/components/Doctor/ShowComponents/DoctorShowDetails.vue';
import DoctorShowEspecialidades from '@/components/Doctor/ShowComponents/DoctorShowEspecialidades.vue';
import DoctorShowTurnos from '@/components/Doctor/ShowComponents/DoctorShowTurnos.vue';

type TurnoDoctor = Turno & {
    pivot?: {
        id?: number;
        doctor_id?: string;
        turno_id?: number;
        dia_semana?: string;
    };
};

const props = defineProps<{
    doctor: DoctorShow;
    especialidades: Especialidad[];
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
    return props.doctor.persona?.usuarios?.[0] ?? null;
});

const turnosAsignados = computed<TurnoDoctor[]>(() => {
    return (props.doctor.turnos ?? []) as TurnoDoctor[];
});

const turnosActualesPayload = computed(() => {
    return turnosAsignados.value
        .map((turno) => ({
            id: turno.id,
            dia_semana: turno.pivot?.dia_semana ?? '',
        }))
        .filter((turno) => turno.dia_semana !== '');
});

const nombreCompleto = computed(() => {
    const nombre = props.doctor.persona?.nombre ?? '';
    const apellido = props.doctor.persona?.apellido ?? '';
    return `${nombre} ${apellido}`.trim();
});

const iniciales = computed(() => {
    const nombre = props.doctor.persona?.nombre?.charAt(0) ?? '';
    const apellido = props.doctor.persona?.apellido?.charAt(0) ?? '';
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
    // Usuario
    _method: 'put',
    email: usuario.value?.email ?? '',
    estado: usuario.value?.estado ?? 'activo',
    url: usuario.value?.url ?? null,
    foto_perfil: null as File | null,

    // Persona
    carnet_identidad: props.doctor.persona?.carnet_identidad ?? '',
    nombre: props.doctor.persona?.nombre ?? '',
    apellido: props.doctor.persona?.apellido ?? '',
    telefono: props.doctor.persona?.telefono ?? '',
    fecha_nacimiento: props.doctor.persona?.fecha_nacimiento ?? '',
    direccion: props.doctor.persona?.direccion ?? '',
    genero: props.doctor.persona?.genero ?? '',

    // Doctor
    matricula_profesional: props.doctor.matricula_profesional ?? '',
    experiencia: props.doctor.experiencia ?? '',
    telefono_profesional: props.doctor.telefono_profesional ?? '',
    fecha_contratacion: props.doctor.fecha_contratacion ?? '',
});

const especialidadesForm = useForm({
    especialidades: props.doctor.especialidades?.map((especialidad) => especialidad.id) ?? [],
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

    especialidadesForm.especialidades =
        props.doctor.especialidades?.map((especialidad) => especialidad.id) ?? [];
    especialidadesForm.clearErrors();

    turnoForm.reset();
    turnoForm.clearErrors();

    editando.value = false;
};

const guardarCambios = () => {
    form.post(route('doctor.update', props.doctor.codigo_doctor), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            editando.value = false;
            form.foto_perfil = null;
        },
    });
};

const guardarEspecialidades = () => {
    especialidadesForm.post(route('doctor.asignar-especialidad', props.doctor.codigo_doctor), {
        preserveScroll: true,
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
        .post(route('doctor.asignar-turno', props.doctor.codigo_doctor), {
            preserveScroll: true,
            onSuccess: () => {
                turnoForm.reset();
            },
        });
};

// cambio de contraseña y email
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
    <Head title="Detalle del doctor" />

    <AuthenticatedLayout>
        <template #title>
            Detalle del doctor
        </template>

        <section class="space-y-6 text-[var(--text)] transition-colors duration-300">
            <!-- Portada y Acciones generales -->
            <DoctorShowHeader
                :doctor="doctor"
                :editando="editando"
                :preview-foto="previewFoto"
                :iniciales="iniciales"
                :nombre-completo="nombreCompleto"
                :usuario="usuario"
                :tiene-rol-user="tieneRol('Doctor')"
                :tiene-permiso-update="tienePermiso('doctor.update')"
                :form-processing="form.processing"
                @change-foto="cambiarFoto"
                @activar-edicion="activarEdicion"
                @cancelar-edicion="cancelarEdicion"
                @guardar-cambios="guardarCambios"
            />

            <!-- Formulario Principal -->
            <DoctorShowDetails
                :form="form"
                :password-form="passwordForm"
                :editando="editando"
                :usuario="usuario"
                :doctor="doctor"
                :iniciales="iniciales"
                :preview-foto="previewFoto"
                :puede-cambiar-estado="tieneRol('Administrador') || tieneRol('Propietario')"
                :puede-editar-contratacion="tieneRol('Administrador') || tieneRol('Propietario')"
                @change-foto="cambiarFoto"
                @cambiar-password="cambiarPassword"
            />

            <!-- Especialidades -->
            <DoctorShowEspecialidades
                :doctor="doctor"
                :especialidades="especialidades"
                :especialidades-form="especialidadesForm"
                :editando="editando"
                @guardar-especialidades="guardarEspecialidades"
            />

            <!-- Horarios y Turnos -->
            <DoctorShowTurnos
                :turnos-asignados="turnosAsignados"
                :turnos="turnos"
                :turno-form="turnoForm"
                :editando="editando"
                :dias-semana="diasSemana"
                :puede-asignar-turnos="tieneRol('Administrador') || tieneRol('Propietario')"
                @guardar-turno="guardarTurno"
            />
        </section>
    </AuthenticatedLayout>
</template>