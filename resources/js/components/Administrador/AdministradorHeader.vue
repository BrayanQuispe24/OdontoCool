<script setup lang="ts">
defineProps<{
    search: string;
    showForm: boolean;
}>();

const emit = defineEmits<{
    'update:search': [value: string];
    create: [];
    close: [];
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)]"
        style="border-color: var(--border)"
    >
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Administración
                </p>

                <h1 class="mt-2 text-3xl font-black text-[var(--title)]">
                    Gestión de propietarios
                </h1>

                <p class="mt-2 text-sm text-[var(--muted)]">
                    Busca, visualiza, registra y administra propietarios del sistema.
                </p>
            </div>

            <div class="flex w-full flex-col gap-3 sm:flex-row lg:max-w-2xl lg:justify-end">
                <input
                    :value="search"
                    type="text"
                    placeholder="Buscar propietario..."
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-3"
                    style="border-color: var(--border)"
                    @input="emit(
                        'update:search',
                        ($event.target as HTMLInputElement).value
                    )"
                />

                <button
                    v-if="!showForm"
                    type="button"
                    class="rounded-2xl bg-[var(--primary)] px-6 py-3 font-black text-white"
                    @click="emit('create')"
                >
                    + Nuevo propietario
                </button>

                <button
                    v-else
                    type="button"
                    class="rounded-2xl bg-red-500 px-6 py-3 font-black text-white"
                    @click="emit('close')"
                >
                    Cerrar formulario
                </button>
            </div>
        </div>
    </div>
</template>