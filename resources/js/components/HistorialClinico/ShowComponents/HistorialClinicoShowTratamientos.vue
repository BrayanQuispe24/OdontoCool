<script setup lang="ts">
import type { HistorialClinico } from '@/types/HistorialClinico';

defineProps<{
    historial: HistorialClinico;
}>();
</script>

<template>
    <!-- Active Treatments -->
    <div class="print-card space-y-4 rounded-3xl border bg-[var(--card)] p-6 shadow-[0_10px_30px_rgba(0,169,157,0.04)] transition-colors duration-300 border-[var(--border)]">
        <h3 class="flex items-center gap-2 border-b pb-2 text-[length:var(--font-base)] font-black text-[var(--title)] border-[var(--border)]">
            <span>♧</span> Tratamientos Clínicos
        </h3>

        <div v-if="!historial.tratamiento || historial.tratamiento.length === 0"
            class="py-8 text-center text-[length:var(--font-xs)] text-[var(--muted)]">
            No registra tratamientos activos.
        </div>

        <div v-else class="max-h-[400px] space-y-4 overflow-y-auto pr-1">
            <div v-for="trat in historial.tratamiento" :key="trat.id"
                class="space-y-2.5 rounded-2xl border bg-[var(--card)] p-4 text-[length:var(--font-xs)] transition hover:bg-[var(--section-soft)]/50 print:bg-white border-[var(--border)]">
                <div class="flex items-center justify-between font-black text-[var(--title)]">
                    <span>♧ Tratamiento #{{ trat.id }}</span>

                    <span class="rounded-xl px-2.5 py-0.5 text-[length:var(--font-xs)] font-black uppercase tracking-wider"
                        :class="trat.estado === 'ACTIVO'
                            ? 'bg-[var(--primary-soft)] text-[var(--primary)]'
                            : 'bg-slate-100 dark:bg-slate-800 text-slate-500'">
                        {{ trat.estado }}
                    </span>
                </div>

                <p class="text-[length:var(--font-xs)] font-bold text-[var(--title)]">
                    Objetivo: {{ trat.objetivo_tratamiento }}
                </p>

                <div class="text-[length:var(--font-xs)] text-[var(--muted)]">
                    Duración: {{ trat.fecha_inicio }} al {{ trat.fecha_fin_estimada }}
                </div>

                <p v-if="trat.observacion" class="text-[var(--muted)]">
                    <strong class="text-[var(--title)]">Observación:</strong>
                    {{ trat.observacion }}
                </p>

                <p v-if="trat.fecha_fin_real" class="font-bold text-emerald-600 dark:text-emerald-400">
                    Terminado el: {{ trat.fecha_fin_real }}
                </p>
            </div>
        </div>
    </div>
</template>
