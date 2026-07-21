<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { HistorialClinico } from '@/types/HistorialClinico';

defineProps<{
    historial: HistorialClinico;
    tieneRolPaciente: boolean;
}>();

const emit = defineEmits<{
    'export-pdf': [];
}>();
</script>

<template>
    <!-- Back Button and Action Headers -->
    <div class="no-print flex flex-col gap-4 border-b pb-5 sm:flex-row sm:items-center sm:justify-between border-[var(--border)]">
        <div class="flex items-center gap-3">
            <Link :href="route('historial_clinico.index')" v-if="!tieneRolPaciente"
                class="rounded-xl border bg-[var(--card)] px-4 py-2.5 text-[length:var(--font-xs)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)] border-[var(--border)]">
                ← Volver al Listado
            </Link>

            <div>
                <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    Expediente: {{ historial.paciente?.persona?.nombre }} {{ historial.paciente?.persona?.apellido }}
                </h2>

                <p class="text-[length:var(--font-xs)] text-[var(--muted)] mt-0.5">
                    Código Historial Clínico:
                    <strong class="text-[var(--primary)]">
                        #{{ historial.codigo_historial }}
                    </strong>
                </p>
            </div>
        </div>

        <button type="button" @click="emit('export-pdf')"
            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[var(--text)] px-5 py-3 text-[length:var(--font-xs)] font-black text-[var(--card)] shadow-sm transition hover:opacity-90 active:scale-95 duration-100">
            📄 Exportar PDF
        </button>
    </div>
</template>
