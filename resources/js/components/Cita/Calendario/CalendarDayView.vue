<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import type { Cita } from '@/types/Cita';
import CalendarEvent from './CalendarEvent.vue';

const props = defineProps<{
    currentDate: Date;
    citas: Cita[];
}>();

const emit = defineEmits<{
    'click-event': [cita: Cita];
    'dblclick-event': [cita: Cita];
    'drag-start': [event: DragEvent, cita: Cita];
    'drop-event': [cita: Cita, targetDate: Date, targetHour: string];
    'resize-event': [cita: Cita, newDurationMinutes: number];
    'click-empty-slot': [date: Date, hourStr: string];
}>();

const hourHeight = 68; // matched to Day View height
const workStartHour = 8;
const workEndHour = 18;
const hours = Array.from({ length: workEndHour - workStartHour + 1 }, (_, i) => workStartHour + i);

const weekDaysNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

// Current time logic for indicator
const now = ref(new Date());
let timeInterval: any = null;

onMounted(() => {
    timeInterval = setInterval(() => {
        now.value = new Date();
    }, 60000);
});

onUnmounted(() => {
    if (timeInterval) clearInterval(timeInterval);
});

const isSameDay = (d1: Date, d2: Date) => {
    return (
        d1.getFullYear() === d2.getFullYear() &&
        d1.getMonth() === d2.getMonth() &&
        d1.getDate() === d2.getDate()
    );
};

const getCitasForDay = computed(() => {
    return props.citas.filter((cita) => {
        if (!cita.fecha_cita) return false;
        const [year, month, day] = cita.fecha_cita.split('-').map(Number);
        const citaDate = new Date(year, month - 1, day);
        return isSameDay(citaDate, props.currentDate);
    });
});

const timeToDecimal = (timeStr?: string) => {
    if (!timeStr) return 0;
    const [h, m] = timeStr.split(':').map(Number);
    return h + m / 60;
};

const getEventStyle = (cita: Cita) => {
    const start = timeToDecimal(cita.hora_inicio);
    const end = timeToDecimal(cita.hora_fin);
    
    const startClamped = Math.max(workStartHour, Math.min(workEndHour, start));
    const endClamped = Math.max(workStartHour, Math.min(workEndHour, end));
    
    const top = (startClamped - workStartHour) * hourHeight;
    const height = (endClamped - startClamped) * hourHeight;
    
    return {
        top: `${top}px`,
        height: `${height > 25 ? height : 25}px`
    };
};

const handleDragStart = (e: DragEvent, cita: Cita) => {
    emit('drag-start', e, cita);
};

const handleDrop = (e: DragEvent, hour: number) => {
    const citaData = e.dataTransfer?.getData('application/json');
    if (!citaData) return;
    try {
        const cita = JSON.parse(citaData) as Cita;
        const hourStr = `${String(hour).padStart(2, '0')}:00`;
        emit('drop-event', cita, props.currentDate, hourStr);
    } catch (err) {
        console.error('Error Day View drop', err);
    }
};

// Resize logic
const resizingCita = ref<Cita | null>(null);
const resizeStartY = ref(0);
const resizeStartDuration = ref(0);

const handleResizeStart = (e: MouseEvent, cita: Cita) => {
    resizingCita.value = cita;
    resizeStartY.value = e.clientY;
    
    const start = timeToDecimal(cita.hora_inicio);
    const end = timeToDecimal(cita.hora_fin);
    resizeStartDuration.value = Math.round((end - start) * 60);

    window.addEventListener('mousemove', handleResizeMouseMove);
    window.addEventListener('mouseup', handleResizeMouseUp);
};

const handleResizeMouseMove = (e: MouseEvent) => {
    if (!resizingCita.value) return;
    const deltaY = e.clientY - resizeStartY.value;
    const deltaMinutes = Math.round(deltaY / (hourHeight / 60));
    const step = 15;
    const newDuration = Math.max(15, Math.round((resizeStartDuration.value + deltaMinutes) / step) * step);
    
    resizingCita.value.hora_fin = addMinutesToTime(resizingCita.value.hora_inicio!, newDuration);
};

const handleResizeMouseUp = () => {
    if (resizingCita.value) {
        const start = timeToDecimal(resizingCita.value.hora_inicio);
        const end = timeToDecimal(resizingCita.value.hora_fin);
        const newDuration = Math.round((end - start) * 60);
        emit('resize-event', resizingCita.value, newDuration);
    }
    resizingCita.value = null;
    window.removeEventListener('mousemove', handleResizeMouseMove);
    window.removeEventListener('mouseup', handleResizeMouseUp);
};

const addMinutesToTime = (timeStr: string, minutes: number) => {
    const [h, m] = timeStr.split(':').map(Number);
    const totalMinutes = h * 60 + m + minutes;
    const newH = Math.min(23, Math.floor(totalMinutes / 60));
    const newM = Math.min(59, totalMinutes % 60);
    return `${String(newH).padStart(2, '0')}:${String(newM).padStart(2, '0')}`;
};

const currentTimeLineTop = computed(() => {
    const currentHour = now.value.getHours() + now.value.getMinutes() / 60;
    if (currentHour >= workStartHour && currentHour <= workEndHour) {
        return (currentHour - workStartHour) * hourHeight;
    }
    return null;
});

const isToday = computed(() => {
    return isSameDay(props.currentDate, new Date());
});

const formattedDayName = computed(() => {
    return weekDaysNames[props.currentDate.getDay()];
});
</script>

<template>
    <div class="flex flex-col h-full bg-[var(--card)] rounded-2xl border shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden border-[var(--border)] transition-all duration-300">
        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto min-h-[480px]">
            <!-- Day Header (Sticky inside the scroll view to align columns perfectly) -->
            <div class="sticky top-0 z-40 grid grid-cols-[64px_1fr] border-b border-[var(--border)] bg-[var(--card)]">
                <!-- empty corner -->
                <div class="border-r border-[var(--border)] bg-[var(--section-soft)]/45"></div>
                
                <div class="py-4 pl-6 text-left bg-[var(--section-soft)]/45">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[var(--muted)]">
                        {{ formattedDayName }}
                    </p>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="text-3xl font-black"
                            :class="isToday ? 'text-[var(--primary)]' : 'text-[var(--title)]'">
                            {{ currentDate.getDate() }}
                        </span>
                        <span class="text-xs font-bold text-[var(--muted)] capitalize">
                            {{ currentDate.toLocaleString('es-ES', { month: 'long', year: 'numeric' }) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Hours grid -->
            <div class="grid grid-cols-[64px_1fr] relative" :style="{ height: `${(hours.length - 1) * hourHeight}px` }">
                
                <!-- Time Labels Column -->
                <div class="border-r bg-[var(--section-soft)]/10 text-right pr-3 select-none border-[var(--border)]">
                    <div v-for="(h, idx) in hours" :key="h"
                        class="text-[10px] font-black tracking-wider text-[var(--muted)] flex items-center justify-end"
                        :style="{ height: `${hourHeight}px`, transform: idx === 0 ? 'translateY(0)' : 'translateY(-50%)' }">
                        {{ String(h).padStart(2, '0') }}:00
                    </div>
                </div>

                <!-- Day Column Slot dropping area -->
                <div class="relative h-full select-none"
                    :class="isToday ? 'bg-[var(--primary-soft)]/5' : ''">
                    
                    <!-- Hour Dividers -->
                    <div v-for="h in hours.slice(0, -1)" :key="h"
                        @dragover.prevent
                        @drop="handleDrop($event, h)"
                        @click="emit('click-empty-slot', currentDate, `${String(h).padStart(2, '0')}:00`)"
                        class="border-b hover:bg-[var(--section-soft)]/30 cursor-pointer border-[var(--border)] transition-colors"
                        :style="{ height: `${hourHeight}px` }">
                    </div>

                    <!-- Current time line indicator -->
                    <div v-if="isToday && currentTimeLineTop !== null"
                        class="absolute left-0 right-0 border-t-2 border-red-500 z-30 pointer-events-none flex items-center"
                        :style="{ top: `${currentTimeLineTop}px` }">
                        <div class="h-2 w-2 rounded-full bg-red-500 -ml-1 shadow-md"></div>
                    </div>

                    <!-- Absolute positioned event cards container -->
                    <div class="absolute inset-0 pointer-events-none">
                        <div v-for="cita in getCitasForDay" :key="cita.id_cita"
                            class="absolute left-2.5 right-4 pointer-events-auto transition-all duration-200"
                            :style="getEventStyle(cita)">
                            <CalendarEvent
                                :cita="cita"
                                view="day"
                                @click="emit('click-event', cita)"
                                @dblclick="emit('dblclick-event', cita)"
                                @drag-start="handleDragStart"
                                @resize-start="handleResizeStart"
                            />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
