<script setup lang="ts">
import type { Modulo } from '@/types/Modulo';

defineProps<{
    search: string;
    moduloSeleccionado: string;
    resultsCount: number;
    showForm: boolean;
    modulos: Array<Modulo>;
}>();

const emit = defineEmits<{
    'update:search': [value: string];
    'update:moduloSeleccionado': [value: string];
    'toggle-form': [];
}>();
</script>

<template>
    <div class="space-y-4">
        <!-- Title Card -->
        <div class="flex flex-col gap-4 rounded-3xl border p-6 shadow-[0_15px_40px_rgba(0,169,157,0.04)] border-[var(--border)] bg-[var(--card)] lg:flex-row lg:items-center lg:justify-between transition-colors duration-300">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-[var(--primary)]">
                    Administración
                </p>
                <h1 class="text-xl font-black text-[var(--title)] mt-1">
                    Gestión de permisos
                </h1>
                <p class="text-[length:var(--font-xs)] leading-5 text-[var(--muted)] mt-1 max-w-lg">
                    Busca, visualiza y registra permisos del sistema OdontoCool.
                </p>
            </div>

            <button type="button" @click="emit('toggle-form')"
                class="whitespace-nowrap inline-flex items-center justify-center gap-2 rounded-2xl bg-[var(--primary)] px-6 py-3 text-[length:var(--font-sm)] font-black text-white shadow-lg shadow-teal-200 transition hover:bg-[var(--primary-hover)] hover:shadow-none dark:shadow-none active:scale-95 duration-150">
                {{ showForm ? 'Cerrar formulario' : '+ Nuevo permiso' }}
            </button>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col gap-3 rounded-3xl border bg-[var(--card)] p-5 shadow-[0_15px_40px_rgba(0,169,157,0.04)] transition-colors duration-300 md:flex-row md:items-center md:justify-between border-[var(--border)]">
            <div class="relative w-full md:w-[30%]">
                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                    ⌕
                </span>

                <input
                    :value="search"
                    @input="emit('update:search', ($event.target as HTMLInputElement).value)"
                    type="text"
                    placeholder="Buscar permiso..."
                    class="w-full rounded-2xl border bg-[var(--section-soft)] py-3 pl-10 pr-5 text-[length:var(--font-sm)] text-[var(--title)] border-[var(--border)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="--tw-ring-color: var(--primary-soft)"
                />
            </div>

            <select
                :value="moduloSeleccionado"
                @change="emit('update:moduloSeleccionado', ($event.target as HTMLSelectElement).value)"
                class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-3 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4 md:w-[30%] border-[var(--border)]"
                style="--tw-ring-color: var(--primary-soft)"
            >
                <option value="">Todos los módulos</option>
                <option v-for="modulo in modulos" :key="modulo.id" :value="modulo.id">
                    {{ modulo.nombre }}
                </option>
            </select>

            <span class="inline-flex items-center justify-center rounded-2xl bg-[var(--section-soft)] px-4 py-3 text-xs font-bold text-[var(--muted)] border border-[var(--border)] md:w-[20%] text-center">
                {{ resultsCount }} resultado(s)
            </span>
        </div>
    </div>
</template>
