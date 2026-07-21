<script setup lang="ts">
import { ref, computed } from 'vue';
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
    'drop-event': [cita: Cita, targetDate: Date];
    'click-empty-cell': [date: Date];
}>();

const weekDays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

const draggedOverDay = ref<number | null>(null);

const calendarDays = computed(() => {
    const year = props.currentDate.getFullYear();
    const month = props.currentDate.getMonth();
    
    const firstDay = new Date(year, month, 1);
    let startDay = firstDay.getDay();
    startDay = startDay === 0 ? 6 : startDay - 1; // Align to Monday week start
    
    const totalDays = new Date(year, month + 1, 0).getDate();
    const prevMonthTotalDays = new Date(year, month, 0).getDate();
    
    const days: Array<{ date: Date; isCurrentMonth: boolean; isToday: boolean }> = [];
    
    // Previous month padding
    for (let i = startDay - 1; i >= 0; i--) {
        const d = new Date(year, month - 1, prevMonthTotalDays - i);
        days.push({
            date: d,
            isCurrentMonth: false,
            isToday: isSameDay(d, new Date()),
        });
    }
    
    // Current month days
    for (let i = 1; i <= totalDays; i++) {
        const d = new Date(year, month, i);
        days.push({
            date: d,
            isCurrentMonth: true,
            isToday: isSameDay(d, new Date()),
        });
    }
    
    // Next month padding
    const remaining = 42 - days.length;
    for (let i = 1; i <= remaining; i++) {
        const d = new Date(year, month + 1, i);
        days.push({
            date: d,
            isCurrentMonth: false,
            isToday: isSameDay(d, new Date()),
        });
    }
    
    return days;
});

const isSameDay = (d1: Date, d2: Date) => {
    return (
        d1.getFullYear() === d2.getFullYear() &&
        d1.getMonth() === d2.getMonth() &&
        d1.getDate() === d2.getDate()
    );
};

const getCitasForDay = (date: Date) => {
    return props.citas.filter((cita) => {
        if (!cita.fecha_cita) return false;
        const [year, month, day] = cita.fecha_cita.split('-').map(Number);
        const citaDate = new Date(year, month - 1, day);
        return isSameDay(citaDate, date);
    }).sort((a, b) => (a.hora_inicio ?? '').localeCompare(b.hora_inicio ?? ''));
};

const handleDragStart = (e: DragEvent, cita: Cita) => {
    emit('drag-start', e, cita);
};

const handleDrop = (e: DragEvent, targetDate: Date) => {
    draggedOverDay.value = null;
    const citaData = e.dataTransfer?.getData('application/json');
    if (!citaData) return;
    try {
        const cita = JSON.parse(citaData) as Cita;
        emit('drop-event', cita, targetDate);
    } catch (err) {
        console.error('Error parsed drop data', err);
    }
};
</script>

<template>
    <div class="flex flex-col h-full bg-[var(--card)] rounded-2xl border shadow-[0_4px_20px_rgba(0,0,0,0.01)] overflow-hidden border-[var(--border)]">
        <!-- Week Header -->
        <div class="grid grid-cols-7 border-b bg-[var(--section-soft)]/50 text-center py-2.5 text-xs font-bold text-[var(--muted)] border-[var(--border)]">
            <div v-for="day in weekDays" :key="day" class="uppercase tracking-wider text-[10px] font-black">
                {{ day }}
            </div>
        </div>

        <!-- Month Grid -->
        <div class="grid grid-cols-7 grid-rows-6 flex-1 divide-x divide-y border-t border-[var(--border)] divide-[var(--border)]">
            <div v-for="(day, idx) in calendarDays" :key="idx"
                @dragover.prevent="draggedOverDay = idx"
                @dragleave="draggedOverDay = null"
                @drop="handleDrop($event, day.date)"
                @click="emit('click-empty-cell', day.date)"
                class="relative min-h-[90px] flex flex-col p-1.5 transition-colors group/cell"
                :class="[
                    day.isCurrentMonth ? 'bg-[var(--card)]' : 'bg-[var(--section-soft)]/20 text-[var(--muted)]',
                    draggedOverDay === idx ? 'bg-[var(--primary-soft)]/30 border border-dashed border-[var(--primary)]' : ''
                ]">
                
                <!-- Day number and Today highlight -->
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-bold"
                        :class="[
                            day.isCurrentMonth ? 'text-[var(--title)]' : 'text-[var(--muted)] opacity-60',
                            day.isToday ? 'bg-[var(--primary)] text-white rounded-full h-5 w-5 flex items-center justify-center font-black shadow-sm' : ''
                        ]">
                        {{ day.date.getDate() }}
                    </span>
                    
                    <!-- Quick create indicator on cell hover -->
                    <span class="hidden group-hover/cell:inline-block text-[10px] text-[var(--primary)] font-bold transition-all cursor-pointer opacity-70 hover:opacity-100"
                        @click.stop="emit('click-empty-cell', day.date)">
                        + Añadir
                    </span>
                </div>

                <!-- Event items -->
                <div class="flex-1 overflow-y-auto space-y-1 pr-0.5 custom-scrollbar">
                    <div v-for="cita in getCitasForDay(day.date)" :key="cita.id_cita" class="h-[28px] overflow-hidden">
                        <CalendarEvent
                            :cita="cita"
                            view="month"
                            @click="emit('click-event', cita)"
                            @dblclick="emit('dblclick-event', cita)"
                            @drag-start="handleDragStart"
                        />
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
.group\/cell:hover .custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--primary-soft, #e2e8f0);
}
</style>
