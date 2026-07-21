<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Secretaria } from '@/types/Secretaria';

defineProps<{
    secretarias: Secretaria[];
    tienePermiso: (permiso: string) => boolean;
}>();

const emit = defineEmits<{
    edit: [secretaria: Secretaria];
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
                    <th class="px-6 py-4 font-black">Fecha contratación</th>
                    <th class="px-6 py-4 font-black">Estado</th>
                    <th class="px-6 py-4 text-right font-black">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-[var(--border)]">
                <tr v-for="secretaria in secretarias" :key="secretaria.codigo_secretaria"
                    class="text-[var(--muted)] transition hover:bg-[var(--section-soft)]/50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-sm)] font-black text-white shrink-0 shadow-lg shadow-teal-100 dark:shadow-none">
                                {{ secretaria.persona?.nombre?.charAt(0).toUpperCase() ?? 'S' }}
                            </div>
                            <div>
                                <div class="font-black text-[var(--title)]">
                                    {{ secretaria.persona?.nombre ?? 'Sin nombre' }} {{ secretaria.persona?.apellido ?? '' }}
                                </div>
                                <div class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                                    Cód: {{ secretaria.codigo_secretaria }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ secretaria.persona_id }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ secretaria.persona?.usuarios?.[0]?.email ?? 'Sin email' }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        {{ secretaria.fecha_contratacion ?? 'Sin fecha' }}
                    </td>

                    <td class="px-6 py-4 text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        <span v-if="secretaria.persona?.usuarios?.[0]?.estado === 'activo'"
                            class="inline-block rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                            Activo
                        </span>
                        <span v-else
                            class="inline-block rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500">
                            Inactivo
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            <button v-if="tienePermiso('secretaria.update')" type="button"
                                @click="emit('edit', secretaria)"
                                class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] text-lg"
                                title="Editar secretaria">
                                ✎
                            </button>

                            <Link :href="route('secretaria.show', secretaria.codigo_secretaria)"
                                class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] text-lg"
                                title="Ver perfil / turnos">
                                ⚙
                            </Link>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
