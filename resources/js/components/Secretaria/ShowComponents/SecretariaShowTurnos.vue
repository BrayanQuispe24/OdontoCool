<script setup lang="ts">
import type { Turno } from '@/types/Turno';

defineProps<{
    turnosAsignados: Turno[];
    turnos: Turno[];
    turnoForm: any;
    editando: boolean;
    diasSemana: Array<{ value: string; label: string }>;
}>();

const emit = defineEmits<{
    'submit-turno': [];
}>();
</script>

<template>
    <!-- TURNOS DE LA SECRETARIA -->
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300 border-[var(--border)]">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Turnos
                </p>

                <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                    Turnos asignados actualmente a la secretaria.
                </p>
            </div>
        </div>

        <!-- LISTA DE TURNOS ASIGNADOS -->
        <div class="mt-5">
            <div v-if="turnosAsignados.length" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <article v-for="turno in turnosAsignados"
                    :key="turno.pivot?.id ?? `${turno.id}-${turno.pivot?.dia_semana}`"
                    class="rounded-2xl border bg-[var(--bg)] p-5 transition hover:bg-[var(--section-soft)]/50 border-[var(--border)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[length:var(--font-lg)] font-black text-[var(--title)]">
                                {{ turno.nombre }}
                            </p>

                            <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--primary)]">
                                {{ turno.hora_inicio }} - {{ turno.hora_fin }}
                            </p>
                        </div>

                        <span class="rounded-full px-4 py-2 text-[length:var(--font-xs)] font-black uppercase tracking-wider"
                            :class="turno.estado === 'activo'
                                ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20'
                                : 'bg-rose-500/10 text-rose-600 border border-rose-500/20'">
                            {{ turno.estado }}
                        </span>
                    </div>

                    <div class="mt-4 rounded-2xl bg-[var(--card)] px-4 py-3 border border-[var(--border)]">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-[0.2em] text-[var(--muted)]">
                            Día asignado
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)] capitalize">
                            {{ turno.pivot?.dia_semana ?? 'Sin día registrado' }}
                        </p>
                    </div>
                </article>
            </div>

            <div v-else
                class="rounded-2xl border border-dashed p-10 text-center text-[length:var(--font-sm)] font-bold text-[var(--muted)] border-[var(--border)] bg-[var(--section-soft)]/30">
                <span class="text-3xl block mb-2">📅</span>
                Esta secretaria todavía no tiene turnos asignados.
            </div>
        </div>

        <!-- FORMULARIO PARA AGREGAR TURNO -->
        <div v-if="editando" class="mt-6 rounded-3xl border border-dashed p-5 border-[var(--border)]">
            <p class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                Agregar nuevo turno
            </p>

            <p class="mt-1 text-[length:var(--font-sm)] text-[var(--muted)]">
                Selecciona un turno y marca uno o varios días en los que atenderá la secretaria.
            </p>

            <form class="mt-5 grid gap-5 md:grid-cols-3" @submit.prevent="emit('submit-turno')">
                <div>
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Turno
                    </label>

                    <select v-model="turnoForm.turno_id"
                        class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]">
                        <option value="">Seleccionar turno</option>
                        <option v-for="t in turnos" :key="t.id" :value="t.id">
                            {{ t.nombre }} | {{ t.hora_inicio }} - {{ t.hora_fin }}
                        </option>
                    </select>

                    <p v-if="turnoForm.errors.turno_id"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ turnoForm.errors.turno_id }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Días de atención
                    </label>

                    <div class="mt-2 grid gap-2 sm:grid-cols-2 md:grid-cols-3">
                        <label v-for="dia in diasSemana" :key="dia.value"
                            class="flex cursor-pointer items-center gap-3 rounded-2xl border bg-[var(--bg)] px-4 py-3 transition hover:bg-[var(--section-soft)]/50 border-[var(--border)]">
                            <input v-model="turnoForm.dias_semana" type="checkbox" :value="dia.value"
                                class="h-5 w-5 rounded border-[var(--border)] text-[var(--primary)] focus:ring-[var(--primary)]" />

                            <span class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                                {{ dia.label }}
                            </span>
                        </label>
                    </div>

                    <p v-if="turnoForm.errors.dias_semana"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ turnoForm.errors.dias_semana }}
                    </p>

                    <p v-if="turnoForm.errors.turnos"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ turnoForm.errors.turnos }}
                    </p>
                </div>

                <div class="flex items-end md:col-span-3 justify-end mt-4">
                    <button type="submit"
                        :disabled="turnoForm.processing || !turnoForm.turno_id || !turnoForm.dias_semana.length"
                        class="rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60 shadow-lg shadow-teal-100 dark:shadow-none">
                        {{ turnoForm.processing ? 'Agregando...' : 'Agregar turno' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
