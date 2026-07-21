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
    <!-- Tarjetas para móvil/tablet -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:hidden">
        <article v-for="item in historiales" :key="item.codigo_historial"
            class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)] border-[var(--border)]">
            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"></div>

            <div class="relative z-10 flex h-full flex-col justify-between">
                <div>
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-xl)] font-black text-white shadow-lg shadow-teal-100 shrink-0">
                        {{ getIniciales(item.paciente?.persona?.nombre, item.paciente?.persona?.apellido) }}
                    </div>

                    <span class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--primary)]">
                        Apertura: {{ item.fecha_apertura }}
                    </span>

                    <h2 class="mt-1 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                        {{ item.paciente?.persona?.nombre }} {{ item.paciente?.persona?.apellido }}
                    </h2>

                    <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] mb-3">
                        Código/CI: {{ item.paciente_ci }}
                    </p>

                    <p class="mt-2 line-clamp-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                        <span class="font-bold text-[var(--title)]">Motivo:</span> {{ item.motivo_apertura }}
                    </p>

                    <div class="mt-4 space-y-2">
                        <!-- Alergias -->
                        <div class="flex items-center gap-2">
                            <span class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] shrink-0">Alergias:</span>
                            <span v-if="item.alergias"
                                class="inline-block max-w-[200px] truncate rounded-xl bg-red-50 dark:bg-red-950/30 px-2.5 py-1 text-[length:var(--font-xs)] font-bold text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/30"
                                :title="item.alergias">
                                ⚠️ {{ item.alergias }}
                            </span>
                            <span v-else class="text-[length:var(--font-xs)] text-[var(--muted)] italic">
                                Ninguna
                            </span>
                        </div>

                        <!-- Enfermedades Base -->
                        <div class="flex items-center gap-2">
                            <span class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] shrink-0">Patologías:</span>
                            <span v-if="item.enfermedades_base"
                                class="inline-block max-w-[200px] truncate rounded-xl bg-amber-50 dark:bg-amber-950/30 px-2.5 py-1 text-[length:var(--font-xs)] font-bold text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30"
                                :title="item.enfermedades_base">
                                {{ item.enfermedades_base }}
                            </span>
                            <span v-else class="text-[length:var(--font-xs)] text-[var(--muted)] italic">
                                Ninguna
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t pt-4 border-[var(--border)]">
                    <span class="inline-block rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider"
                        :class="item.estado === 'ACTIVO'
                            ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20'
                            : 'bg-[var(--section-soft)] text-[var(--muted)] border border-[var(--border)]'">
                        {{ item.estado }}
                    </span>

                    <div class="flex items-center gap-2">
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
                </div>
            </div>
        </article>
    </div>
</template>
