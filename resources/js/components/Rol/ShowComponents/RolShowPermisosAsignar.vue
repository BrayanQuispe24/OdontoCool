<script setup lang="ts">
import { Modulo } from '@/types/Modulo';
import { Permiso } from '@/types/Permiso';

defineProps<{
    modulos: Modulo[];
    permisosFiltrados: Permiso[];
    moduloSeleccionado: string;
    asignarForm: any;
}>();

const emit = defineEmits<{
    'update:moduloSeleccionado': [value: string];
    'submit': [];
}>();
</script>

<template>
    <form
        class="mt-4 flex flex-col gap-3 rounded-2xl bg-[var(--section-soft)] p-4 md:flex-row md:items-start"
        @submit.prevent="emit('submit')"
    >
        <div class="w-full md:max-w-xs">
            <label class="mb-2 block text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                Módulo
            </label>

            <select
                :value="moduloSeleccionado"
                @change="emit('update:moduloSeleccionado', ($event.target as HTMLSelectElement).value)"
                class="h-11 w-full rounded-xl border bg-[var(--card)] px-4 text-[length:var(--font-sm)] font-bold text-[var(--title)] outline-none"
                style="border-color: var(--border)"
            >
                <option value="">Todos</option>

                <option
                    v-for="modulo in modulos"
                    :key="modulo.id"
                    :value="modulo.id"
                >
                    {{ modulo.nombre }}
                </option>
            </select>
        </div>

        <div class="w-full md:max-w-md">
            <label class="mb-2 block text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                Permiso
            </label>

            <select
                v-model="asignarForm.permiso_id"
                class="h-11 w-full rounded-xl border bg-[var(--card)] px-4 text-[length:var(--font-sm)] font-bold text-[var(--title)] outline-none"
                style="border-color: var(--border)"
            >
                <option value="">Seleccione un permiso</option>

                <option
                    v-for="permiso in permisosFiltrados"
                    :key="permiso.id"
                    :value="permiso.id"
                >
                    {{ permiso.nombre }}
                </option>
            </select>
        </div>

        <button
            type="submit"
            :disabled="asignarForm.processing"
            class="h-11 rounded-xl bg-[var(--primary)] px-5 text-[length:var(--font-sm)] font-black text-white transition hover:bg-[var(--primary-hover)] disabled:opacity-60 md:mt-6"
        >
            {{ asignarForm.processing ? 'Asignando...' : 'Asignar' }}
        </button>
    </form>
</template>
