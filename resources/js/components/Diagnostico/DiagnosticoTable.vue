<script setup lang="ts">
import type { Diagnostico } from '@/types/Diagnostico';

defineProps<{
    diagnosticos: Diagnostico[];
}>();

const emit = defineEmits<{
    edit: [diagnostico: Diagnostico];
    delete: [id: number];
}>();

const getSeverityBadgeClass = (gravedad: string) => {
    switch (gravedad.toUpperCase()) {
        case 'LEVE':
            return 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20';
        case 'MODERADA':
            return 'bg-amber-500/10 text-amber-600 border border-amber-500/20';
        case 'SEVERA':
            return 'bg-rose-500/10 text-rose-600 border border-rose-500/20';
        default:
            return 'bg-[var(--section-soft)] text-[var(--muted)] border border-[var(--border)]';
    }
};
</script>

<template>
    <div class="overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 border-[var(--border)]">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-[length:var(--font-sm)]">
                <thead class="bg-[var(--text)] text-[length:var(--font-xs)] font-bold uppercase text-[var(--card)]">
                    <tr>
                        <th class="px-6 py-4">Paciente</th>
                        <th class="px-6 py-4">Tipo & Síntomas</th>
                        <th class="px-6 py-4">Gravedad</th>
                        <th class="px-6 py-4">Detalles Inspección</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[var(--border)] border-t border-[var(--border)]">
                    <tr v-for="item in diagnosticos" :key="item.id" class="transition hover:bg-[var(--section-soft)]/50">
                        <td class="px-6 py-4">
                            <div class="font-black text-[var(--title)]">
                                {{ item.cita?.paciente?.persona?.nombre }} {{ item.cita?.paciente?.persona?.apellido }}
                            </div>

                            <div class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                CI: {{ item.cita?.paciente_ci }} | Cita #{{ item.cita_id }}
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-bold text-[var(--title)]">
                                {{ item.tipo_diagnostico }}
                            </div>

                            <div class="line-clamp-1 max-w-xs text-[length:var(--font-xs)] text-[var(--muted)]" :title="item.sintomas">
                                {{ item.sintomas }}
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-block rounded-xl px-3 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider"
                                :class="getSeverityBadgeClass(item.gravedad)">
                                {{ item.gravedad }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex max-w-xs flex-wrap gap-1">
                                <span v-for="det in item.detalle_diagnostico" :key="det.id"
                                    class="inline-block rounded-lg border bg-[var(--section-soft)] px-2 py-0.5 text-[length:var(--font-xs)] font-extrabold text-[var(--title)] border-[var(--border)]"
                                    :title="det.observacion || ''">
                                    🦷 #{{ det.diente?.numero }} - {{ det.zona_bucal }}
                                </span>

                                <span v-if="!item.detalle_diagnostico?.length"
                                    class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                    Ninguno
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="emit('edit', item)"
                                    class="rounded-xl border bg-[var(--card)] px-3 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:bg-[var(--primary-soft)] border-[var(--primary)]">
                                    Editar
                                </button>

                                <button type="button" @click="emit('delete', item.id)"
                                    class="rounded-xl border border-red-200 bg-[var(--card)] px-3 py-2 text-[length:var(--font-xs)] font-black text-red-500 transition hover:bg-red-50 dark:border-rose-900/60 dark:hover:bg-rose-950/20">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
