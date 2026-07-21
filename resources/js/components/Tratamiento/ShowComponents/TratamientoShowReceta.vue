<script setup lang="ts">
import { RecetaRecomendaciones } from '@/types/RecetaRecomendaciones';

defineProps<{
    receta?: RecetaRecomendaciones;
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
            Prescripciones
        </p>

        <h2 class="mt-1 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
            Receta e Indicaciones Médicas
        </h2>

        <div v-if="receta" class="mt-5 space-y-4">
            <div
                class="flex flex-col justify-between gap-4 rounded-2xl border bg-[var(--section-soft)] p-5 md:flex-row md:items-center"
                style="border-color: var(--border)"
            >
                <div>
                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Fecha de Emisión
                    </p>

                    <p class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        {{ receta.fecha_emision }}
                    </p>
                </div>

                <div class="flex-1 md:ml-6">
                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Observación General de Receta
                    </p>

                    <p class="text-[length:var(--font-sm)] italic text-[var(--muted)]">
                        "{{ receta.observacion_general || 'Sin observaciones.' }}"
                    </p>
                </div>
            </div>

            <!-- Details Table -->
            <div
                v-if="receta.detalles?.length"
                class="mt-3 overflow-x-auto"
            >
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr
                            class="border-b bg-[var(--section-soft)] text-[length:var(--font-xs)] font-black uppercase tracking-wider text-[var(--muted)]"
                            style="border-color: var(--border)"
                        >
                            <th class="rounded-l-xl px-6 py-3">Medicamento</th>
                            <th class="px-6 py-3">Dosis</th>
                            <th class="px-6 py-3">Frecuencia</th>
                            <th class="px-6 py-3">Duración</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y text-[length:var(--font-xs)]" style="border-color: var(--border)">
                        <tr
                            v-for="d in receta.detalles"
                            :key="d.id"
                            class="transition hover:bg-[var(--section-soft)]"
                        >
                            <td class="px-6 py-3 font-bold text-[var(--title)]">
                                {{ d.descripcion }}
                            </td>

                            <td class="px-6 py-3 text-[var(--muted)]">
                                {{ d.dosis || 'N/A' }}
                            </td>

                            <td class="px-6 py-3 text-[var(--muted)]">
                                {{ d.frecuencia || 'N/A' }}
                            </td>

                            <td class="px-6 py-3 text-[var(--muted)]">
                                {{ d.duracion || 'N/A' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            v-else
            class="mt-4 flex flex-col items-center justify-center rounded-2xl border border-dashed bg-[var(--section-soft)] p-6 text-center"
            style="border-color: var(--border)"
        >
            <span class="text-[length:var(--font-2xl)]">💊</span>

            <p class="mt-2 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                No se ha emitido receta para este tratamiento.
            </p>
        </div>
    </div>
</template>
