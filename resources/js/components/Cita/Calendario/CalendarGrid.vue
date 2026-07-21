<script setup lang="ts">
import type { Cita } from '@/types/Cita';
import CalendarMonthView from './CalendarMonthView.vue';
import CalendarWeekView from './CalendarWeekView.vue';
import CalendarDayView from './CalendarDayView.vue';

defineProps<{
    view: 'day' | 'week' | 'month';
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

const handleDragStart = (e: DragEvent, cita: Cita) => {
    emit('drag-start', e, cita);
};

const handleMonthDrop = (cita: Cita, targetDate: Date) => {
    // For Month view drops, default the hour to the original hour or 09:00
    const hour = cita.hora_inicio ? cita.hora_inicio.substring(0, 5) : '09:00';
    emit('drop-event', cita, targetDate, hour);
};

const handleWeekDrop = (cita: Cita, targetDate: Date, hourStr: string) => {
    emit('drop-event', cita, targetDate, hourStr);
};

const handleDayDrop = (cita: Cita, targetDate: Date, hourStr: string) => {
    emit('drop-event', cita, targetDate, hourStr);
};
</script>

<template>
    <div class="flex-1 flex flex-col h-full min-h-[500px]">
        <CalendarMonthView
            v-if="view === 'month'"
            :current-date="currentDate"
            :citas="citas"
            @click-event="(c) => emit('click-event', c)"
            @dblclick-event="(c) => emit('dblclick-event', c)"
            @drag-start="handleDragStart"
            @drop-event="handleMonthDrop"
            @click-empty-cell="(date) => emit('click-empty-slot', date, '09:00')"
        />

        <CalendarWeekView
            v-else-if="view === 'week'"
            :current-date="currentDate"
            :citas="citas"
            @click-event="(c) => emit('click-event', c)"
            @dblclick-event="(c) => emit('dblclick-event', c)"
            @drag-start="handleDragStart"
            @drop-event="handleWeekDrop"
            @resize-event="(c, minutes) => emit('resize-event', c, minutes)"
            @click-empty-slot="(date, hour) => emit('click-empty-slot', date, hour)"
        />

        <CalendarDayView
            v-else-if="view === 'day'"
            :current-date="currentDate"
            :citas="citas"
            @click-event="(c) => emit('click-event', c)"
            @dblclick-event="(c) => emit('dblclick-event', c)"
            @drag-start="handleDragStart"
            @drop-event="handleDayDrop"
            @resize-event="(c, minutes) => emit('resize-event', c, minutes)"
            @click-empty-slot="(date, hour) => emit('click-empty-slot', date, hour)"
        />
    </div>
</template>
