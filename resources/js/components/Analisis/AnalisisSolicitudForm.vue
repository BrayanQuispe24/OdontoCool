<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { SolicitudAnalisis } from '@/types/SolicitudAnalisi';
import type { Analisis } from '@/types/Analisis';
import type { Tratamiento } from '@/types/Tratamiento';
import type { Doctor } from '@/types/doctor';

const props = defineProps<{
    solicitud: SolicitudAnalisis | null;
    analisisActivos: Analisis[];
    tratamientos: Tratamiento[];
    doctores: Doctor[];
    loggedInDoctorCi: string;
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

const editMode = computed(() => props.solicitud !== null);

const solicitudForm = useForm({
    fecha_solicitud: new Date().toISOString().split('T')[0],
    motivo: '',
    estado: 'PENDIENTE',
    analisis_id: '',
    tratamiento_id: '',
    doctor_ci: '',
});

const resetSolicitudForm = () => {
    solicitudForm.reset();
    solicitudForm.clearErrors();
};

const cargarSolicitud = (solicitud: SolicitudAnalisis | null) => {
    resetSolicitudForm();

    if (solicitud) {
        solicitudForm.fecha_solicitud = solicitud.fecha_solicitud;
        solicitudForm.motivo = solicitud.motivo || '';
        solicitudForm.estado = solicitud.estado;
        solicitudForm.analisis_id = solicitud.analisis?.id ? String(solicitud.analisis.id) : '';
        solicitudForm.tratamiento_id = solicitud.tratamiento?.id ? String(solicitud.tratamiento.id) : '';
        solicitudForm.doctor_ci = solicitud.doctor_ci ? String(solicitud.doctor_ci) : '';
    } else {
        solicitudForm.fecha_solicitud = new Date().toISOString().split('T')[0];
        solicitudForm.motivo = '';
        solicitudForm.estado = 'PENDIENTE';

        if (props.analisisActivos.length > 0) {
            solicitudForm.analisis_id = String(props.analisisActivos[0].id);
        }
        if (props.tratamientos.length > 0) {
            solicitudForm.tratamiento_id = String(props.tratamientos[0].id);
        }
        solicitudForm.doctor_ci = props.loggedInDoctorCi;
    }
};

watch(
    () => props.solicitud,
    cargarSolicitud,
    {
        immediate: true,
    },
);

const submit = () => {
    if (props.solicitud) {
        solicitudForm.put(
            route('solicitud_analisis.update', props.solicitud.id),
            {
                preserveScroll: true,
                onSuccess: () => {
                    resetSolicitudForm();
                    emit('saved');
                },
            },
        );
        return;
    }

    solicitudForm.post(route('solicitud_analisis.store'), {
        preserveScroll: true,
        onSuccess: () => {
            resetSolicitudForm();
            emit('saved');
        },
    });
};
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
            {{ editMode ? 'Editar Solicitud de Análisis' : 'Emitir Solicitud de Análisis' }}
        </h2>

        <form class="mt-5 space-y-4" @submit.prevent="submit">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Fecha de Solicitud
                    </label>

                    <input v-model="solicitudForm.fecha_solicitud" type="date"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                    <p v-if="solicitudForm.errors.fecha_solicitud"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ solicitudForm.errors.fecha_solicitud }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Seleccionar Análisis
                    </label>

                    <select v-model="solicitudForm.analisis_id" required
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                        <option v-for="item in analisisActivos" :key="item.id" :value="String(item.id)">
                            {{ item.nombre }}
                        </option>
                    </select>

                    <p v-if="solicitudForm.errors.analisis_id"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ solicitudForm.errors.analisis_id }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Asociar al Tratamiento
                    </label>

                    <select v-model="solicitudForm.tratamiento_id" required
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                        <option v-for="t in tratamientos" :key="t.id" :value="String(t.id)">
                            #{{ t.id }} - {{ t.objetivo_tratamiento }}
                        </option>
                    </select>

                    <p v-if="solicitudForm.errors.tratamiento_id"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ solicitudForm.errors.tratamiento_id }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Doctor Solicitante
                    </label>

                    <select v-model="solicitudForm.doctor_ci" required
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                        <option value="">Seleccione un Doctor</option>
                        <option v-for="doc in doctores" :key="doc.codigo_doctor" :value="doc.codigo_doctor">
                            {{ doc.persona?.nombre }} {{ doc.persona?.apellido }}
                        </option>
                    </select>

                    <p v-if="solicitudForm.errors.doctor_ci"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ solicitudForm.errors.doctor_ci }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Estado de la Solicitud
                    </label>

                    <select v-model="solicitudForm.estado"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                        <option value="PENDIENTE">PENDIENTE</option>
                        <option value="COMPLETADO">COMPLETADO</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Motivo / Indicaciones
                    </label>

                    <textarea v-model="solicitudForm.motivo" rows="3"
                        placeholder="Indica el motivo clínico por el que se solicita esta prueba..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t pt-4" style="border-color: var(--border)">
                <button type="button" @click="emit('cancel')"
                    class="rounded-2xl border bg-[var(--card)] px-6 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)]"
                    style="border-color: var(--border)">
                    Cancelar
                </button>

                <button type="submit" :disabled="solicitudForm.processing"
                    class="rounded-2xl bg-[var(--text)] px-6 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:opacity-60">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</template>
