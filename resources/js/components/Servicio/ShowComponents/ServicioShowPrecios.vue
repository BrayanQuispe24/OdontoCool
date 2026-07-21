<script setup lang="ts">
import { AsignacionPrecio } from '@/types/AsignacionPrecio';
import { Precio } from '@/types/Precio';

defineProps<{
    asignaciones?: AsignacionPrecio[];
    precios: Precio[];
    asignarForm: any;
}>();

const emit = defineEmits<{
    'submit-asignar': [];
    'eliminar-asignacion': [id: number];
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Precios
                </p>

                <h2 class="mt-1 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                    Precios asignados al servicio
                </h2>

                <p class="mt-1 text-[length:var(--font-sm)] text-[var(--muted)]">
                    Historial y asignaciones vigentes de tarifas para este tratamiento.
                </p>
            </div>

            <div class="rounded-2xl bg-[var(--primary-soft)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                {{ asignaciones?.length || 0 }} asignación(es)
            </div>
        </div>

        <!-- Assignment Form -->
        <form
            class="mt-4 flex flex-col gap-4 rounded-2xl bg-[var(--section-soft)] p-4 lg:flex-row lg:items-end"
            @submit.prevent="emit('submit-asignar')"
        >
            <div class="w-full lg:max-w-xs">
                <label class="mb-2 block text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                    Seleccionar Precio
                </label>

                <select
                    v-model="asignarForm.precio_id"
                    required
                    class="h-11 w-full rounded-xl border bg-[var(--card)] px-4 text-[length:var(--font-sm)] font-bold text-[var(--title)] outline-none"
                    style="border-color: var(--border)"
                >
                    <option value="">Seleccione un precio</option>

                    <option
                        v-for="precio in precios"
                        :key="precio.id"
                        :value="precio.id"
                    >
                        {{ precio.monto }} {{ precio.moneda }} ({{ precio.estado }})
                    </option>
                </select>

                <p
                    v-if="asignarForm.errors.precio_id"
                    class="mt-1 text-[length:var(--font-xs)] font-bold text-red-500"
                >
                    {{ asignarForm.errors.precio_id }}
                </p>
            </div>

            <div class="w-full lg:max-w-xs">
                <label class="mb-2 block text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                    Fecha de Inicio
                </label>

                <input
                    v-model="asignarForm.fecha_inicio"
                    type="date"
                    required
                    class="h-11 w-full rounded-xl border bg-[var(--card)] px-4 text-[length:var(--font-sm)] font-bold text-[var(--title)] outline-none"
                    style="border-color: var(--border)"
                />

                <p
                    v-if="asignarForm.errors.fecha_inicio"
                    class="mt-1 text-[length:var(--font-xs)] font-bold text-red-500"
                >
                    {{ asignarForm.errors.fecha_inicio }}
                </p>
            </div>

            <div class="w-full lg:max-w-xs">
                <label class="mb-2 block text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                    Fecha de Fin
                </label>

                <input
                    v-model="asignarForm.fecha_fin"
                    type="date"
                    required
                    class="h-11 w-full rounded-xl border bg-[var(--card)] px-4 text-[length:var(--font-sm)] font-bold text-[var(--title)] outline-none"
                    style="border-color: var(--border)"
                />

                <p
                    v-if="asignarForm.errors.fecha_fin"
                    class="mt-1 text-[length:var(--font-xs)] font-bold text-red-500"
                >
                    {{ asignarForm.errors.fecha_fin }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="asignarForm.processing"
                class="h-11 rounded-xl bg-[var(--primary)] px-5 text-[length:var(--font-sm)] font-black text-white transition hover:bg-[var(--primary-hover)] disabled:opacity-60 lg:mt-0"
            >
                {{ asignarForm.processing ? 'Asignando...' : 'Asignar Precio' }}
            </button>
        </form>

        <!-- Assignments Table -->
        <div
            v-if="asignaciones && asignaciones.length"
            class="mt-6"
        >
            <div
                class="overflow-hidden rounded-2xl border"
                style="border-color: var(--border)"
            >
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-left">
                        <thead class="bg-[var(--primary-soft)] text-[length:var(--font-sm)] font-black text-[var(--title)]">
                            <tr>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Monto</th>
                                <th class="px-6 py-4">Vigencia</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y text-[length:var(--font-sm)] text-[var(--muted)]" style="border-color: var(--border)">
                            <tr
                                v-for="asig in asignaciones"
                                :key="asig.id"
                                class="transition hover:bg-[var(--section-soft)]"
                            >
                                <td class="px-6 py-4 font-bold text-[var(--title)]">
                                    #{{ asig.id }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-[length:var(--font-base)] font-black text-[var(--title)]">
                                        {{ asig.precio?.monto }} {{ asig.precio?.moneda }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                            Desde
                                        </span>

                                        <span class="font-semibold text-[var(--title)]">
                                            {{ asig.fecha_inicio }}
                                        </span>

                                        <span class="mt-1 text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                            Hasta
                                        </span>

                                        <span class="font-semibold text-[var(--title)]">
                                            {{ asig.fecha_fin }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-[length:var(--font-xs)] font-black"
                                        :class="asig.estado === 'ACTIVO' || asig.estado === 'activo'
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-700'"
                                    >
                                        {{ asig.estado }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <button
                                        type="button"
                                        @click="emit('eliminar-asignacion', asig.id)"
                                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500"
                                        title="Eliminar Asignación"
                                    >
                                        🗑
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div
            v-else
            class="mt-6 rounded-2xl border border-dashed bg-[var(--section-soft)] p-8 text-center text-[var(--muted)]"
            style="border-color: var(--border)"
        >
            Este servicio no tiene precios asignados actualmente. Selecciona un precio del selector de arriba para comenzar.
        </div>
    </div>
</template>
