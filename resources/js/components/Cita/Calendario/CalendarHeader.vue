<script setup lang="ts">
import type { Doctor } from '@/types/Cita';

defineProps<{
    modelValueView: 'day' | 'week' | 'month';
    searchPaciente: string;
    estadoFiltro: string;
    doctorFiltro: string;
    doctores: Doctor[];
    estadosDisponibles: string[];
    formattedCurrentRange: string;
    tienePermisoCrear: boolean;
    esDoctor: boolean;
    doctorLogueadoCi: string;
}>();

const emit = defineEmits<{
    'update:modelValueView': [value: 'day' | 'week' | 'month'];
    'update:searchPaciente': [value: string];
    'update:estadoFiltro': [value: string];
    'update:doctorFiltro': [value: string];
    prev: [];
    next: [];
    today: [];
    create: [];
}>();
</script>

<template>
    <div class="flex flex-col gap-4 rounded-2xl border p-4 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border-[var(--border)] bg-[var(--card)] lg:flex-row lg:items-center lg:justify-between transition-all duration-300">
        <!-- Range navigation & Title -->
        <div class="flex items-center justify-between lg:justify-start gap-4">
            <h1 class="text-lg font-black tracking-tight text-[var(--title)] select-none">
                {{ formattedCurrentRange }}
            </h1>

            <div class="flex items-center rounded-xl border p-0.5 border-[var(--border)] bg-[var(--section-soft)]/60">
                <button type="button" @click="emit('prev')"
                    class="rounded-lg p-1.5 text-[var(--muted)] hover:bg-[var(--card)] hover:text-[var(--title)] transition-all duration-200">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button type="button" @click="emit('today')"
                    class="px-3 py-1 text-xs font-bold text-[var(--text)] hover:bg-[var(--card)] hover:text-[var(--title)] rounded-lg transition-all duration-200">
                    Hoy
                </button>

                <button type="button" @click="emit('next')"
                    class="rounded-lg p-1.5 text-[var(--muted)] hover:bg-[var(--card)] hover:text-[var(--title)] transition-all duration-200">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Filters & View Switcher -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search Input -->
            <div class="relative w-full max-w-[200px] sm:w-auto">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[var(--muted)]">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input :value="searchPaciente"
                    @input="emit('update:searchPaciente', ($event.target as HTMLInputElement).value)"
                    type="text" placeholder="Buscar paciente..."
                    class="w-full rounded-xl border bg-[var(--section-soft)]/50 py-1.5 pl-9 pr-3 text-xs text-[var(--title)] border-[var(--border)] outline-none transition-all placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary-soft)]" />
            </div>

            <!-- Doctor selector -->
            <div v-if="!esDoctor" class="relative">
                <select :value="doctorFiltro"
                    @change="emit('update:doctorFiltro', ($event.target as HTMLSelectElement).value)"
                    class="appearance-none rounded-xl border bg-[var(--section-soft)]/50 pl-3 pr-8 py-1.5 text-xs font-bold text-[var(--text)] border-[var(--border)] outline-none transition-all hover:bg-[var(--section-soft)] focus:bg-[var(--card)] focus:border-[var(--primary)]">
                    <option value="">Todos los doctores</option>
                    <option v-for="doc in doctores" :key="doc.codigo_doctor" :value="doc.codigo_doctor">
                        Dr. {{ doc.persona?.nombre }} {{ doc.persona?.apellido }}
                    </option>
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center text-[var(--muted)]">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </span>
            </div>

            <!-- Status selector -->
            <div class="relative">
                <select :value="estadoFiltro"
                    @change="emit('update:estadoFiltro', ($event.target as HTMLSelectElement).value)"
                    class="appearance-none rounded-xl border bg-[var(--section-soft)]/50 pl-3 pr-8 py-1.5 text-xs font-bold text-[var(--text)] border-[var(--border)] outline-none transition-all hover:bg-[var(--section-soft)] focus:bg-[var(--card)] focus:border-[var(--primary)]">
                    <option value="">Todos los estados</option>
                    <option v-for="est in estadosDisponibles" :key="est" :value="est">
                        {{ est }}
                    </option>
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center text-[var(--muted)]">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </span>
            </div>

            <!-- View switch -->
            <div class="flex items-center rounded-xl border p-0.5 border-[var(--border)] bg-[var(--section-soft)]/60">
                <button type="button" @click="emit('update:modelValueView', 'day')"
                    class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all duration-200"
                    :class="modelValueView === 'day'
                        ? 'bg-[var(--card)] text-[var(--primary)] shadow-sm font-black'
                        : 'text-[var(--muted)] hover:text-[var(--title)]'
                    ">
                    Día
                </button>
                <button type="button" @click="emit('update:modelValueView', 'week')"
                    class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all duration-200"
                    :class="modelValueView === 'week'
                        ? 'bg-[var(--card)] text-[var(--primary)] shadow-sm font-black'
                        : 'text-[var(--muted)] hover:text-[var(--title)]'
                    ">
                    Semana
                </button>
                <button type="button" @click="emit('update:modelValueView', 'month')"
                    class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all duration-200"
                    :class="modelValueView === 'month'
                        ? 'bg-[var(--card)] text-[var(--primary)] shadow-sm font-black'
                        : 'text-[var(--muted)] hover:text-[var(--title)]'
                    ">
                    Mes
                </button>
            </div>

            <!-- New Appointment Button -->
            <button v-if="tienePermisoCrear" type="button" @click="emit('create')"
                class="inline-flex items-center gap-1.5 rounded-xl bg-[var(--primary)] px-4 py-2 text-xs font-black text-white shadow-md hover:bg-[var(--primary)] hover:opacity-90 active:scale-95 transition-all duration-150">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva cita
            </button>
        </div>
    </div>
</template>
