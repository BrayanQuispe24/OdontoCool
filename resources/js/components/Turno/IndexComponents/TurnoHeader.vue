<script setup lang="ts">
defineProps<{
    search: string;
    showForm: boolean;
}>();

const emit = defineEmits<{
    'update:search': [value: string];
    'toggle-form': [];
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Administración
                </p>

                <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                    Gestión de turnos
                </h1>

                <p class="mt-2 text-[length:var(--font-sm)] leading-6 text-[var(--muted)]">
                    Busca, visualiza y registra turnos del sistema OdontoCool.
                </p>
            </div>

            <div class="flex w-full flex-col gap-3 sm:flex-row lg:max-w-2xl lg:justify-end">
                <div class="relative w-full sm:max-w-sm">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                        ⌕
                    </span>

                    <input
                        :value="search"
                        @input="emit('update:search', ($event.target as HTMLInputElement).value)"
                        type="text"
                        placeholder="Buscar turno..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-3 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                    />
                </div>

                <button
                    type="button"
                    @click="emit('toggle-form')"
                    class="whitespace-nowrap rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-[0_15px_35px_rgba(0,169,157,0.25)] transition hover:-translate-y-1 hover:bg-[var(--primary-hover)]"
                >
                    {{ showForm ? 'Cerrar formulario' : '+ Nuevo turno' }}
                </button>
            </div>
        </div>
    </div>
</template>
