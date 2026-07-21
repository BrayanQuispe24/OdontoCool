<script setup lang="ts">
import { computed } from 'vue';
import type { Cita } from '@/types/Cita';

const props = defineProps<{
    cita: Cita;
    view: 'day' | 'week' | 'month';
}>();

const emit = defineEmits<{
    click: [cita: Cita];
    dblclick: [cita: Cita];
    'drag-start': [event: DragEvent, cita: Cita];
    'resize-start': [event: MouseEvent, cita: Cita];
}>();

const getPacienteNombre = computed(() => {
    if (!props.cita.paciente) return 'Sin paciente';
    return `${props.cita.paciente.persona?.nombre ?? ''} ${props.cita.paciente.persona?.apellido ?? ''}`.trim();
});

const getDoctorNombre = computed(() => {
    if (!props.cita.doctor) return 'Sin doctor';
    return `${props.cita.doctor.persona?.nombre ?? ''} ${props.cita.doctor.persona?.apellido ?? ''}`.trim();
});

const getEstadoNombre = computed(() => {
    return props.cita.ultimo_estado_asignado?.estado_cita?.nombre ?? 'Sin estado';
});

const statusColorClasses = computed(() => {
    const estado = getEstadoNombre.value.toLowerCase();
    
    if (estado === 'confirmada' || estado === 'confirmado') {
        return 'bg-emerald-50 text-emerald-800 border-emerald-250 dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-900/50 hover:bg-emerald-100/80';
    } else if (estado === 'pendiente') {
        return 'bg-amber-50 text-amber-800 border-amber-250 dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-900/50 hover:bg-amber-100/80';
    } else if (estado === 'cancelada' || estado === 'cancelado') {
        return 'bg-rose-50 text-rose-800 border-rose-250 dark:bg-rose-950/30 dark:text-rose-300 dark:border-rose-900/50 hover:bg-rose-100/80';
    } else if (estado === 'finalizada' || estado === 'finalizado') {
        return 'bg-sky-50 text-sky-800 border-sky-250 dark:bg-sky-950/30 dark:text-sky-300 dark:border-sky-900/50 hover:bg-sky-100/80';
    } else {
        // En espera or others
        return 'bg-slate-100 text-slate-800 border-slate-250 dark:bg-slate-800/40 dark:text-slate-300 dark:border-slate-800 hover:bg-slate-150';
    }
});

const formattedTimeRange = computed(() => {
    const start = props.cita.hora_inicio?.substring(0, 5) ?? '';
    const end = props.cita.hora_fin?.substring(0, 5) ?? '';
    return `${start} - ${end}`;
});

const handleDragStart = (e: DragEvent) => {
    emit('drag-start', e, props.cita);
};

const handleResizeMouseDown = (e: MouseEvent) => {
    emit('resize-start', e, props.cita);
};

// Calculate duration in minutes for tooltip
const durationMinutes = computed(() => {
    if (!props.cita.hora_inicio || !props.cita.hora_fin) return 60;
    const [hStart, mStart] = props.cita.hora_inicio.split(':').map(Number);
    const [hEnd, mEnd] = props.cita.hora_fin.split(':').map(Number);
    const diff = (hEnd * 60 + mEnd) - (hStart * 60 + mStart);
    return diff > 0 ? diff : 60;
});
</script>

<template>
    <div draggable="true"
        @dragstart="handleDragStart"
        @click.stop="emit('click', cita)"
        @dblclick.stop="emit('dblclick', cita)"
        class="group relative flex flex-col justify-between overflow-hidden rounded-lg border p-2 shadow-[0_2px_4px_rgba(0,0,0,0.01)] transition-all cursor-grab active:cursor-grabbing select-none h-full text-left"
        :class="statusColorClasses">
        
        <!-- Tooltip on hover -->
        <div class="pointer-events-none absolute hidden group-hover:block z-50 bg-slate-900 text-white rounded-xl p-3 shadow-xl w-60 border border-slate-800 -top-2 left-full ml-2 dark:bg-slate-950">
            <p class="text-xs font-black text-slate-200">Detalles de la Cita</p>
            <div class="mt-2 space-y-1.5 text-[10px] text-slate-350">
                <p><span class="font-bold text-slate-100">Paciente:</span> {{ getPacienteNombre }}</p>
                <p><span class="font-bold text-slate-100">Doctor:</span> Dr. {{ getDoctorNombre }}</p>
                <p><span class="font-bold text-slate-100">Hora:</span> {{ formattedTimeRange }}</p>
                <p><span class="font-bold text-slate-100">Duración:</span> {{ durationMinutes }} minutos</p>
                <p><span class="font-bold text-slate-100">Motivo:</span> {{ cita.motivo || 'Sin motivo registrado' }}</p>
                <p><span class="font-bold text-slate-100">Estado:</span> <span class="capitalize">{{ getEstadoNombre }}</span></p>
            </div>
        </div>

        <!-- Event Body -->
        <div class="min-w-0 flex-1">
            <p class="text-[10px] font-black uppercase tracking-wide opacity-80">
                {{ formattedTimeRange }}
            </p>
            
            <p class="mt-0.5 truncate text-xs font-black leading-tight">
                {{ getPacienteNombre }}
            </p>

            <p v-if="view !== 'month'" class="mt-1 truncate text-[10px] opacity-75 font-semibold">
                Dr. {{ getDoctorNombre }}
            </p>
        </div>

        <div v-if="view !== 'month'" class="flex items-center justify-between mt-1 text-[9px] font-bold tracking-tight opacity-75">
            <span class="truncate uppercase">{{ getEstadoNombre }}</span>
        </div>

        <!-- Resize Handle (only visible on Week and Day views at the bottom edge) -->
        <div v-if="view !== 'month'"
            @mousedown.stop="handleResizeMouseDown"
            class="absolute bottom-0 inset-x-0 h-1.5 cursor-ns-resize hover:bg-slate-400/30 transition-colors z-20"
            title="Arrastrar para cambiar duración">
        </div>
    </div>
</template>
