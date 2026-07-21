<script setup lang="ts">
import type { HistorialClinico } from '@/types/HistorialClinico';

defineProps<{
    historial: HistorialClinico;
}>();
</script>

<template>
    <!-- Patient Card Details -->
    <div class="space-y-6 lg:col-span-1 print:col-span-1">
        <div class="print-card space-y-4 rounded-3xl border bg-[var(--card)] p-6 shadow-[0_10px_30px_rgba(0,169,157,0.04)] transition-colors duration-300 border-[var(--border)]">
            <div class="flex items-center gap-3 border-b pb-3 border-[var(--border)]">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-2xl">
                    👨
                </div>

                <div>
                    <h3 class="text-[length:var(--font-base)] font-black text-[var(--title)]">
                        Datos del Paciente
                    </h3>

                    <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                        Información de afiliación
                    </p>
                </div>
            </div>

            <div class="space-y-3 text-[length:var(--font-xs)]">
                <div>
                    <span class="block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                        Nombre Completo
                    </span>

                    <p class="font-bold text-[var(--title)]">
                        {{ historial.paciente?.persona?.nombre }} {{ historial.paciente?.persona?.apellido }}
                    </p>
                </div>

                <div>
                    <span class="block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                        Cédula de Identidad (CI)
                    </span>

                    <p class="font-semibold text-[var(--title)]">
                        {{ historial.paciente_ci }}
                    </p>
                </div>

                <div>
                    <span class="block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                        Contacto de Emergencia
                    </span>

                    <p class="font-semibold text-[var(--title)]">
                        {{ historial.paciente?.contacto_emergencia || 'No registrado' }}
                    </p>
                </div>

                <div>
                    <span class="block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                        Teléfono de Emergencia
                    </span>

                    <p class="font-semibold text-[var(--title)]">
                        {{ historial.paciente?.telefono_emergencia || 'No registrado' }}
                    </p>
                </div>

                <div>
                    <span class="block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                        Estado de la Ficha
                    </span>

                    <span class="mt-1 inline-block rounded-xl px-2.5 py-0.5 text-[length:var(--font-xs)] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 border border-emerald-500/20"
                        v-if="historial.estado === 'ACTIVO'">
                        {{ historial.estado }}
                    </span>
                    <span class="mt-1 inline-block rounded-xl px-2.5 py-0.5 text-[length:var(--font-xs)] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500"
                        v-else>
                        {{ historial.estado }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Medical conditions panel -->
        <div class="print-card space-y-4 rounded-3xl border bg-[var(--card)] p-6 shadow-[0_10px_30px_rgba(0,169,157,0.04)] transition-colors duration-300 border-[var(--border)]">
            <h3 class="border-b pb-2 text-[length:var(--font-base)] font-black text-[var(--title)] border-[var(--border)]">
                Condición de Salud Base
            </h3>

            <div class="space-y-3 text-[length:var(--font-xs)]">
                <div>
                    <span class="block text-[length:var(--font-xs)] font-bold uppercase text-red-500">
                        Alergias médicas y alimentarias
                    </span>

                    <p class="mt-1 rounded-xl border border-red-100 dark:border-red-950 bg-red-50/50 dark:bg-red-950/20 px-3 py-2 font-semibold leading-relaxed text-red-700 dark:text-red-400">
                        {{ historial.alergias || 'Ninguna alergia registrada.' }}
                    </p>
                </div>

                <div>
                    <span class="block text-[length:var(--font-xs)] font-bold uppercase text-amber-600 dark:text-amber-500">
                        Patologías Crónicas / Base
                    </span>

                    <p class="mt-1 rounded-xl border border-amber-100 dark:border-amber-950 bg-amber-50/50 dark:bg-amber-950/20 px-3 py-2 font-semibold leading-relaxed text-amber-700 dark:text-amber-400">
                        {{ historial.enfermedades_base || 'Sin enfermedades de base.' }}
                    </p>
                </div>

                <div>
                    <span class="block text-[length:var(--font-xs)] font-bold uppercase text-[var(--muted)]">
                        Antecedentes Médicos Generales
                    </span>

                    <p class="mt-1 rounded-xl border bg-[var(--section-soft)] px-3 py-2 font-medium leading-relaxed text-[var(--muted)] border-[var(--border)]">
                        {{ historial.antecedentes_medicos || 'No registra antecedentes médicos relevantes.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
