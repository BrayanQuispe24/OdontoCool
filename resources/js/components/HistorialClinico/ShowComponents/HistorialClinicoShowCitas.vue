<script setup lang="ts">
import type { HistorialClinico } from '@/types/HistorialClinico';

defineProps<{
    historial: HistorialClinico;
}>();
</script>

<template>
    <!-- Appointments -->
    <div class="print-card space-y-4 rounded-3xl border bg-[var(--card)] p-6 shadow-[0_10px_30px_rgba(0,169,157,0.04)] transition-colors duration-300 border-[var(--border)]">
        <h3 class="flex items-center gap-2 border-b pb-2 text-[length:var(--font-base)] font-black text-[var(--title)] border-[var(--border)]">
            <span>📅</span> Historial de Citas
        </h3>

        <div v-if="!historial.citas || historial.citas.length === 0"
            class="py-8 text-center text-[length:var(--font-xs)] text-[var(--muted)]">
            No registra citas agendadas.
        </div>

        <div v-else class="max-h-[400px] space-y-4 overflow-y-auto pr-1">
            <div v-for="cita in historial.citas" :key="cita.id_cita"
                class="space-y-2.5 rounded-2xl border bg-[var(--card)] p-4 text-[length:var(--font-xs)] transition hover:bg-[var(--section-soft)]/50 print:bg-white border-[var(--border)]">
                <div class="flex items-center justify-between font-black text-[var(--title)]">
                    <span class="text-[length:var(--font-xs)]">Cita #{{ cita.id_cita }}</span>

                    <span class="font-semibold text-[var(--muted)]">
                        {{ cita.fecha_cita }}
                    </span>
                </div>

                <div class="text-[length:var(--font-xs)] text-[var(--muted)]">
                    Horario: {{ cita.hora_inicio }} - {{ cita.hora_fin }}
                </div>

                <p class="text-[var(--muted)]">
                    <strong class="text-[var(--title)]">Motivo:</strong>
                    {{ cita.motivo }}
                </p>

                <p v-if="cita.doctor" class="text-[var(--muted)]">
                    <strong class="text-[var(--title)]">Doctor:</strong>
                    {{ cita.doctor.persona?.nombre }} {{ cita.doctor.persona?.apellido }}
                </p>

                <!-- Diagnosis -->
                <div v-if="cita.diagnostico"
                    class="mt-3 space-y-1.5 rounded-xl border bg-[var(--section-soft)] p-3 border-[var(--border)]">
                    <div class="text-[length:var(--font-xs)] font-black uppercase tracking-wide text-[var(--primary)]">
                        🔍 Diagnóstico Clínico
                    </div>

                    <p class="text-[length:var(--font-xs)] font-bold text-[var(--title)]">
                        {{ cita.diagnostico.tipo_diagnostico }} (Gravedad: {{ cita.diagnostico.gravedad }})
                    </p>

                    <p class="text-[var(--muted)]">
                        <strong class="text-[var(--title)]">Síntomas:</strong>
                        {{ cita.diagnostico.sintomas }}
                    </p>

                    <p v-if="cita.diagnostico.observaciones" class="text-[length:var(--font-xs)] italic text-[var(--muted)]">
                        Observación: {{ cita.diagnostico.observaciones }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
