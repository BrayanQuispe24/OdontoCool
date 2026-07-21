<script setup lang="ts">
defineProps<{
    search: string;
    resultsCount: number;
    showForm: boolean;
}>();

const emit = defineEmits<{
    'update:search': [value: string];
    'toggle-form': [];
}>();
</script>

<template>
    <!-- Unified Premium Header Card -->
    <div class="flex flex-col gap-4 rounded-3xl border p-6 shadow-[0_15px_40px_rgba(0,169,157,0.04)] border-[var(--border)] bg-[var(--card)] lg:flex-row lg:items-center lg:justify-between transition-colors duration-300">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.25em] text-[var(--primary)]">
                Administración
            </p>
            <h1 class="text-xl font-black text-[var(--title)] mt-1">
                Gestión de especialidades
            </h1>
            <p class="text-[length:var(--font-xs)] leading-5 text-[var(--muted)] mt-1">
                Busca, visualiza y registra especialidades del sistema OdontoCool.
            </p>
        </div>

        <!-- Filters & Action Buttons -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search bar -->
            <div class="relative w-full max-w-[300px] sm:w-auto">
                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                    ⌕
                </span>
                <input
                    :value="search"
                    @input="emit('update:search', ($event.target as HTMLInputElement).value)"
                    type="text"
                    placeholder="Buscar especialidad..."
                    class="w-full rounded-2xl border bg-[var(--section-soft)] py-3 pl-10 pr-5 text-[length:var(--font-sm)] text-[var(--title)] border-[var(--border)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="--tw-ring-color: var(--primary-soft)"
                />
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-2xl bg-[var(--section-soft)] px-4 py-3 text-xs font-bold text-[var(--muted)] border border-[var(--border)]">
                    {{ resultsCount }} resultado(s)
                </span>

                <button type="button" @click="emit('toggle-form')"
                    class="whitespace-nowrap inline-flex items-center justify-center gap-2 rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-lg shadow-teal-200 transition hover:bg-[var(--primary-hover)] hover:shadow-none dark:shadow-none active:scale-95 duration-150">
                    {{ showForm ? 'Cerrar formulario' : '+ Nueva especialidad' }}
                </button>
            </div>
        </div>
    </div>
</template>
