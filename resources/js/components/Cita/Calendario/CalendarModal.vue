<script setup lang="ts">
import { computed } from 'vue';
import type { Cita } from '@/types/Cita';

const props = defineProps<{
    cita: Cita | null;
    show: boolean;
}>();

const emit = defineEmits<{
    close: [];
    edit: [cita: Cita];
    delete: [id: number];
}>();

const getPacienteNombre = computed(() => {
    if (!props.cita?.paciente) return 'Sin paciente';
    return `${props.cita.paciente.persona?.nombre ?? ''} ${props.cita.paciente.persona?.apellido ?? ''}`.trim();
});

const getDoctorNombre = computed(() => {
    if (!props.cita?.doctor) return 'Sin doctor';
    return `${props.cita.doctor.persona?.nombre ?? ''} ${props.cita.doctor.persona?.apellido ?? ''}`.trim();
});

const getSecretariaNombre = computed(() => {
    if (!props.cita?.secretaria) return 'No registrada';
    return `${props.cita.secretaria.persona?.nombre ?? ''} ${props.cita.secretaria.persona?.apellido ?? ''}`.trim();
});

const getEstadoNombre = computed(() => {
    return props.cita?.ultimo_estado_asignado?.estado_cita?.nombre ?? 'Sin estado';
});

const statusColorClasses = computed(() => {
    const estado = getEstadoNombre.value.toLowerCase();
    
    if (estado === 'confirmada' || estado === 'confirmado') {
        return 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20';
    } else if (estado === 'pendiente') {
        return 'bg-amber-500/10 text-amber-600 border-amber-500/20';
    } else if (estado === 'cancelada' || estado === 'cancelado') {
        return 'bg-rose-500/10 text-rose-600 border-rose-500/20';
    } else if (estado === 'finalizada' || estado === 'finalizado') {
        return 'bg-sky-500/10 text-sky-600 border-sky-500/20';
    } else {
        return 'bg-[var(--section-soft)] text-[var(--muted)] border-[var(--border)]';
    }
});

const formattedTimeRange = computed(() => {
    const start = props.cita?.hora_inicio?.substring(0, 5) ?? '';
    const end = props.cita?.hora_fin?.substring(0, 5) ?? '';
    return `${start} - ${end}`;
});
</script>

<template>
    <div v-if="show && cita" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm transition-opacity" @click="emit('close')"></div>

        <!-- Modal Box -->
        <div class="relative z-10 w-full max-w-lg transform rounded-2xl border p-6 shadow-2xl transition-all border-[var(--border)] bg-[var(--card)]">
            <!-- Close button -->
            <button type="button" @click="emit('close')"
                class="absolute right-4 top-4 rounded-lg p-1.5 text-[var(--muted)] hover:bg-[var(--section-soft)] hover:text-[var(--title)]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Header -->
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-xl font-black text-[var(--primary)]">
                    📅
                </div>
                <div>
                    <h3 class="text-base font-bold text-[var(--title)]">
                        Detalle de Cita Médica
                    </h3>
                    <p class="text-xs text-[var(--muted)] mt-0.5">
                        ID de cita: #{{ cita.id_cita }}
                    </p>
                </div>
            </div>

            <!-- Details list -->
            <div class="mt-6 space-y-4 border-y py-4 border-[var(--border)]">
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs font-bold text-[var(--muted)] uppercase tracking-wider">Paciente</span>
                    <span class="col-span-2 text-xs font-black text-[var(--title)]">
                        {{ getPacienteNombre }} (CI: {{ cita.paciente_ci }})
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs font-bold text-[var(--muted)] uppercase tracking-wider">Doctor</span>
                    <span class="col-span-2 text-xs font-bold text-[var(--title)]">
                        Dr. {{ getDoctorNombre }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs font-bold text-[var(--muted)] uppercase tracking-wider">Fecha</span>
                    <span class="col-span-2 text-xs font-bold text-[var(--title)]">
                        {{ cita.fecha_cita }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs font-bold text-[var(--muted)] uppercase tracking-wider">Horario</span>
                    <span class="col-span-2 text-xs font-black text-[var(--primary)]">
                        {{ formattedTimeRange }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs font-bold text-[var(--muted)] uppercase tracking-wider">Motivo</span>
                    <span class="col-span-2 text-xs text-[var(--text)]">
                        {{ cita.motivo || 'Sin motivo registrado' }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs font-bold text-[var(--muted)] uppercase tracking-wider">Observación</span>
                    <span class="col-span-2 text-xs text-[var(--text)] italic opacity-95">
                        {{ cita.observacion || 'Sin observaciones' }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <span class="text-xs font-bold text-[var(--muted)] uppercase tracking-wider">Secretaria</span>
                    <span class="col-span-2 text-xs text-[var(--text)] opacity-90">
                        {{ getSecretariaNombre }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 items-center">
                    <span class="text-xs font-bold text-[var(--muted)] uppercase tracking-wider">Estado</span>
                    <span class="col-span-2">
                        <span class="inline-flex rounded-full border px-3 py-1 text-xs font-black capitalize"
                            :class="statusColorClasses">
                            {{ getEstadoNombre }}
                        </span>
                    </span>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="emit('delete', cita.id_cita)"
                    class="rounded-xl border px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition border-rose-200 bg-[var(--card)] dark:hover:bg-rose-950/20 dark:border-rose-900">
                    Eliminar
                </button>

                <button type="button" @click="emit('edit', cita)"
                    class="rounded-xl px-4 py-2.5 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-850 dark:bg-slate-100 dark:text-slate-950 dark:hover:bg-white transition">
                    Editar
                </button>
            </div>
        </div>
    </div>
</template>
