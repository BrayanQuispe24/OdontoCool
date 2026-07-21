<script setup lang="ts">
defineProps<{
    citasDisponiblesCount: number;
    editMode: boolean;
    showForm: boolean;
    search: string;
    resultsCount: number;
}>();

const emit = defineEmits<{
    'open-form': [];
    'update:search': [value: string];
}>();
</script>

<template>
    <div class="space-y-6">
        <!-- Warnings -->
        <div v-if="citasDisponiblesCount === 0 && !editMode"
            class="flex items-center gap-3 rounded-2xl border p-4 text-[length:var(--font-sm)] bg-amber-500/5 text-amber-600 border-amber-500/20 dark:border-amber-900/40">
            <span>⚠️</span>
            <span>
                Todas las citas médicas registradas ya cuentan con su respectivo diagnóstico. Crea una nueva cita antes de generar otro diagnóstico.
            </span>
        </div>

        <!-- Header Card -->
        <div class="flex flex-col gap-4 rounded-3xl border p-6 shadow-[0_15px_40px_rgba(0,169,157,0.04)] border-[var(--border)] bg-[var(--card)] lg:flex-row lg:items-center lg:justify-between transition-colors duration-300">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[var(--primary)]">
                    Diagnósticos
                </p>
                <h1 class="text-xl font-black text-[var(--title)] mt-1">
                    Listado de Diagnósticos Clínicos
                </h1>
                <p class="text-[length:var(--font-xs)] text-[var(--muted)] mt-1">
                    Registra y administra los reportes de patologías o afecciones detectadas en consulta.
                </p>
            </div>

            <!-- Search, Stats & Action Button -->
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
                        placeholder="Buscar diagnóstico..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] py-3 pl-10 pr-5 text-[length:var(--font-sm)] text-[var(--title)] border-[var(--border)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="--tw-ring-color: var(--primary-soft)"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-2xl bg-[var(--section-soft)] px-4 py-3 text-xs font-bold text-[var(--muted)] border border-[var(--border)]">
                        {{ resultsCount }} resultado(s)
                    </span>

                    <button v-if="!showForm && citasDisponiblesCount > 0" type="button" @click="emit('open-form')"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-lg shadow-teal-200 transition hover:bg-[var(--primary-hover)] hover:shadow-none dark:shadow-none active:scale-95 duration-150">
                        + Registrar Diagnóstico
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
