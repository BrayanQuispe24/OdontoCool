<script setup lang="ts">
import { Turno } from '@/types/Turno';

defineProps<{
    turnos: Turno[];
}>();

const emit = defineEmits<{
    'edit': [turno: Turno];
    'delete': [id: number];
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        <article
            v-for="turno in turnos"
            :key="turno.id"
            class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)]"
            style="border-color: var(--border)"
        >
            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"></div>

            <div class="relative z-10">
                <div
                    class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-2xl)] font-black text-white shadow-lg shadow-teal-100"
                >
                    {{ turno.nombre.charAt(0).toUpperCase() }}
                </div>

                <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ turno.nombre }}
                </h2>

                <p class="mt-2 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                    🕒 {{ turno.hora_inicio }} - {{ turno.hora_fin }}
                </p>

                <p class="mt-3 text-[length:var(--font-sm)] leading-6 text-[var(--muted)]">
                    Turno disponible para la administración del sistema OdontoCool.
                </p>

                <div class="mt-6 flex items-center justify-between">
                    <span
                        v-if="turno.estado === 'activo'"
                        class="rounded-full bg-[var(--primary-soft)] px-4 py-2 text-[var(--primary)] font-bold text-[length:var(--font-xs)]"
                    >
                        👍 Activo
                    </span>

                    <span
                        v-else
                        class="rounded-full bg-red-100 px-4 py-2 text-red-600 font-bold text-[length:var(--font-xs)]"
                    >
                        👎 {{ turno.estado }}
                    </span>

                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            @click="emit('edit', turno)"
                            class="text-[length:var(--font-xl)] font-black text-[var(--primary)] transition group-hover:translate-x-1 hover:text-[var(--primary-hover)]"
                            title="Editar turno"
                        >
                            ✎
                        </button>

                        <button
                            type="button"
                            @click="emit('delete', turno.id)"
                            class="text-[length:var(--font-xl)] font-black text-red-500 transition group-hover:translate-x-1 hover:text-red-700"
                            title="Eliminar turno"
                        >
                            🗑
                        </button>
                    </div>
                </div>
            </div>
        </article>
    </div>
</template>
