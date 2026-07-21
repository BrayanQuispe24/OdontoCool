<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import type { Cita } from '@/types/Cita';

const props = defineProps<{
    currentDate: Date;
    citasHoyCount: number;
    citasPendientesCount: number;
    citasCanceladasCount: number;
    citasDisponiblesCount: number;
    proximasCitas: Cita[];
}>();

const emit = defineEmits<{
    'select-date': [date: Date];
}>();

// Mini-calendar navigation state
const miniCalendarDate = ref(new Date(props.currentDate));

watch(
    () => props.currentDate,
    (newDate) => {
        miniCalendarDate.value = new Date(newDate);
    }
);

const miniMonthName = computed(() => {
    return miniCalendarDate.value.toLocaleString('es-ES', { month: 'long', year: 'numeric' });
});

const prevMiniMonth = () => {
    const d = new Date(miniCalendarDate.value);
    d.setMonth(d.getMonth() - 1);
    miniCalendarDate.value = d;
};

const nextMiniMonth = () => {
    const d = new Date(miniCalendarDate.value);
    d.setMonth(d.getMonth() + 1);
    miniCalendarDate.value = d;
};

// Generate 42 calendar grid cells for mini calendar
const miniCalendarDays = computed(() => {
    const year = miniCalendarDate.value.getFullYear();
    const month = miniCalendarDate.value.getMonth();
    
    // First day of current month
    const firstDay = new Date(year, month, 1);
    let startDay = firstDay.getDay(); 
    startDay = startDay === 0 ? 6 : startDay - 1; // Align to Monday week start
    
    const totalDays = new Date(year, month + 1, 0).getDate();
    const prevMonthTotalDays = new Date(year, month, 0).getDate();
    
    const days: Array<{ date: Date; isCurrentMonth: boolean; isSelected: boolean; isToday: boolean }> = [];
    
    // Previous month padding
    for (let i = startDay - 1; i >= 0; i--) {
        const d = new Date(year, month - 1, prevMonthTotalDays - i);
        days.push({
            date: d,
            isCurrentMonth: false,
            isSelected: isSameDay(d, props.currentDate),
            isToday: isSameDay(d, new Date()),
        });
    }
    
    // Current month days
    for (let i = 1; i <= totalDays; i++) {
        const d = new Date(year, month, i);
        days.push({
            date: d,
            isCurrentMonth: true,
            isSelected: isSameDay(d, props.currentDate),
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
            isSelected: isSameDay(d, props.currentDate),
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

const handleDayClick = (date: Date) => {
    emit('select-date', date);
};

const formatTime = (timeStr?: string) => {
    if (!timeStr) return '';
    return timeStr.substring(0, 5);
};
</script>

<template>
    <aside class="flex flex-col gap-6 w-full lg:w-[270px] shrink-0 rounded-2xl border p-5 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border-[var(--border)] bg-[var(--card)] transition-all duration-300">
        <!-- Mini Calendar -->
        <div class="rounded-xl border p-3.5 border-[var(--border)] bg-[var(--card)]">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-black capitalize text-[var(--title)]">
                    {{ miniMonthName }}
                </span>
                <div class="flex items-center gap-1.5">
                    <button type="button" @click="prevMiniMonth" class="rounded-lg p-1 text-[var(--muted)] hover:bg-[var(--section-soft)] transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button type="button" @click="nextMiniMonth" class="rounded-lg p-1 text-[var(--muted)] hover:bg-[var(--section-soft)] transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Week days header -->
            <div class="grid grid-cols-7 text-center mb-2 text-[10px] font-black text-[var(--muted)] tracking-wider opacity-75 uppercase">
                <span>L</span><span>M</span><span>M</span><span>J</span><span>V</span><span>S</span><span>D</span>
            </div>

            <!-- Days grid -->
            <div class="grid grid-cols-7 text-center gap-y-1.5">
                <button type="button"
                    v-for="(day, idx) in miniCalendarDays"
                    :key="idx"
                    @click="handleDayClick(day.date)"
                    class="h-[26px] w-[26px] mx-auto flex items-center justify-center rounded-full text-xs font-bold transition-all"
                    :class="[
                        day.isCurrentMonth ? 'text-[var(--text)] font-semibold' : 'text-[var(--muted)] opacity-40',
                        day.isSelected ? 'bg-[var(--primary)] text-white font-black hover:bg-[var(--primary)] shadow-sm' : '',
                        !day.isSelected && day.isToday ? 'border border-[var(--primary)] text-[var(--primary)] font-black' : '',
                        !day.isSelected ? 'hover:bg-[var(--section-soft)]' : ''
                    ]">
                    {{ day.date.getDate() }}
                </button>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div>
            <h3 class="text-[10px] font-black uppercase tracking-[0.15em] text-[var(--muted)] mb-3 select-none">
                Estadísticas de Hoy
            </h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl border p-3 border-[var(--border)] bg-[var(--section-soft)]/40 hover:bg-[var(--section-soft)]/75 transition-all">
                    <p class="text-[10px] text-[var(--muted)] font-black uppercase tracking-wider">Hoy</p>
                    <p class="text-xl font-black text-[var(--title)] mt-0.5">{{ citasHoyCount }}</p>
                </div>

                <div class="rounded-xl border p-3 border-amber-250 bg-amber-500/5 hover:bg-amber-500/10 transition-all dark:border-amber-900/40">
                    <p class="text-[10px] text-amber-600 dark:text-amber-400 font-black uppercase tracking-wider">Espera</p>
                    <p class="text-xl font-black text-amber-600 dark:text-amber-400 mt-0.5">{{ citasPendientesCount }}</p>
                </div>

                <div class="rounded-xl border p-3 border-rose-250 bg-rose-500/5 hover:bg-rose-500/10 transition-all dark:border-rose-900/40">
                    <p class="text-[10px] text-rose-600 dark:text-rose-450 font-black uppercase tracking-wider">Canc.</p>
                    <p class="text-xl font-black text-rose-600 dark:text-rose-450 mt-0.5">{{ citasCanceladasCount }}</p>
                </div>

                <div class="rounded-xl border p-3 border-emerald-250 bg-emerald-500/5 hover:bg-emerald-500/10 transition-all dark:border-emerald-900/40">
                    <p class="text-[10px] text-emerald-600 dark:text-emerald-450 font-black uppercase tracking-wider">Disp.</p>
                    <p class="text-xl font-black text-emerald-600 dark:text-emerald-450 mt-0.5">{{ citasDisponiblesCount }}h</p>
                </div>
            </div>
        </div>

        <!-- Próximas Citas -->
        <div class="flex-1 min-h-[220px] overflow-hidden flex flex-col">
            <h3 class="text-[10px] font-black uppercase tracking-[0.15em] text-[var(--muted)] mb-3 select-none">
                Próximas Citas
            </h3>
            
            <div class="flex-1 overflow-y-auto space-y-2.5 pr-1 custom-scrollbar">
                <div v-for="cita in proximasCitas" :key="cita.id_cita"
                    @click="handleDayClick(new Date(cita.fecha_cita))"
                    class="cursor-pointer group flex items-start gap-3 rounded-xl border p-2.5 transition border-[var(--border)] bg-[var(--card)] hover:border-[var(--primary-soft)] hover:bg-[var(--section-soft)]/50">
                    
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[var(--primary-soft)] text-[10px] font-black text-[var(--primary)] shadow-[0_2px_10px_rgba(0,169,157,0.06)] group-hover:scale-105 transition-all">
                        {{ formatTime(cita.hora_inicio) }}
                    </div>
                    
                    <div class="min-w-0">
                        <p class="text-xs font-black text-[var(--text)] group-hover:text-[var(--primary)] truncate transition">
                            {{ cita.paciente?.persona?.nombre }} {{ cita.paciente?.persona?.apellido }}
                        </p>
                        <p class="text-[10px] text-[var(--muted)] font-semibold truncate mt-0.5 flex items-center gap-1">
                            <span class="h-1 w-1 rounded-full bg-[var(--primary)]"></span>
                            Dr. {{ cita.doctor?.persona?.nombre }}
                        </p>
                    </div>
                </div>

                <div v-if="proximasCitas.length === 0" class="text-center py-8 rounded-xl border border-dashed border-[var(--border)]">
                    <p class="text-[11px] text-[var(--muted)] font-bold">No hay próximas citas.</p>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 2px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: transparent;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
    background: var(--border);
}
</style>
