<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    objetivo: string;
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Ficha de Tratamiento
                </p>

                <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                    {{ objetivo }}
                </h1>

                <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                    Consulta la información de planificación clínica, dientes asociados y recetas prescritas.
                </p>
            </div>

            <Link
                :href="route('tratamiento.index')"
                class="rounded-2xl bg-[var(--text)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90"
            >
                Volver
            </Link>
        </div>
    </div>
</template>
