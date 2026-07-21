<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Paciente } from '@/types/Paciente';

defineProps<{
    pacientes: Paciente[];
    tienePermiso: (permiso: string) => boolean;
}>();

const emit = defineEmits<{
    edit: [paciente: Paciente];
}>();
</script>

<template>
    <!-- Tabla para desktop -->
    <div class="hidden overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 md:block border-[var(--border)]">
        <table class="w-full text-left text-[length:var(--font-sm)]">
            <thead class="bg-[var(--text)] text-[var(--card)]">
                <tr>
                    <th class="px-6 py-4 font-black">Nombre</th>
                    <th class="px-6 py-4 font-black">CI</th>
                    <th class="px-6 py-4 font-black">Email</th>
                    <th class="px-6 py-4 font-black">Contacto de emergencia</th>
                    <th class="px-6 py-4 font-black">Teléfono de emergencia</th>
                    <th class="px-6 py-4 font-black">Estado</th>
                    <th class="px-6 py-4 text-right font-black">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-[var(--border)]">
                <tr v-for="paciente in pacientes" :key="paciente.codigo_paciente"
                    class="text-[var(--muted)] transition hover:bg-[var(--section-soft)]/50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-sm)] font-black text-white shrink-0 shadow-lg shadow-teal-100 dark:shadow-none">
                                {{ paciente.persona?.nombre?.charAt(0).toUpperCase() ?? 'P' }}
                            </div>
                            <div>
                                <div class="font-black text-[var(--title)]">
                                    {{ paciente.persona?.nombre ?? 'Sin nombre' }} {{ paciente.persona?.apellido ?? '' }}
                                </div>
                                <div class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                                    Cód: {{ paciente.codigo_paciente }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ paciente.persona_id }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ paciente.persona?.usuarios?.[0]?.email ?? 'Sin email' }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ paciente.contacto_emergencia ?? 'Sin contacto' }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ paciente.telefono_emergencia ?? 'Sin teléfono' }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        <span v-if="paciente.persona?.usuarios?.[0]?.estado === 'activo'"
                            class="inline-block rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                            Activo
                        </span>
                        <span v-else
                            class="inline-block rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500">
                            Inactivo
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2"
                            v-if="tienePermiso('paciente.update') || tienePermiso('paciente.show')">
                            <button type="button" @click="emit('edit', paciente)"
                                v-if="tienePermiso('paciente.update')"
                                class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] text-lg"
                                title="Editar paciente">
                                ✎
                            </button>

                            <Link :href="route('paciente.show', paciente.codigo_paciente)"
                                v-if="tienePermiso('paciente.show')"
                                class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] text-lg"
                                title="Ver ficha / expediente">
                                ⚙
                            </Link>
                        </div>
                        <div class="flex justify-end gap-2" v-else>
                            <span class="rounded-xl px-4 py-2 text-[length:var(--font-sm)] font-black text-gray-400">
                                Sin permisos
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
