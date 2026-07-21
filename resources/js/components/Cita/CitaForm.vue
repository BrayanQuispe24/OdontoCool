<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Cita, EstadoCita, Doctor } from '@/types/Cita';
import type { Paciente } from '@/types/Paciente';
import type { Secretaria } from '@/types/Secretaria';

const props = defineProps<{
    cita: Cita | null;
    pacientes: Paciente[];
    doctores: Doctor[];
    secretarias?: Secretaria[];
    estadoCitas: EstadoCita[];
    soloLecturaPaciente: boolean;
    esPaciente: boolean;
    pacienteLogueadoCi: string;
    esDoctor: boolean;
    doctorLogueadoCi: string;
    defaultDate?: string;
    defaultHour?: string;
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

const editMode = computed(() => props.cita !== null);

const form = useForm({
    fecha_cita: '',
    hora_inicio: '',
    motivo: '',
    observacion: '',
    paciente_ci: '',
    codigo_historial: '',
    secretaria_ci: '',
    doctor_ci: '',
    estado_cita_id: '',
});

const resetForm = () => {
    form.reset();
    form.clearErrors();
};

const cargarCita = (cita: Cita | null) => {
    resetForm();

    if (cita) {
        form.fecha_cita = cita.fecha_cita;
        form.hora_inicio = cita.hora_inicio?.substring(0, 5) ?? '';
        form.motivo = cita.motivo ?? '';
        form.observacion = cita.observacion ?? '';
        form.paciente_ci = cita.paciente_ci ?? '';
        form.codigo_historial = cita.codigo_historial ? String(cita.codigo_historial) : '';
        form.secretaria_ci = cita.secretaria_ci ?? '';
        form.doctor_ci = props.esDoctor ? props.doctorLogueadoCi : (cita.doctor_ci ?? '');
        form.estado_cita_id = '';
    } else {
        form.fecha_cita = props.defaultDate || '';
        form.hora_inicio = props.defaultHour || '';
        form.motivo = '';
        form.observacion = '';
        form.paciente_ci = props.esPaciente ? props.pacienteLogueadoCi : '';
        form.codigo_historial = '';
        form.secretaria_ci = '';
        form.doctor_ci = props.esDoctor ? props.doctorLogueadoCi : '';
        form.estado_cita_id = '';
    }
};

watch(
    () => props.cita,
    cargarCita,
    {
        immediate: true,
    },
);

const estadosParaFormulario = computed(() => {
    if (!props.esPaciente) {
        return props.estadoCitas;
    }

    return props.estadoCitas.filter((estado) => {
        const nombre = estado.nombre.toLowerCase();
        return nombre === 'cancelada' || nombre === 'cancelado';
    });
});

const diasSemana = [
    'domingo',
    'lunes',
    'martes',
    'miercoles',
    'jueves',
    'viernes',
    'sabado',
];

const normalizarTexto = (texto: string) => {
    return texto
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();
};

const doctorSeleccionado = computed(() => {
    return props.doctores.find((doctor) => {
        return doctor.codigo_doctor === form.doctor_ci;
    }) ?? null;
});

const turnosDoctorSeleccionado = computed(() => {
    return doctorSeleccionado.value?.turnos ?? [];
});

const diaSemanaSeleccionado = computed(() => {
    if (!form.fecha_cita) {
        return '';
    }

    const fecha = new Date(`${form.fecha_cita}T00:00:00`);
    return diasSemana[fecha.getDay()];
});

const horaDentroDeRango = (
    hora: string,
    horaInicio: string,
    horaFin: string
) => {
    const horaNormalizada = hora.substring(0, 5);
    const inicioNormalizado = horaInicio.substring(0, 5);
    const finNormalizado = horaFin.substring(0, 5);

    return horaNormalizada >= inicioNormalizado && horaNormalizada < finNormalizado;
};

const turnoValidoSeleccionado = computed(() => {
    if (!form.doctor_ci || !form.fecha_cita || !form.hora_inicio) {
        return null;
    }

    return turnosDoctorSeleccionado.value.find((turno) => {
        const mismoDia =
            normalizarTexto(turno.pivot?.dia_semana ?? '') === normalizarTexto(diaSemanaSeleccionado.value);

        const horaValida = horaDentroDeRango(
            form.hora_inicio,
            turno.hora_inicio,
            turno.hora_fin
        );

        return mismoDia && horaValida;
    }) ?? null;
});

const errorTurnoDoctor = computed(() => {
    if (props.soloLecturaPaciente) {
        return '';
    }

    if (!form.doctor_ci || !form.fecha_cita || !form.hora_inicio) {
        return '';
    }

    if (turnosDoctorSeleccionado.value.length === 0) {
        return 'El doctor seleccionado no tiene turnos asignados.';
    }

    const tieneTurnoEseDia = turnosDoctorSeleccionado.value.some((turno) => {
        return normalizarTexto(turno.pivot?.dia_semana ?? '') === normalizarTexto(diaSemanaSeleccionado.value ?? '');
    });

    if (!tieneTurnoEseDia) {
        return `El doctor no atiende el día ${diaSemanaSeleccionado.value}.`;
    }

    if (!turnoValidoSeleccionado.value) {
        return 'La hora seleccionada no está dentro del horario de atención del doctor.';
    }

    return '';
});

const formularioTieneErrorTurno = computed(() => {
    return errorTurnoDoctor.value !== '';
});

const submit = () => {
    if (formularioTieneErrorTurno.value) {
        alert(errorTurnoDoctor.value);
        return;
    }

    if (editMode.value && props.cita?.id_cita) {
        form.put(route('citas.update', props.cita.id_cita), {
            preserveScroll: true,
            onSuccess: () => {
                resetForm();
                emit('saved');
            },
        });
        return;
    }

    form.post(route('citas.store'), {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
            emit('saved');
        },
    });
};
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
            {{ editMode ? 'Editar cita' : 'Registrar cita' }}
        </h2>

        <p class="mt-1 text-[length:var(--font-sm)] text-[var(--muted)]">
            Selecciona el paciente, doctor, fecha y hora de inicio. La hora final se calcula automáticamente en Laravel.
        </p>

        <div v-if="Object.keys(form.errors).length > 0"
            class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-[length:var(--font-xs)] font-bold text-red-500">
            <p class="mb-1 text-[length:var(--font-sm)] font-black">
                Por favor corrige los siguientes errores:
            </p>

            <ul class="list-disc space-y-1 pl-4">
                <li v-for="(error, field) in form.errors" :key="field">
                    {{ field }}: {{ error }}
                </li>
            </ul>
        </div>

        <form class="mt-5 grid gap-4 md:grid-cols-2" @submit.prevent="submit">
            <div>
                <label for="paciente_ci" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Paciente
                </label>

                <select id="paciente_ci" v-model="form.paciente_ci" :disabled="soloLecturaPaciente || esPaciente"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4 disabled:cursor-not-allowed disabled:opacity-70"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                    <option value="">Seleccionar paciente</option>
                    <option v-for="paciente in pacientes" :key="paciente.codigo_paciente" :value="paciente.codigo_paciente">
                        {{ paciente.persona?.nombre ?? '' }} {{ paciente.persona?.apellido ?? '' }} - {{ paciente.codigo_paciente }}
                    </option>
                </select>

                <p v-if="form.errors.paciente_ci" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.paciente_ci }}
                </p>
            </div>

            <div>
                <label for="doctor_ci" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Doctor
                </label>

                <select id="doctor_ci" v-model="form.doctor_ci" :disabled="soloLecturaPaciente || esDoctor"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4 disabled:cursor-not-allowed disabled:opacity-70"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                    <option value="">Seleccionar doctor</option>
                    <option v-for="doctor in doctores" :key="doctor.codigo_doctor" :value="doctor.codigo_doctor">
                        {{ doctor.persona?.nombre ?? '' }} {{ doctor.persona?.apellido ?? '' }}
                    </option>
                </select>

                <div v-if="form.doctor_ci" class="mt-3 rounded-2xl border bg-[var(--section-soft)] p-4"
                    style="border-color: var(--border)">
                    <p class="text-[length:var(--font-xs)] font-black uppercase tracking-wider text-[var(--muted)]">
                        Turnos del doctor
                    </p>

                    <div v-if="turnosDoctorSeleccionado.length" class="mt-3 flex flex-wrap gap-2">
                        <span v-for="turno in turnosDoctorSeleccionado" :key="`${turno.id}-${turno.pivot?.dia_semana}`"
                            class="rounded-xl bg-[var(--primary-soft)] px-3 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)]">
                            {{ turno.pivot?.dia_semana }}:
                            {{ turno.hora_inicio?.substring(0, 5) }}
                            -
                            {{ turno.hora_fin?.substring(0, 5) }}
                        </span>
                    </div>

                    <p v-else class="mt-2 text-[length:var(--font-xs)] font-bold text-red-500">
                        Este doctor no tiene turnos asignados.
                    </p>
                </div>

                <p v-if="form.errors.doctor_ci" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.doctor_ci }}
                </p>
            </div>

            <div>
                <label for="fecha_cita" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Fecha de la cita
                </label>

                <input id="fecha_cita" v-model="form.fecha_cita" type="date" :disabled="soloLecturaPaciente"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.fecha_cita" class="mt-2 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                    Día seleccionado: {{ diaSemanaSeleccionado }}
                </p>

                <p v-if="form.errors.fecha_cita" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.fecha_cita }}
                </p>
            </div>

            <div>
                <label for="hora_inicio" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Hora de inicio
                </label>

                <input id="hora_inicio" v-model="form.hora_inicio" type="time" :disabled="soloLecturaPaciente"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p class="mt-2 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                    La hora final se calculará automáticamente sumando una hora.
                </p>

                <p v-if="errorTurnoDoctor" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ errorTurnoDoctor }}
                </p>

                <p v-else-if="turnoValidoSeleccionado" class="mt-2 text-[length:var(--font-sm)] font-bold text-green-600">
                    Horario válido dentro del turno del doctor.
                </p>

                <p v-if="form.errors.hora_inicio" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.hora_inicio }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label for="motivo" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Motivo
                </label>

                <input id="motivo" v-model="form.motivo" type="text" :disabled="soloLecturaPaciente"
                    placeholder="Ej: Consulta general, control, limpieza dental..."
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.motivo" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.motivo }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label for="observacion" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Observación
                </label>

                <textarea id="observacion" v-model="form.observacion" rows="3"
                    placeholder="Notas adicionales sobre la cita..."
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"></textarea>

                <p v-if="form.errors.observacion" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.observacion }}
                </p>
            </div>

            <div v-if="secretarias?.length && !esPaciente">
                <label for="secretaria_ci" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Secretaria
                </label>

                <select id="secretaria_ci" v-model="form.secretaria_ci" :disabled="soloLecturaPaciente"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                    <option value="">Sin secretaria asignada</option>
                    <option v-for="secretaria in secretarias" :key="secretaria.codigo_secretaria" :value="secretaria.codigo_secretaria">
                        {{ secretaria.persona?.nombre ?? '' }} {{ secretaria.persona?.apellido ?? '' }}
                    </option>
                </select>

                <p v-if="form.errors.secretaria_ci" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.secretaria_ci }}
                </p>
            </div>

            <div v-if="editMode">
                <label for="estado_cita_id" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Cambiar estado
                </label>

                <select id="estado_cita_id" v-model="form.estado_cita_id"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                    <option value="">Mantener estado actual</option>
                    <option v-for="estado in estadosParaFormulario" :key="estado.id" :value="estado.id">
                        {{ estado.nombre }}
                    </option>
                </select>

                <p v-if="form.errors.estado_cita_id" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.estado_cita_id }}
                </p>
            </div>

            <div class="mt-4 flex justify-end gap-3 md:col-span-2">
                <button type="button" @click="emit('cancel')"
                    class="rounded-2xl border bg-[var(--card)] px-8 py-4 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)]"
                    style="border-color: var(--border)">
                    Cancelar
                </button>

                <button type="submit" :disabled="form.processing || formularioTieneErrorTurno"
                    class="rounded-2xl bg-[var(--text)] px-8 py-4 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60">
                    {{ form.processing ? 'Guardando...' : editMode ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>
