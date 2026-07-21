<script setup lang="ts">
</script>

<template>
    <div
        class="mt-5 rounded-2xl border border-dashed bg-[var(--section-soft)] p-8 text-center"
        style="border-color: var(--border)"
    >
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-[length:var(--font-2xl)]">
            🔐
        </div>

        <h3 class="mt-4 text-[length:var(--font-xl)] font-black text-[var(--title)]">
            Sin permisos asignados
        </h3>

        <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
            Este rol todavía no tiene permisos vinculados.
        </p>
    </div>
</template>
