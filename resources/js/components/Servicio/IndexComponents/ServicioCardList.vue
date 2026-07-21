<script setup lang="ts">
import { Servicio } from '@/types/Servicio';

defineProps<{
    servicios: Servicio[];
    canUpdate: boolean;
    canDestroy: boolean;
}>();

const emit = defineEmits<{
    'edit': [servicio: Servicio];
    'delete': [servicioId: number];
    'restore': [servicioId: number];
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        <article
            v-for="servicio in servicios"
            :key="servicio.id"
            class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)]"
            style="border-color: var(--border)"
        >
            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"></div>

            <div class="relative z-10">
                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-xl)] font-black text-white shadow-lg shadow-teal-100">
                    {{ servicio.nombre.substring(0, 2).toUpperCase() }}
                </div>

                <span class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--primary)]">
                    {{ servicio.tipo }}
                </span>

                <h2 class="mt-1 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ servicio.nombre }}
                </h2>

                <p class="mt-2 line-clamp-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                    {{ servicio.descripcion || 'Sin descripción registrada.' }}
                </p>

                <!-- Precio Actual -->
                <div class="mt-4 flex items-center justify-between border-t pt-4" style="border-color: var(--border)">
                    <span class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Precio Actual
                    </span>

                    <span class="text-[length:var(--font-lg)] font-black text-[var(--title)]">
                        {{
                            servicio.asignaciones_precio?.find(a => a.estado === 'ACTIVO')?.precio?.monto
                                ? `${servicio.asignaciones_precio!.find(a => a.estado === 'ACTIVO')!.precio!.monto} ${servicio.asignaciones_precio!.find(a => a.estado === 'ACTIVO')!.precio!.moneda}`
                                : 'Sin asignar'
                        }}
                    </span>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <!-- Estado -->
                    <span
                        v-if="servicio.estado === 'ACTIVO' || servicio.estado === 'activo'"
                        class="inline-flex items-center gap-1.5 rounded-full bg-[var(--primary-soft)] px-4 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)]"
                    >
                        <span class="h-2 w-2 rounded-full bg-[var(--primary)]"></span>
                        Activo
                    </span>

                    <span
                        v-else
                        class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-4 py-2 text-[length:var(--font-xs)] font-black text-red-600"
                    >
                        <span class="h-2 w-2 rounded-full bg-red-600"></span>
                        Inactivo
                    </span>

                    <!-- Actions -->
                    <div class="flex items-center gap-3" v-if="canUpdate || canDestroy">
                        <button 
                            type="button"
                            @click="emit('edit', servicio)"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)]"
                            title="Editar Servicio"
                        >
                            ✎
                        </button>

                        <!-- Inactivar (If Active) -->
                        <button 
                            v-if="servicio.estado === 'ACTIVO' || servicio.estado === 'activo'"
                            type="button"
                            @click="emit('delete', servicio.id)"
                            class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-50 hover:text-red-500"
                            title="Inactivar Servicio"
                        >
                            🗑
                        </button>

                        <!-- Restaurar (If Inactive) -->
                        <button
                            v-else
                            type="button"
                            @click="emit('restore', servicio.id)"
                            class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-50 hover:text-emerald-500"
                            title="Restaurar Servicio"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-5 w-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </article>
    </div>
</template>
