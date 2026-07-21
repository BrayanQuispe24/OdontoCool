<script setup lang="ts">
defineProps<{
    resumen: {
        total_citas: number;
        total_servicios: number;
        total_usuarios: number;
        total_solicitudes_analisis: number;
    };
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
            <p class="text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                Citas realizadas
            </p>
            <h3 class="mt-2 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                {{ resumen.total_citas }}
            </h3>
            <p class="mt-1 text-[length:var(--font-xs)] text-[var(--muted)]">
                En el rango seleccionado
            </p>
        </article>

        <article class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
            <p class="text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                Servicios prestados
            </p>
            <h3 class="mt-2 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                {{ resumen.total_servicios }}
            </h3>
            <p class="mt-1 text-[length:var(--font-xs)] text-[var(--muted)]">
                Servicios activos realizados
            </p>
        </article>

        <article class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
            <p class="text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                Usuarios registrados
            </p>
            <h3 class="mt-2 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                {{ resumen.total_usuarios }}
            </h3>
            <p class="mt-1 text-[length:var(--font-xs)] text-[var(--muted)]">
                Nuevos usuarios
            </p>
        </article>

        <article class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
            <p class="text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                Solicitudes de análisis
            </p>
            <h3 class="mt-2 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                {{ resumen.total_solicitudes_analisis }}
            </h3>
            <p class="mt-1 text-[length:var(--font-xs)] text-[var(--muted)]">
                Solicitudes activas
            </p>
        </article>
    </div>
</template>
