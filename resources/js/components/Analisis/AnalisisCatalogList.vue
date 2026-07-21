<script setup lang="ts">
import type { Analisis } from '@/types/Analisis';

defineProps<{
    analisis: Analisis[];
    tienePermisoUpdate: boolean;
    tienePermisoDestroy: boolean;
}>();

const emit = defineEmits<{
    edit: [item: Analisis];
    restore: [id: number];
    delete: [id: number];
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        <article v-for="item in analisis" :key="item.id"
            class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)]"
            style="border-color: var(--border)">
            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"></div>

            <div class="relative z-10 flex h-full flex-col justify-between">
                <div>
                    <div class="mb-5 flex items-start justify-between">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-2xl)] font-black text-white shadow-lg shadow-teal-100">
                            {{ item.nombre.charAt(0).toUpperCase() }}
                        </div>

                        <span v-if="item.estado === 'ACTIVO'"
                            class="inline-flex items-center gap-1 rounded-full bg-[var(--primary-soft)] px-3.5 py-1.5 text-[length:var(--font-xs)] font-black text-[var(--primary)]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[var(--primary)]"></span>
                            Activo
                        </span>

                        <span v-else
                            class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3.5 py-1.5 text-[length:var(--font-xs)] font-black text-red-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>
                            Inactivo
                        </span>
                    </div>

                    <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                        {{ item.nombre }}
                    </h2>

                    <p class="mt-2 line-clamp-3 text-[length:var(--font-sm)] leading-relaxed text-[var(--muted)]">
                        {{ item.descripcion || 'Sin descripción registrada.' }}
                    </p>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 border-t pt-4"
                    style="border-color: var(--border)">
                    <button type="button" @click="emit('edit', item)" v-if="tienePermisoUpdate"
                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)]">
                        ✎
                    </button>

                    <button v-if="item.estado !== 'ACTIVO' && tienePermisoDestroy" type="button"
                        @click="emit('restore', item.id)"
                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-emerald-500"
                        title="Restaurar tipo de análisis">
                        ↩
                    </button>

                    <button v-else-if="tienePermisoDestroy" type="button"
                        @click="emit('delete', item.id)"
                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500"
                        title="Inactivar tipo de análisis">
                        🗑
                    </button>
                </div>
            </div>
        </article>
    </div>
</template>
