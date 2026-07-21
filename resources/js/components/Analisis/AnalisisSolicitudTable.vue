<script setup lang="ts">
defineProps<{
    solicitudes: any[];
    tieneRolDoctorOrAdmin: boolean;
    tienePermisoUpdate: boolean;
    tienePermisoDestroy: boolean;
}>();

const emit = defineEmits<{
    edit: [item: any];
    delete: [id: number];
    registrarResultado: [item: any];
    editResultado: [item: any];
}>();
</script>

<template>
    <div class="overflow-x-auto rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <table class="w-full border-collapse text-left text-[length:var(--font-sm)]">
            <thead>
                <tr class="border-b bg-[var(--section-soft)] text-[length:var(--font-xs)] font-black uppercase text-[var(--muted)]"
                    style="border-color: var(--border)">
                    <th class="rounded-l-2xl px-6 py-4">ID</th>
                    <th class="px-6 py-4">Fecha</th>
                    <th class="px-6 py-4">Análisis</th>
                    <th class="px-6 py-4">Tratamiento Asociado</th>
                    <th class="px-6 py-4">Doctor Solicitante</th>
                    <th class="px-6 py-4">Motivo / Indicaciones</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4">Resultado</th>
                    <th class="rounded-r-2xl px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y" style="--tw-divide-opacity: 1; border-color: var(--border)">
                <tr v-for="item in solicitudes" :key="item.id"
                    class="transition hover:bg-[var(--section-soft)]">
                    <td class="px-6 py-4 font-black text-[var(--title)]">
                        #{{ item.id }}
                    </td>

                    <td class="whitespace-nowrap px-6 py-4 text-[var(--muted)]">
                        {{ item.fecha_solicitud }}
                    </td>

                    <td class="px-6 py-4 font-bold text-[var(--title)]">
                        {{ item.analisis?.nombre || 'Análisis general' }}
                    </td>

                    <td class="max-w-xs truncate px-6 py-4 text-[var(--muted)]">
                        #{{ item.tratamiento?.id }} -
                        {{ item.tratamiento?.objetivo_tratamiento }}
                    </td>

                    <td class="px-6 py-4 font-semibold text-[var(--title)]">
                        {{ item.doctor ? `${item.doctor.persona?.nombre} ${item.doctor.persona?.apellido}` : '-' }}
                    </td>

                    <td class="max-w-xs truncate px-6 py-4 italic text-[var(--muted)]" :title="item.motivo">
                        {{ item.motivo || '-' }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="inline-block rounded-full px-2.5 py-1 text-[length:var(--font-xs)] font-black"
                            :class="item.estado === 'PENDIENTE'
                                ? 'bg-amber-100 text-amber-700'
                                : item.estado === 'COMPLETADO'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-slate-100 text-slate-600'
                            ">
                            {{ item.estado }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div v-if="item.resultado_analisis">
                            <button type="button" @click="emit('editResultado', item)"
                                :disabled="!tieneRolDoctorOrAdmin"
                                class="text-[length:var(--font-xs)] font-black text-[var(--primary)] hover:underline">
                                📄 Ver / Editar
                            </button>
                        </div>

                        <div v-else>
                            <button type="button" @click="emit('registrarResultado', item)"
                                class="rounded-lg border bg-[var(--primary-soft)] px-2.5 py-1 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:bg-[var(--primary)] hover:text-white"
                                style="border-color: var(--border)">
                                + Cargar
                            </button>
                        </div>
                    </td>

                    <td class="space-x-1 whitespace-nowrap px-6 py-4 text-right">
                        <button type="button" @click="emit('edit', item)"
                            class="rounded-lg p-1.5 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--title)]"
                            v-if="tienePermisoUpdate">
                            ✏️
                        </button>

                        <button type="button" @click="emit('delete', item.id)"
                            class="rounded-lg p-1.5 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500"
                            v-if="tienePermisoDestroy">
                            🗑️
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
