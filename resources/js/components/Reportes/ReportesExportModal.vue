<script setup lang="ts">
defineProps<{
    show: boolean;
    periodo: string;
    doctor: string;
}>();

const emit = defineEmits<{
    close: [];
    'export-pdf': [];
    'export-excel': [];
}>();
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-[9998] bg-slate-900/50 backdrop-blur-sm"
            @click="emit('close')"></div>

        <Transition enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-4 opacity-0 scale-95" enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100 scale-100"
            leave-to-class="translate-y-4 opacity-0 scale-95">
            <div v-if="show"
                class="fixed left-1/2 top-1/2 z-[9999] w-[calc(100vw-2rem)] max-w-[460px] -translate-x-1/2 -translate-y-1/2 rounded-3xl border bg-[var(--card)] p-5 shadow-[0_25px_80px_rgba(15,23,42,0.35)] border-[var(--border)]"
                @click.stop>
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                            Exportar reporte
                        </p>

                        <h3 class="mt-1 text-[length:var(--font-lg)] font-black text-[var(--title)]">
                            Selecciona el formato
                        </h3>

                        <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                            Se exportará el reporte usando el rango de fechas y doctor seleccionado.
                        </p>
                    </div>

                    <button type="button"
                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500"
                        @click="emit('close')">
                        ✕
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <button type="button"
                        class="rounded-2xl border border-emerald-600 bg-emerald-50 px-4 py-5 text-left transition hover:-translate-y-0.5 hover:bg-emerald-100"
                        @click="emit('export-pdf')">
                        <span class="block text-[length:var(--font-2xl)]">📄</span>
                        <span class="mt-2 block text-[length:var(--font-sm)] font-black text-emerald-700">
                            Exportar PDF
                        </span>
                        <span class="mt-1 block text-[length:var(--font-xs)] font-bold text-emerald-700/70">
                            Archivo imprimible del reporte
                        </span>
                    </button>

                    <button type="button"
                        class="rounded-2xl border border-sky-600 bg-sky-50 px-4 py-5 text-left transition hover:-translate-y-0.5 hover:bg-sky-100"
                        @click="emit('export-excel')">
                        <span class="block text-[length:var(--font-2xl)]">📊</span>
                        <span class="mt-2 block text-[length:var(--font-sm)] font-black text-sky-700">
                            Exportar Excel
                        </span>
                        <span class="mt-1 block text-[length:var(--font-xs)] font-bold text-sky-700/70">
                            Libro con varias hojas
                        </span>
                    </button>
                </div>

                <div class="mt-5 rounded-2xl bg-[var(--section-soft)] p-3">
                    <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                        Periodo:
                        <span class="font-black text-[var(--title)]">{{ periodo }}</span>
                    </p>
                    <p class="mt-1 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                        Doctor:
                        <span class="font-black text-[var(--title)]">{{ doctor }}</span>
                    </p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
