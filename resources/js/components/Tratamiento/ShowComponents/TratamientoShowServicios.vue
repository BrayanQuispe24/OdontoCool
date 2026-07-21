<script setup lang="ts">
import { ServicioPrestado } from '@/types/ServicioPrestado';

defineProps<{
    serviciosPrestados?: ServicioPrestado[];
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
            Servicios Clínicos
        </p>
        <h2 class="mt-1 text-[length:var(--font-2xl)] font-black text-[var(--title)] mb-4">
            Servicios Prestados en este Tratamiento
        </h2>

        <div v-if="serviciosPrestados?.length" class="space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr
                            class="border-b bg-[var(--section-soft)] text-[length:var(--font-xs)] font-black uppercase tracking-wider text-[var(--muted)]"
                            style="border-color: var(--border)"
                        >
                            <th class="px-6 py-4 rounded-l-2xl">Servicio</th>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4 text-center">Cantidad</th>
                            <th class="px-6 py-4 text-right">Precio Unitario</th>
                            <th class="px-6 py-4 text-right">Descuento</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                            <th class="px-6 py-4 rounded-r-2xl">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-[length:var(--font-sm)]" style="border-color: var(--border)">
                        <tr
                            v-for="sp in serviciosPrestados"
                            :key="sp.id"
                            class="transition hover:bg-[var(--section-soft)]"
                        >
                            <td class="px-6 py-4 font-bold text-[var(--title)]">
                                <div>
                                    <p class="font-black text-[var(--title)]">{{ sp.servicio?.nombre }}</p>
                                    <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">
                                        Tipo: {{ sp.servicio?.tipo || 'General' }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[var(--muted)]">
                                {{ sp.fecha_servicio }}
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-[var(--title)]">
                                {{ sp.cantidad }}
                            </td>
                            <td class="px-6 py-4 text-right text-[var(--muted)]">
                                Bs. {{ parseFloat(sp.precio as any).toFixed(2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-red-500 font-bold">
                                - Bs. {{ parseFloat((sp.descuento || 0) as any).toFixed(2) }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-[var(--primary)]">
                                Bs. {{ parseFloat(sp.subtotal as any).toFixed(2) }}
                            </td>
                            <td class="px-6 py-4 text-[var(--muted)] italic max-w-xs leading-relaxed">
                                {{ sp.observacion || 'Ninguna' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total Servicios Card -->
            <div class="flex justify-end pt-2">
                <div
                    class="rounded-2xl border bg-[var(--primary-soft)] px-6 py-3.5 text-right transition-colors duration-300"
                    style="border-color: var(--border)"
                >
                    <span class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider block">Total Servicios Prestados</span>
                    <span class="text-[length:var(--font-xl)] font-black text-[var(--primary)]">
                        Bs. {{ 
                            serviciosPrestados.reduce((total, sp) => {
                                return total + parseFloat(sp.subtotal as any);
                            }, 0).toFixed(2)
                        }}
                    </span>
                </div>
            </div>
        </div>

        <div
            v-else
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed bg-[var(--section-soft)] p-6 text-center"
            style="border-color: var(--border)"
        >
            <span class="text-[length:var(--font-2xl)]">🛠️</span>
            <p class="mt-2 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">No se han registrado servicios prestados para este tratamiento.</p>
        </div>
    </div>
</template>
