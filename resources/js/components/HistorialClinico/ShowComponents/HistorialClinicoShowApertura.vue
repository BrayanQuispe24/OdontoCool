<script setup lang="ts">
import type { HistorialClinico } from '@/types/HistorialClinico';

defineProps<{
    historial: HistorialClinico;
}>();
</script>

<template>
    <!-- Apertura motive -->
    <div class="print-card space-y-3 rounded-3xl border bg-[var(--card)] p-6 shadow-[0_10px_30px_rgba(0,169,157,0.04)] transition-colors duration-300 border-[var(--border)]">
        <h3 class="text-[length:var(--font-sm)] font-black uppercase tracking-wide text-[var(--primary)]">
            Motivo de Apertura de Ficha
        </h3>

        <p class="text-[length:var(--font-sm)] font-semibold leading-relaxed text-[var(--title)]">
            {{ historial.motivo_apertura }}
        </p>

        <div class="flex items-center gap-4 text-[length:var(--font-xs)] text-[var(--muted)] border-t pt-3 border-[var(--border)]">
            <span>
                Aperturado el: <strong>{{ historial.fecha_apertura }}</strong>
            </span>

            <span v-if="historial.fecha_actualizacion">
                Actualizado el: <strong>{{ historial.fecha_actualizacion }}</strong>
            </span>
        </div>

        <p v-if="historial.observaciones_generales" class="mt-2 text-[length:var(--font-xs)] italic text-[var(--muted)]">
            Observaciones adicionales: {{ historial.observaciones_generales }}
        </p>
    </div>
</template>
