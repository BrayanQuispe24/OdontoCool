<script setup lang="ts">
import { Tratamiento } from '@/types/Tratamiento';

defineProps<{
    tratamiento: Tratamiento;
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Dientes Intervenidos
                </p>

                <h2 class="mt-1 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                    Detalle de Tratamientos por Diente
                </h2>
            </div>

            <div
                class="rounded-2xl border bg-[var(--primary-soft)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)]"
                style="border-color: var(--border)"
            >
                {{ tratamiento.tratamientos_dientes?.length || 0 }} diente(s) asignado(s)
            </div>
        </div>

        <!-- Intervention Table -->
        <div v-if="tratamiento.tratamientos_dientes?.length" class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr
                        class="border-b bg-[var(--section-soft)] text-[length:var(--font-xs)] font-black uppercase tracking-wider text-[var(--muted)]"
                        style="border-color: var(--border)"
                    >
                        <th class="rounded-l-2xl px-6 py-4">Diente</th>
                        <th class="px-6 py-4">Cara Dental</th>
                        <th class="px-6 py-4">Procedimiento Planificado</th>
                        <th class="px-6 py-4">Observaciones del Diente</th>
                        <th class="rounded-r-2xl px-6 py-4">Fecha Registro</th>
                    </tr>
                </thead>

                <tbody class="divide-y text-[length:var(--font-sm)]" style="border-color: var(--border)">
                    <tr
                        v-for="td in tratamiento.tratamientos_dientes"
                        :key="td.id"
                        class="transition hover:bg-[var(--section-soft)]"
                    >
                        <td class="px-6 py-4 font-bold text-[var(--title)]">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border bg-[var(--primary-soft)] font-black text-[var(--primary)]"
                                    style="border-color: var(--border)"
                                >
                                    #{{ td.diente?.numero }}
                                </span>

                                <div>
                                    <p class="font-black text-[var(--title)]">
                                        {{ td.diente?.nombre }}
                                    </p>

                                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                        {{ td.diente?.tipo }} ({{ td.diente?.ubicacion }})
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 font-bold text-[var(--title)]">
                            <span
                                class="inline-flex rounded-lg border bg-[var(--section-soft)] px-2.5 py-1 text-[length:var(--font-xs)] font-black text-[var(--primary)]"
                                style="border-color: var(--border)"
                            >
                                {{ td.cara_dental }}
                            </span>
                        </td>

                        <td class="max-w-xs whitespace-pre-line px-6 py-4 leading-relaxed text-[var(--muted)]">
                            {{ td.tratamiento_planificado }}
                        </td>

                        <td class="max-w-xs px-6 py-4 italic leading-relaxed text-[var(--muted)]">
                            {{ td.observacion || 'Ninguna' }}
                        </td>

                        <td class="px-6 py-4 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                            {{ td.fecha_registro || 'N/A' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-else
            class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed bg-[var(--section-soft)] p-8 text-center"
            style="border-color: var(--border)"
        >
            <span class="text-[length:var(--font-3xl)]">🦷</span>

            <h3 class="mt-2 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                No hay dientes asociados
            </h3>

            <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                Este tratamiento general no contiene registros de intervención sobre dientes.
            </p>
        </div>
    </div>
</template>
