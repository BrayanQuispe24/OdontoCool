<script setup lang="ts">
import { ServicioWithAsignaciones } from '@/types/Servicio';

defineProps<{
    servicio: ServicioWithAsignaciones;
}>();
</script>

<template>
    <div class="grid gap-5 md:grid-cols-1">
        <div
            class="grid grid-cols-1 gap-6 rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300 sm:grid-cols-4"
            style="border-color: var(--border)"
        >
            <div>
                <p class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                    ID
                </p>

                <p class="mt-2 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    #{{ servicio.id }}
                </p>
            </div>

            <div>
                <p class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                    Tipo / Especialidad
                </p>

                <p class="mt-2 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ servicio.tipo }}
                </p>
            </div>

            <div>
                <p class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                    Estado
                </p>

                <span
                    class="mt-3 inline-flex rounded-full px-4 py-2 text-[length:var(--font-xs)] font-black"
                    :class="servicio.estado === 'ACTIVO' || servicio.estado === 'activo'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'"
                >
                    {{ servicio.estado }}
                </span>
            </div>

            <div>
                <p class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                    Descripción
                </p>

                <p class="mt-2 text-[length:var(--font-base)] leading-7 text-[var(--title)]">
                    {{ servicio.descripcion || 'Sin descripción registrada.' }}
                </p>
            </div>
        </div>
    </div>
</template>
