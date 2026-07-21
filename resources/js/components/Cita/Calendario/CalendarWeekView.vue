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

const weekDaysNames = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

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

// Calculate dates for the current week starting on Monday
const weekDates = computed(() => {
    const dates = [];
    const current = new Date(props.currentDate);
    const day = current.getDay();
    // Monday is index 0. If current day is Sunday (0), shift by -6. Else shift by -(day - 1).
    const shift = day === 0 ? -6 : -(day - 1);
    const startOfWeek = new Date(current);
    startOfWeek.setDate(current.getDate() + shift);

    for (let i = 0; i < 7; i++) {
        const d = new Date(startOfWeek);
        d.setDate(startOfWeek.getDate() + i);
        dates.push(d);
    }
    return dates;
});

const isSameDay = (d1: Date, d2: Date) => {
    return (
        d1.getFullYear() === d2.getFullYear() &&
        d1.getMonth() === d2.getMonth() &&
        d1.getDate() === d2.getDate()
    );
};

// Filter appointments for a specific day
const getCitasForDay = (date: Date) => {
    return props.citas.filter((cita) => {
        if (!cita.fecha_cita) return false;
        const [year, month, day] = cita.fecha_cita.split('-').map(Number);
        const citaDate = new Date(year, month - 1, day);
        return isSameDay(citaDate, date);
    });
};

// Convert "HH:MM" string to decimal hours
const timeToDecimal = (timeStr?: string) => {
    if (!timeStr) return 0;
    const [h, m] = timeStr.split(':').map(Number);
    return h + m / 60;
};

// Calculate absolute position style for appointment card
const getEventStyle = (cita: Cita) => {
    const start = timeToDecimal(cita.hora_inicio);
    const end = timeToDecimal(cita.hora_fin);
    
    // Clamp to working hours range
    const startClamped = Math.max(workStartHour, Math.min(workEndHour, start));
    const endClamped = Math.max(workStartHour, Math.min(workEndHour, end));
    
    const top = (startClamped - workStartHour) * hourHeight;
    const height = (endClamped - startClamped) * hourHeight;
    
    return {
        top: `${top}px`,
        height: `${height > 25 ? height : 25}px` // min height of 25px
    };
};

const handleDragStart = (e: DragEvent, cita: Cita) => {
    emit('drag-start', e, cita);
};

// Drag & drop drop handler
const handleDrop = (e: DragEvent, targetDate: Date, hour: number) => {
    const citaData = e.dataTransfer?.getData('application/json');
    if (!citaData) return;
    try {
        const cita = JSON.parse(citaData) as Cita;
        const hourStr = `${String(hour).padStart(2, '0')}:00`;
        emit('drop-event', cita, targetDate, hourStr);
    } catch (err) {
        console.error('Error Week View drop', err);
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

// Current time indicator line offset
const currentTimeLineTop = computed(() => {
    const currentHour = now.value.getHours() + now.value.getMinutes() / 60;
    if (currentHour >= workStartHour && currentHour <= workEndHour) {
        return (currentHour - workStartHour) * hourHeight;
    }
    return null;
});

const isToday = (date: Date) => {
    return isSameDay(date, new Date());
};
</script>

<template>
    <div class="flex flex-col h-full bg-[var(--card)] rounded-2xl border shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden border-[var(--border)] transition-all duration-300">
        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto min-h-[480px]">
            <!-- Week Header (Sticky inside the scroll view to align perfectly with scrollbar track) -->
            <div class="sticky top-0 z-40 grid grid-cols-[64px_1fr] border-b border-[var(--border)] bg-[var(--card)]">
                <!-- empty corner -->
                <div class="border-r border-[var(--border)] bg-[var(--section-soft)]/45"></div>
                
                <div class="grid grid-cols-7 divide-x divide-[var(--border)] bg-[var(--section-soft)]/45">
                    <div v-for="(day, idx) in weekDates" :key="idx"
                        class="py-3 text-center transition-colors"
                        :class="isToday(day) ? 'bg-[var(--primary-soft)]/20' : ''">
                        <p class="text-[10px] font-black uppercase tracking-[0.15em] text-[var(--muted)]">
                            {{ weekDaysNames[idx] }}
                        </p>
                        <p class="text-lg font-black mt-0.5"
                            :class="isToday(day)
                                ? 'text-[var(--primary)] font-black'
                                : 'text-[var(--text)]'
                            ">
                            {{ day.getDate() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Hours Grid -->
            <div class="grid grid-cols-[64px_1fr] relative" :style="{ height: `${(hours.length - 1) * hourHeight}px` }">
                
                <!-- Time Labels Column -->
                <div class="border-r bg-[var(--section-soft)]/10 text-right pr-3 select-none border-[var(--border)]">
                    <div v-for="(h, idx) in hours" :key="h"
                        class="text-[10px] font-black tracking-wider text-[var(--muted)] flex items-center justify-end"
                        :style="{ height: `${hourHeight}px`, transform: idx === 0 ? 'translateY(0)' : 'translateY(-50%)' }">
                        {{ String(h).padStart(2, '0') }}:00
                    </div>
                </div>

                <!-- Grid Columns -->
                <div class="grid grid-cols-7 relative divide-x divide-[var(--border)]">
                    
                    <!-- Vertical grid columns for each day -->
                    <div v-for="(day, dayIdx) in weekDates" :key="dayIdx"
                        class="relative h-full select-none"
                        :class="isToday(day) ? 'bg-[var(--primary-soft)]/5' : ''">
                        
                        <!-- Horizontal slot dividers for hour clicks -->
                        <div v-for="h in hours.slice(0, -1)" :key="h"
                            @dragover.prevent
                            @drop="handleDrop($event, day, h)"
                            @click="emit('click-empty-slot', day, `${String(h).padStart(2, '0')}:00`)"
                            class="border-b hover:bg-[var(--section-soft)]/30 cursor-pointer border-[var(--border)] transition-colors"
                            :style="{ height: `${hourHeight}px` }">
                        </div>

                        <!-- Current time indicator line -->
                        <div v-if="isToday(day) && currentTimeLineTop !== null"
                            class="absolute left-0 right-0 border-t-2 border-red-500 z-30 pointer-events-none flex items-center"
                            :style="{ top: `${currentTimeLineTop}px` }">
                            <div class="h-2 w-2 rounded-full bg-red-500 -ml-1 shadow-md"></div>
                        </div>

                        <!-- Absolute event cards container -->
                        <div class="absolute inset-0 pointer-events-none">
                            <div v-for="cita in getCitasForDay(day)" :key="cita.id_cita"
                                class="absolute left-1 right-1 pointer-events-auto transition-all duration-200"
                                :style="getEventStyle(cita)">
                                <CalendarEvent
                                    :cita="cita"
                                    view="week"
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
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 2px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: transparent;
}
</style>
