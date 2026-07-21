<script setup lang="ts">
import type { Especialidad } from '@/types/Especialidad';

defineProps<{
    especialidades: Especialidad[];
}>();

const emit = defineEmits<{
    edit: [especialidad: Especialidad];
    delete: [id: number];
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        <article v-for="especialidad in especialidades" :key="especialidad.id"
            class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)] border-[var(--border)]">
            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"></div>

            <div class="relative z-10">
                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-2xl)] font-black text-white shadow-lg shadow-teal-100">
                    {{ especialidad.nombre.charAt(0).toUpperCase() }}
                </div>

                <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ especialidad.nombre }}
                </h2>

                <p class="mt-3 text-[length:var(--font-sm)] leading-6 text-[var(--muted)] min-h-[48px]">
                    {{ especialidad.descripcion || 'Especialidad disponible para la administración del sistema OdontoCool.' }}
                </p>

                <div class="mt-6 flex items-center justify-between border-t pt-4 border-[var(--border)]">
                    <span v-if="especialidad.estado === 'activo'"
                        class="rounded-full bg-emerald-500/10 px-4 py-2 text-[length:var(--font-xs)] font-black text-emerald-600 border border-emerald-500/20 uppercase tracking-wider">
                        Activo
                    </span>

                    <span v-else-if="especialidad.estado === 'suspendido'"
                        class="rounded-full bg-amber-500/10 px-4 py-2 text-[length:var(--font-xs)] font-black text-amber-600 border border-amber-500/20 uppercase tracking-wider">
                        Suspendido
                    </span>

                    <span v-else
                        class="rounded-full bg-rose-500/10 px-4 py-2 text-[length:var(--font-xs)] font-black text-rose-600 border border-rose-500/20 uppercase tracking-wider">
                        Inactivo
                    </span>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="emit('edit', especialidad)"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)]"
                            title="Editar especialidad">
                            ✎
                        </button>

                        <button type="button" @click="emit('delete', especialidad.id)"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500"
                            title="Eliminar especialidad">
                            🗑
                        </button>
                    </div>
                </div>
            </div>
        </article>
    </div>
</template>
