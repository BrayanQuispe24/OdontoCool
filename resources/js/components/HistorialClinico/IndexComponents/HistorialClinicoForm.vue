<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { HistorialClinico } from '@/types/HistorialClinico';
import type { Paciente } from '@/types/Paciente';

const props = defineProps<{
    historial: HistorialClinico | null;
    editMode: boolean;
    pacientes: Paciente[];
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

const form = useForm({
    motivo_apertura: '',
    fecha_apertura: new Date().toISOString().split('T')[0],
    alergias: '',
    antecedentes_medicos: '',
    enfermedades_base: '',
    observaciones_generales: '',
    paciente_ci: '',
    estado: 'ACTIVO',

    registrar_antecedentes: false,
    antecedentes_odontologicos: {
        id_antecedente: null as number | null,
        fecha_registro: new Date().toISOString().split('T')[0],
        observacion_general: '',
        detalles: [] as Array<{
            id_detalle_antecedente?: number;
            nombre_tratamiento: string;
            descripcion: string;
            fecha_tratamiento: string;
            lugar_tratamiento: string;
            observacion: string;
        }>
    }
});

const cargarHistorial = (historial: HistorialClinico | null) => {
    form.clearErrors();

    if (historial) {
        form.motivo_apertura = historial.motivo_apertura;
        form.fecha_apertura = historial.fecha_apertura;
        form.alergias = historial.alergias || '';
        form.antecedentes_medicos = historial.antecedentes_medicos || '';
        form.enfermedades_base = historial.enfermedades_base || '';
        form.observaciones_generales = historial.observaciones_generales || '';
        form.paciente_ci = historial.paciente_ci;
        form.estado = historial.estado;

        if (historial.antecedentes_odontologicos) {
            form.registrar_antecedentes = true;
            form.antecedentes_odontologicos.id_antecedente = historial.antecedentes_odontologicos.id_antecedente;
            form.antecedentes_odontologicos.fecha_registro = historial.antecedentes_odontologicos.fecha_registro;
            form.antecedentes_odontologicos.observacion_general = historial.antecedentes_odontologicos.observacion_general || '';
            form.antecedentes_odontologicos.detalles = historial.antecedentes_odontologicos.detalle_antecedentes
                ? historial.antecedentes_odontologicos.detalle_antecedentes.map(d => ({
                    id_detalle_antecedente: d.id_detalle_antecedente,
                    nombre_tratamiento: d.nombre_tratamiento,
                    descripcion: d.descripcion || '',
                    fecha_tratamiento: d.fecha_tratamiento,
                    lugar_tratamiento: d.lugar_tratamiento,
                    observacion: d.observacion || ''
                }))
                : [];
        } else {
            form.registrar_antecedentes = false;
            form.antecedentes_odontologicos.id_antecedente = null;
            form.antecedentes_odontologicos.fecha_registro = new Date().toISOString().split('T')[0];
            form.antecedentes_odontologicos.observacion_general = '';
            form.antecedentes_odontologicos.detalles = [];
        }
    } else {
        form.motivo_apertura = '';
        form.fecha_apertura = new Date().toISOString().split('T')[0];
        form.alergias = '';
        form.antecedentes_medicos = '';
        form.enfermedades_base = '';
        form.observaciones_generales = '';
        form.paciente_ci = props.pacientes.length > 0 ? props.pacientes[0].codigo_paciente : '';
        form.estado = 'ACTIVO';
        form.registrar_antecedentes = false;
        form.antecedentes_odontologicos.id_antecedente = null;
        form.antecedentes_odontologicos.fecha_registro = new Date().toISOString().split('T')[0];
        form.antecedentes_odontologicos.observacion_general = '';
        form.antecedentes_odontologicos.detalles = [];
    }
};

watch(
    () => props.historial,
    cargarHistorial,
    { immediate: true }
);

// We also need to watch props.pacientes to pre-select first paciente if available
watch(
    () => props.pacientes,
    (pacientes) => {
        if (!props.historial && pacientes.length > 0 && !form.paciente_ci) {
            form.paciente_ci = pacientes[0].codigo_paciente;
        }
    },
    { immediate: true }
);

const addTratamientoLinea = () => {
    form.antecedentes_odontologicos.detalles.push({
        nombre_tratamiento: '',
        descripcion: '',
        fecha_tratamiento: new Date().toISOString().split('T')[0],
        lugar_tratamiento: '',
        observacion: '',
    });
};

const removeTratamientoLinea = (index: number) => {
    form.antecedentes_odontologicos.detalles.splice(index, 1);
};

const submitForm = () => {
    // prepare form payload
    const payload = form.data();
    if (!form.registrar_antecedentes) {
        payload.antecedentes_odontologicos = null as any;
    }

    if (props.editMode && props.historial?.codigo_historial) {
        form.transform(() => payload).put(route('historial_clinico.update', props.historial.codigo_historial), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('saved');
            },
        });
        return;
    }

    form.transform(() => payload).post(route('historial_clinico.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('saved');
        },
    });
};
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 border-[var(--border)]">
        <div class="mb-4 border-b pb-3 border-[var(--border)]">
            <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                {{ editMode ? 'Editar Historial Clínico' : 'Registrar Nuevo Historial Clínico' }}
            </h2>

            <p class="mt-1 text-[length:var(--font-xs)] text-[var(--muted)]">
                Ingresa los antecedentes médicos generales para la apertura de la ficha del paciente.
            </p>
        </div>

        <form @submit.prevent="submitForm" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Patient selection -->
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Paciente
                    </label>

                    <select v-model="form.paciente_ci" :disabled="editMode" required
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition disabled:opacity-70 focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                        style="--tw-ring-color: var(--primary-soft)">
                        <option v-for="paciente in pacientes" :key="paciente.codigo_paciente"
                            :value="paciente.codigo_paciente">
                            {{ paciente.persona?.nombre }} {{ paciente.persona?.apellido }} (CI: {{ paciente.codigo_paciente }})
                        </option>
                    </select>

                    <p v-if="form.errors.paciente_ci" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.paciente_ci }}
                    </p>
                </div>

                <!-- Fecha Apertura -->
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Fecha de Apertura
                    </label>

                    <input v-model="form.fecha_apertura" type="date" required
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                        style="--tw-ring-color: var(--primary-soft)" />

                    <p v-if="form.errors.fecha_apertura" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.fecha_apertura }}
                    </p>
                </div>

                <!-- Motivo Apertura -->
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Motivo de Apertura / Diagnóstico de ingreso
                    </label>

                    <input v-model="form.motivo_apertura" type="text" required
                        placeholder="Ej: Dolor dental agudo, consulta general preventiva..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                        style="--tw-ring-color: var(--primary-soft)" />

                    <p v-if="form.errors.motivo_apertura" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.motivo_apertura }}
                    </p>
                </div>

                <!-- Alergias -->
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Alergias médicas y alimentarias
                    </label>

                    <textarea v-model="form.alergias" rows="3"
                        placeholder="Ej: Penicilina, látex, mariscos, anestésicos locales..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                        style="--tw-ring-color: var(--primary-soft)"></textarea>

                    <p v-if="form.errors.alergias" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.alergias }}
                    </p>
                </div>

                <!-- Enfermedades Base -->
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Enfermedades de Base / Patologías crónicas
                    </label>

                    <textarea v-model="form.enfermedades_base" rows="3"
                        placeholder="Ej: Diabetes Tipo 2, hipertensión arterial, asma..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                        style="--tw-ring-color: var(--primary-soft)"></textarea>

                    <p v-if="form.errors.enfermedades_base" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.enfermedades_base }}
                    </p>
                </div>

                <!-- Antecedentes Medicos -->
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Otros antecedentes médicos generales
                    </label>

                    <textarea v-model="form.antecedentes_medicos" rows="3"
                        placeholder="Ej: Cirugías previas, prótesis cardíacas, trastornos hemorrágicos..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                        style="--tw-ring-color: var(--primary-soft)"></textarea>

                    <p v-if="form.errors.antecedentes_medicos" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.antecedentes_medicos }}
                    </p>
                </div>

                <!-- Observaciones Generales -->
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Observaciones Generales
                    </label>

                    <textarea v-model="form.observaciones_generales" rows="3"
                        placeholder="Notas adicionales sobre el comportamiento clínico del paciente..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                        style="--tw-ring-color: var(--primary-soft)"></textarea>

                    <p v-if="form.errors.observaciones_generales" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.observaciones_generales }}
                    </p>
                </div>

                <!-- Antecedentes Odontologicos Header -->
                <div class="mt-2 border-t pt-4 md:col-span-2 border-[var(--border)]">
                    <label class="flex cursor-pointer items-center gap-3">
                        <input v-model="form.registrar_antecedentes" type="checkbox"
                            class="h-5 w-5 rounded border-[var(--border)] text-[var(--primary)] focus:ring-[var(--primary)]" />

                        <span class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                            Registrar Antecedentes Odontológicos Previos (Historial de tratamientos en otras clínicas)
                        </span>
                    </label>
                </div>

                <!-- Antecedentes Odontologicos Fields -->
                <template v-if="form.registrar_antecedentes && form.antecedentes_odontologicos">
                    <div>
                        <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            Fecha de Registro
                        </label>

                        <input v-model="form.antecedentes_odontologicos.fecha_registro" type="date" required
                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                            style="--tw-ring-color: var(--primary-soft)" />
                    </div>

                    <div>
                        <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            Observación General de Antecedentes
                        </label>

                        <input v-model="form.antecedentes_odontologicos.observacion_general" type="text"
                            placeholder="Ej: El paciente refiere buena higiene y visitas regulares..."
                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                            style="--tw-ring-color: var(--primary-soft)" />
                    </div>

                    <!-- Treatment details rows -->
                    <div class="space-y-4 md:col-span-2">
                        <div class="flex items-center justify-between border-b pb-2 border-[var(--border)]">
                            <h4 class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                                Detalle de Tratamientos Históricos
                            </h4>

                            <button type="button" @click="addTratamientoLinea"
                                class="rounded-xl border bg-[var(--card)] px-4 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:bg-[var(--primary-soft)] border-[var(--primary)]">
                                + Agregar Tratamiento
                            </button>
                        </div>

                        <div v-if="form.antecedentes_odontologicos.detalles.length === 0"
                            class="py-3 text-center text-[length:var(--font-xs)] text-[var(--muted)]">
                            No has agregado tratamientos históricos aún.
                        </div>

                        <div v-else v-for="(det, index) in form.antecedentes_odontologicos.detalles" :key="index"
                            class="relative grid gap-3 rounded-2xl border bg-[var(--section-soft)] p-4 md:grid-cols-5 border-[var(--border)]">
                            <div>
                                <label class="mb-1 block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                                    Tratamiento
                                </label>

                                <input v-model="det.nombre_tratamiento" type="text" required placeholder="Ej: Ortodoncia"
                                    class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none placeholder:text-[var(--muted)] focus:ring-4 border-[var(--border)]"
                                    style="--tw-ring-color: var(--primary-soft)" />
                            </div>

                            <div>
                                <label class="mb-1 block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                                    Clínica / Lugar
                                </label>

                                <input v-model="det.lugar_tratamiento" type="text" required placeholder="Ej: Dental Center"
                                    class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none placeholder:text-[var(--muted)] focus:ring-4 border-[var(--border)]"
                                    style="--tw-ring-color: var(--primary-soft)" />
                            </div>

                            <div>
                                <label class="mb-1 block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                                    Fecha
                                </label>

                                <input v-model="det.fecha_tratamiento" type="date" required
                                    class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none focus:ring-4 border-[var(--border)]"
                                    style="--tw-ring-color: var(--primary-soft)" />
                            </div>

                            <div>
                                <label class="mb-1 block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                                    Descripción
                                </label>

                                <input v-model="det.descripcion" type="text" placeholder="Ej: Brackets metálicos"
                                    class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none placeholder:text-[var(--muted)] focus:ring-4 border-[var(--border)]"
                                    style="--tw-ring-color: var(--primary-soft)" />
                            </div>

                            <div class="flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="mb-1 block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                                        Observación
                                    </label>

                                    <input v-model="det.observacion" type="text" placeholder="Ej: Retenedores colocados"
                                        class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none placeholder:text-[var(--muted)] focus:ring-4 border-[var(--border)]"
                                        style="--tw-ring-color: var(--primary-soft)" />
                                </div>

                                <button type="button" @click="removeTratamientoLinea(index)"
                                    class="rounded-xl border border-red-200 dark:border-red-900/40 bg-[var(--card)] p-2.5 text-red-500 dark:text-red-400 transition hover:bg-red-50/50 dark:hover:bg-red-950/30"
                                    title="Eliminar línea">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Actions Form -->
            <div class="flex items-center justify-end gap-3 border-t pt-4 border-[var(--border)]">
                <button type="button" @click="emit('cancel')"
                    class="rounded-2xl border bg-[var(--card)] px-6 py-3.5 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)] border-[var(--border)]">
                    Cancelar
                </button>
                <button type="submit" :disabled="form.processing"
                    class="rounded-2xl bg-[var(--text)] px-8 py-3.5 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:opacity-60">
                    {{ form.processing ? 'Guardando...' : editMode ? 'Actualizar' : 'Guardar Ficha' }}
                </button>
            </div>
        </form>
    </div>
</template>
