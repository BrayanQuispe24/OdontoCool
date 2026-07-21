<script setup lang="ts">
import type { HistorialClinico } from '@/types/HistorialClinico';

defineProps<{
    historial: HistorialClinico;
}>();
</script>

<template>
    <!-- Dental History Records -->
    <div class="print-card space-y-4 rounded-3xl border bg-[var(--card)] p-6 shadow-[0_10px_30px_rgba(0,169,157,0.04)] transition-colors duration-300 border-[var(--border)]">
        <div class="flex items-center justify-between border-b pb-3 border-[var(--border)]">
            <h3 class="flex items-center gap-2 text-[length:var(--font-base)] font-black text-[var(--title)]">
                <span>🦷</span> Antecedentes Odontológicos Previos
            </h3>
        </div>

        <div v-if="historial.antecedentes_odontologicos" class="space-y-4">
            <div class="flex items-center justify-between rounded-xl border bg-[var(--section-soft)] p-3 text-[length:var(--font-xs)] print:bg-white border-[var(--border)]">
                <span class="font-bold text-[var(--title)]">
                    Fecha de Registro: {{ historial.antecedentes_odontologicos.fecha_registro }}
                </span>

                <span v-if="historial.antecedentes_odontologicos.observacion_general" class="text-[var(--muted)]">
                    Nota: {{ historial.antecedentes_odontologicos.observacion_general }}
                </span>
            </div>

            <div class="space-y-3">
                <h4 class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                    Historial de Tratamientos Realizados
                </h4>

                <div v-if="!historial.antecedentes_odontologicos.detalle_antecedentes || historial.antecedentes_odontologicos.detalle_antecedentes.length === 0"
                    class="py-4 text-center text-[length:var(--font-xs)] text-[var(--muted)]">
                    Sin tratamientos detallados registrados en los antecedentes.
                </div>

                <div v-else v-for="det in historial.antecedentes_odontologicos.detalle_antecedentes"
                    :key="det.id_detalle_antecedente"
                    class="space-y-2 rounded-2xl border bg-[var(--section-soft)] p-4 text-[length:var(--font-xs)] transition hover:border-[var(--primary)] border-[var(--border)]">
                    <div class="flex items-center justify-between text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        <span>🦷 {{ det.nombre_tratamiento }}</span>

                        <span class="rounded-xl bg-[var(--primary-soft)] px-3 py-1 text-[length:var(--font-xs)] text-[var(--primary)] font-black border border-[var(--primary-soft)]">
                            {{ det.fecha_tratamiento }}
                        </span>
                    </div>

                    <p class="text-[var(--muted)]">
                        <strong class="text-[var(--title)]">Lugar/Clínica:</strong>
                        {{ det.lugar_tratamiento }}
                    </p>

                    <p v-if="det.descripcion" class="leading-relaxed text-[var(--muted)]">
                        {{ det.descripcion }}
                    </p>

                    <p v-if="det.observacion" class="text-[length:var(--font-xs)] italic text-[var(--muted)]">
                        Observación: {{ det.observacion }}
                    </p>
                </div>
            </div>
        </div>

        <div v-else
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed bg-[var(--section-soft)] py-12 text-[var(--muted)] border-[var(--border)]">
            <span class="text-4xl mb-2">🦷</span>

            <p class="text-[length:var(--font-xs)] font-bold">
                No se registran antecedentes odontológicos históricos para este paciente.
            </p>
        </div>
    </div>
</template>
