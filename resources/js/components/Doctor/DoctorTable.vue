<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Doctor } from '@/types/doctor';

defineProps<{
    doctores: Doctor[];
    tienePermisoUpdate: boolean;
    tienePermisoShow: boolean;
}>();

const emit = defineEmits<{
    edit: [doctor: Doctor];
}>();
</script>

<template>
    <div class="hidden overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 border-[var(--border)] md:block">
        <table class="w-full text-left text-[length:var(--font-sm)] border-collapse">
            <thead class="bg-[var(--text)] text-[length:var(--font-xs)] font-bold uppercase text-[var(--card)]">
                <tr>
                    <th class="px-6 py-4">Nombre</th>
                    <th class="px-6 py-4">CI</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Fecha inicio</th>
                    <th class="px-6 py-4">Experiencia</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-[var(--border)]">
                <tr v-for="doctor in doctores" :key="doctor.codigo_doctor"
                    class="text-[var(--muted)] transition hover:bg-[var(--section-soft)]/50">
                    <td class="px-6 py-4 font-bold text-[var(--title)]">
                        {{ doctor.persona?.nombre ?? 'Sin nombre' }}
                        {{ doctor.persona?.apellido ?? '' }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ doctor.persona_id }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ doctor.persona?.usuarios?.[0]?.email ?? 'Sin email' }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ doctor.fecha_contratacion ?? 'Sin fecha' }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ doctor.experiencia ?? '0' }} año(s)
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        <span v-if="doctor.persona?.usuarios?.[0]?.estado === 'activo'"
                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-black text-emerald-600 border border-emerald-500/20">
                            Activo
                        </span>

                        <span v-else
                            class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-3 py-1 text-xs font-black text-rose-600 border border-rose-500/20">
                            Inactivo
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-3">
                            <button v-if="tienePermisoUpdate" type="button" @click="emit('edit', doctor)"
                                class="rounded-xl border bg-[var(--card)] px-3 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:bg-[var(--primary-soft)] border-[var(--primary)]"
                                title="Editar doctor">
                                Editar
                            </button>

                            <Link v-if="tienePermisoShow" :href="route('doctor.show', doctor.codigo_doctor)"
                                class="rounded-xl border border-red-200 bg-[var(--card)] px-3 py-2 text-[length:var(--font-xs)] font-black text-red-500 transition hover:bg-red-50 dark:border-rose-900/60 dark:hover:bg-rose-950/20"
                                title="Configurar doctor/horarios">
                                Configurar
                            </Link>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
