<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Diagnostico } from '@/types/Diagnostico';
import type { Cita } from '@/types/Cita';
import type { Diente } from '@/types/Diente';

const props = defineProps<{
    diagnostico: Diagnostico | null;
    citasSelect: Cita[];
    dientes: Diente[];
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

const editMode = computed(() => props.diagnostico !== null);

const form = useForm({
    sintomas: '',
    tipo_diagnostico: '',
    gravedad: 'LEVE',
    observaciones: '',
    cita_id: '',
    detalles: [] as Array<{
        id?: number;
        diente_id: number;
        zona_bucal: string;
        observacion: string;
    }>
});

const cargarDiagnostico = (item: Diagnostico | null) => {
    form.clearErrors();
    
    if (item) {
        form.sintomas = item.sintomas;
        form.tipo_diagnostico = item.tipo_diagnostico;
        form.gravedad = item.gravedad;
        form.observaciones = item.observaciones || '';
        form.cita_id = item.cita_id.toString();
        form.detalles = item.detalle_diagnostico
            ? item.detalle_diagnostico.map((d) => ({
                  id: d.id,
                  diente_id: d.diente_id,
                  zona_bucal: d.zona_bucal,
                  observacion: d.observacion || ''
              }))
            : [];
    } else {
        form.sintomas = '';
        form.tipo_diagnostico = '';
        form.gravedad = 'LEVE';
        form.observaciones = '';
        form.cita_id = props.citasSelect.length > 0 ? props.citasSelect[0].id_cita.toString() : '';
        form.detalles = [];
    }
};

watch(
    () => props.diagnostico,
    (item) => {
        cargarDiagnostico(item);
    },
    { immediate: true }
);

// Watch for initial option in create mode if select changes
watch(
    () => props.citasSelect,
    (list) => {
        if (!editMode.value && list.length > 0 && !form.cita_id) {
            form.cita_id = list[0].id_cita.toString();
        }
    }
);

const addDetalleLinea = () => {
    const defaultDienteId = props.dientes.length > 0 ? props.dientes[0].id : 0;
    form.detalles.push({
        diente_id: defaultDienteId,
        zona_bucal: 'Corona',
        observacion: ''
    });
};

const removeDetalleLinea = (index: number) => {
    form.detalles.splice(index, 1);
};

const submitForm = () => {
    if (editMode.value && props.diagnostico) {
        form.put(route('diagnostico.update', props.diagnostico.id), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('saved');
            }
        });
        return;
    }

    form.post(route('diagnostico.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('saved');
        }
    });
};

const handleCancel = () => {
    form.reset();
    form.clearErrors();
    emit('cancel');
};
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <div class="mb-4 border-b pb-3" style="border-color: var(--border)">
            <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                {{ editMode ? 'Editar Diagnóstico Clínico' : 'Registrar Nuevo Diagnóstico' }}
            </h2>

            <p class="mt-1 text-[length:var(--font-xs)] text-[var(--muted)]">
                Completa la información diagnóstica general e indica los dientes intervenidos.
            </p>
        </div>

        <form @submit.prevent="submitForm" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Select Appointment -->
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Cita / Paciente
                    </label>

                    <select v-model="form.cita_id" :disabled="editMode" required
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition disabled:opacity-70 focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                        <option v-for="cita in citasSelect" :key="cita.id_cita" :value="cita.id_cita">
                            Ficha #{{ cita.id_cita }} - {{ cita.paciente?.persona?.nombre }} {{ cita.paciente?.persona?.apellido }} (Fecha: {{ cita.fecha_cita }})
                        </option>
                    </select>

                    <p v-if="form.errors.cita_id" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.cita_id }}
                    </p>
                </div>

                <!-- Severity -->
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Gravedad
                    </label>

                    <select v-model="form.gravedad" required
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                        <option value="LEVE">Leve</option>
                        <option value="MODERADA">Moderada</option>
                        <option value="SEVERA">Severa</option>
                    </select>

                    <p v-if="form.errors.gravedad" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.gravedad }}
                    </p>
                </div>

                <!-- Sintomas -->
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Síntomas Reportados
                    </label>

                    <input v-model="form.sintomas" type="text" required
                        placeholder="Ej: Dolor punzante al masticar, sensibilidad al frío..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                    <p v-if="form.errors.sintomas" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.sintomas }}
                    </p>
                </div>

                <!-- Tipo Diagnostico -->
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Tipo de Diagnóstico / Enfermedad
                    </label>

                    <input v-model="form.tipo_diagnostico" type="text" required
                        placeholder="Ej: Caries penetrante, Pulpitis reversible..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                    <p v-if="form.errors.tipo_diagnostico" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.tipo_diagnostico }}
                    </p>
                </div>

                <!-- Observaciones -->
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Observaciones y Recomendaciones Clínicas
                    </label>

                    <textarea v-model="form.observaciones" rows="3"
                        placeholder="Notas internas para el plan de tratamiento o prescripciones necesarias..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"></textarea>

                    <p v-if="form.errors.observaciones" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.observaciones }}
                    </p>
                </div>

                <!-- Teeth Details Nested Section -->
                <div class="mt-2 space-y-4 border-t pt-4 md:col-span-2" style="border-color: var(--border)">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h4 class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                                Inspección por Diente (Hallazgos Odontológicos)
                            </h4>

                            <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                Asocia anomalías encontradas a dientes específicos.
                            </p>
                        </div>

                        <button type="button" @click="addDetalleLinea"
                            class="rounded-xl border bg-[var(--card)] px-4 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:bg-[var(--primary-soft)]"
                            style="border-color: var(--primary)">
                            + Agregar Diente
                        </button>
                    </div>

                    <div v-if="form.detalles.length === 0"
                        class="py-3 text-center text-[length:var(--font-xs)] text-[var(--muted)]">
                        No has agregado detalles por diente en este diagnóstico.
                    </div>

                    <div v-else v-for="(det, index) in form.detalles" :key="index"
                        class="relative grid gap-3 rounded-2xl border bg-[var(--section-soft)] p-4 md:grid-cols-4"
                        style="border-color: var(--border)">
                        <!-- Tooth select -->
                        <div>
                            <label class="mb-1 block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                                Diente
                            </label>

                            <select v-model="det.diente_id" required
                                class="w-full rounded-xl border bg-[var(--card)] px-3 py-2 text-[length:var(--font-xs)] text-[var(--title)] outline-none"
                                style="border-color: var(--border)">
                                <option v-for="d in dientes" :key="d.id" :value="d.id">
                                    Diente #{{ d.numero }} - {{ d.nombre }}
                                </option>
                            </select>
                        </div>

                        <!-- Zona bucal -->
                        <div>
                            <label class="mb-1 block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                                Zona Bucal
                            </label>

                            <input v-model="det.zona_bucal" type="text" required placeholder="Ej: Oclusal, Corona, Encía..."
                                class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none placeholder:text-[var(--muted)]"
                                style="border-color: var(--border)" />
                        </div>

                        <!-- Observacion -->
                        <div class="flex items-end gap-2 md:col-span-2">
                            <div class="flex-1">
                                <label class="mb-1 block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                                    Hallazgo / Observación
                                </label>

                                <input v-model="det.observacion" type="text"
                                    placeholder="Ej: Presencia de placa bacteriana, fractura leve..."
                                    class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none placeholder:text-[var(--muted)]"
                                    style="border-color: var(--border)" />
                            </div>

                            <button type="button" @click="removeDetalleLinea(index)"
                                class="rounded-xl border border-red-200 bg-[var(--card)] p-2.5 text-red-500 transition hover:bg-red-50"
                                title="Eliminar diente">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 border-t pt-4" style="border-color: var(--border)">
                <button type="button" @click="handleCancel"
                    class="rounded-2xl border bg-[var(--card)] px-6 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)]"
                    style="border-color: var(--border)">
                    Cancelar
                </button>

                <button type="submit" :disabled="form.processing"
                    class="rounded-2xl bg-[var(--text)] px-6 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:opacity-60">
                    {{ form.processing ? 'Guardando...' : 'Guardar Diagnóstico' }}
                </button>
            </div>
        </form>
    </div>
</template>
