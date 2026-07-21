<script setup lang="ts">
import { SolicitudAnalisis } from '@/types/SolicitudAnalisi';
import { Link } from '@inertiajs/vue3';

defineProps<{
    solicitudes?: SolicitudAnalisis[];
    formatPath: (path?: string) => string;
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
            Laboratorio
        </p>

        <h2 class="mt-1 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
            Solicitudes de Análisis Clínicos
        </h2>

        <div
            v-if="solicitudes?.length"
            class="mt-5 grid gap-4 md:grid-cols-2"
        >
            <div
                v-for="solicitud in solicitudes"
                :key="solicitud.id"
                class="space-y-3 rounded-2xl border bg-[var(--section-soft)] p-5"
                style="border-color: var(--border)"
            >
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Tipo de Análisis
                        </p>

                        <Link
                            :href="route('analisis.index', { tab: 'solicitudes', search: String(solicitud.id) })"
                            class="block text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:text-[var(--primary)] hover:underline"
                        >
                            {{ solicitud.analisis?.nombre || 'Análisis general' }} 🔍
                        </Link>
                    </div>

                    <span
                        class="rounded-full px-2.5 py-1 text-[length:var(--font-xs)] font-black"
                        :class="solicitud.estado === 'PENDIENTE'
                            ? 'bg-amber-100 text-amber-700'
                            : 'bg-green-100 text-green-700'"
                    >
                        {{ solicitud.estado }}
                    </span>
                </div>

                <div class="space-y-1 text-[length:var(--font-xs)] text-[var(--muted)]">
                    <p>
                        <span class="font-bold text-[var(--title)]">Fecha Solicitud:</span>
                        {{ solicitud.fecha_solicitud }}
                    </p>

                    <p v-if="solicitud.observacion">
                        <span class="font-bold text-[var(--title)]">Observación:</span>
                        {{ solicitud.observacion }}
                    </p>
                </div>

                <!-- Resultado files details if uploaded -->
                <div
                    v-if="solicitud.resultado_analisis"
                    class="mt-3 space-y-2 border-t pt-3"
                    style="border-color: var(--border)"
                >
                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Resultado del Análisis
                    </p>

                    <div class="flex items-center justify-between gap-3 text-[length:var(--font-xs)]">
                        <div>
                            <p class="font-bold text-[var(--title)]">
                                Fecha Carga: {{ solicitud.resultado_analisis.fecha_resultado }}
                            </p>

                            <p class="text-[length:var(--font-xs)] italic text-[var(--muted)]">
                                "{{ solicitud.resultado_analisis.observaciones || 'Sin observaciones.' }}"
                            </p>
                        </div>
                        <a v-if="solicitud.resultado_analisis.archivo_resultado"
                            :href="formatPath(solicitud.resultado_analisis.archivo_resultado)"
                            target="_blank"
                            :download="(solicitud.resultado_analisis.archivo_resultado).split('/').pop()"
                            class="rounded-lg bg-[var(--primary)] px-3 py-1.5 font-bold text-[var(--card)] text-[11px] hover:opacity-90 transition">
                            Descargar Archivo 📥
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else
            class="mt-4 flex flex-col items-center justify-center rounded-2xl border border-dashed bg-[var(--section-soft)] p-6 text-center"
            style="border-color: var(--border)"
        >
            <span class="text-[length:var(--font-2xl)]">🔬</span>

            <p class="mt-2 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                No se han solicitado análisis clínicos para este tratamiento.
            </p>
        </div>
    </div>
</template>
