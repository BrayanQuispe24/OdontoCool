<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { HistorialClinico } from '@/types/HistorialClinico';

defineProps<{
    historiales: HistorialClinico[];
}>();

const emit = defineEmits<{
    edit: [item: HistorialClinico];
    delete: [codigo: number];
}>();

const getIniciales = (nombre?: string, apellido?: string) => {
    const n = nombre?.charAt(0) ?? '';
    const a = apellido?.charAt(0) ?? '';
    return `${n}${a}`.toUpperCase() || 'HC';
};
</script>

<template>
    <!-- Tabla para desktop -->
    <div class="hidden overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 lg:block border-[var(--border)]">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-[length:var(--font-sm)]">
                <thead class="bg-[var(--text)] text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--card)]">
                    <tr>
                        <th class="px-6 py-4 font-black">Paciente</th>
                        <th class="px-6 py-4 font-black">Apertura</th>
                        <th class="px-6 py-4 font-black">Alergias</th>
                        <th class="px-6 py-4 font-black">Patologías Crónicas</th>
                        <th class="px-6 py-4 font-black">Estado</th>
                        <th class="px-6 py-4 text-right font-black">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in historiales" :key="item.codigo_historial"
                        class="transition hover:bg-[var(--section-soft)]/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-sm)] font-black text-white shrink-0 shadow-lg shadow-teal-100 dark:shadow-none">
                                    {{ getIniciales(item.paciente?.persona?.nombre, item.paciente?.persona?.apellido) }}
                                </div>

                                <div>
                                    <div class="font-black text-[var(--title)]">
                                        {{ item.paciente?.persona?.nombre }} {{ item.paciente?.persona?.apellido }}
                                    </div>

                                    <div class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                                        Código/CI: {{ item.paciente_ci }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-semibold text-[var(--title)]">
                                {{ item.fecha_apertura }}
                            </div>

                            <div class="max-w-xs truncate text-[length:var(--font-xs)] text-[var(--muted)]"
                                :title="item.motivo_apertura">
                                {{ item.motivo_apertura }}
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <span v-if="item.alergias"
                                class="inline-block max-w-[150px] truncate rounded-xl bg-red-50 dark:bg-red-950/30 px-2.5 py-1 text-[length:var(--font-xs)] font-bold text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/30"
                                :title="item.alergias">
                                ⚠️ {{ item.alergias }}
                            </span>

                            <span v-else class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                Ninguna
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span v-if="item.enfermedades_base"
                                class="inline-block max-w-[150px] truncate rounded-xl bg-amber-50 dark:bg-amber-950/30 px-2.5 py-1 text-[length:var(--font-xs)] font-bold text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30"
                                :title="item.enfermedades_base">
                                {{ item.enfermedades_base }}
                            </span>

                            <span v-else class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                Ninguna
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-block rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider"
                                :class="item.estado === 'ACTIVO'
                                    ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20'
                                    : 'bg-[var(--section-soft)] text-[var(--muted)] border border-[var(--border)]'">
                                {{ item.estado }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <Link :href="route('historial_clinico.show', item.codigo_historial)"
                                    class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] text-lg"
                                    title="Ver Ficha">
                                    👁
                                </Link>

                                <button type="button" @click="emit('edit', item)"
                                    class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] text-lg"
                                    title="Editar">
                                    ✏️
                                </button>

                                <button v-if="item.estado === 'ACTIVO'" type="button"
                                    @click="emit('delete', item.codigo_historial)"
                                    class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500 text-lg"
                                    title="Inactivar">
                                    ✕
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
