<script setup lang="ts">
import { Tratamiento } from '@/types/Tratamiento';

defineProps<{
    tratamiento: Tratamiento;
}>();
</script>

<template>
    <div class="grid gap-5 md:grid-cols-3">
        <!-- Col 1: Dates -->
        <div
            class="space-y-4 rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
            style="border-color: var(--border)"
        >
            <h3 class="text-[length:var(--font-xs)] font-bold uppercase tracking-[0.2em] text-[var(--primary)]">
                Fechas y Cronograma
            </h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Fecha Inicio
                    </p>

                    <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        {{ tratamiento.fecha_inicio }}
                    </p>
                </div>

                <div>
                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Fin Estimado
                    </p>

                    <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        {{ tratamiento.fecha_fin_estimada }}
                    </p>
                </div>

                <div
                    class="col-span-2 border-t pt-3"
                    style="border-color: var(--border)"
                >
                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Fecha Fin Real
                    </p>

                    <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        {{ tratamiento.fecha_fin_real || 'En proceso / Pendiente' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Col 2: Info & Status -->
        <div
            class="space-y-4 rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
            style="border-color: var(--border)"
        >
            <h3 class="text-[length:var(--font-xs)] font-bold uppercase tracking-[0.2em] text-[var(--primary)]">
                Estado Clínico
            </h3>

            <div>
                <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                    Estado de Ficha
                </p>

                <span
                    class="mt-2 inline-flex rounded-full px-3.5 py-1.5 text-[length:var(--font-xs)] font-black"
                    :class="tratamiento.estado === 'ACTIVO'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'"
                >
                    {{ tratamiento.estado }}
                </span>
            </div>

            <div
                class="border-t pt-3"
                style="border-color: var(--border)"
            >
                <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                    Tratamiento ID
                </p>

                <p class="mt-1 text-[length:var(--font-base)] font-black text-[var(--title)]">
                    #{{ tratamiento.id }}
                </p>
            </div>
        </div>

        <!-- Col 3: General Observation -->
        <div
            class="space-y-3 rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
            style="border-color: var(--border)"
        >
            <h3 class="text-[length:var(--font-xs)] font-bold uppercase tracking-[0.2em] text-[var(--primary)]">
                Observación del Tratamiento
            </h3>

            <p
                class="rounded-2xl border bg-[var(--section-soft)] p-4 text-[length:var(--font-sm)] italic leading-6 text-[var(--muted)]"
                style="border-color: var(--border)"
            >
                "{{ tratamiento.observacion || 'Sin observaciones registradas.' }}"
            </p>
        </div>
    </div>
</template>
