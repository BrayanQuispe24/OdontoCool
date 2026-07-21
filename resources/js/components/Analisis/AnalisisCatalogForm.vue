<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Analisis } from '@/types/Analisis';

const props = defineProps<{
    analisis: Analisis | null;
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

const editMode = computed(() => props.analisis !== null);

const catalogForm = useForm({
    nombre: '',
    descripcion: '',
    estado: 'ACTIVO',
});

const resetCatalogForm = () => {
    catalogForm.reset();
    catalogForm.clearErrors();
};

const cargarAnalisis = (analisis: Analisis | null) => {
    resetCatalogForm();

    if (!analisis) {
        return;
    }

    catalogForm.nombre = analisis.nombre;
    catalogForm.descripcion = analisis.descripcion || '';
    catalogForm.estado = analisis.estado;
};

watch(
    () => props.analisis,
    cargarAnalisis,
    {
        immediate: true,
    },
);

const submit = () => {
    if (props.analisis) {
        catalogForm.put(route('analisis.update', props.analisis.id), {
            preserveScroll: true,
            onSuccess: () => {
                resetCatalogForm();
                emit('saved');
            },
        });
        return;
    }

    catalogForm.post(route('analisis.store'), {
        preserveScroll: true,
        onSuccess: () => {
            resetCatalogForm();
            emit('saved');
        },
    });
};
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)">
        <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
            {{ editMode ? 'Editar tipo de análisis' : 'Registrar tipo de análisis' }}
        </h2>

        <form class="mt-5 space-y-4" @submit.prevent="submit">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Nombre del análisis
                    </label>

                    <input v-model="catalogForm.nombre" type="text" placeholder="Ej: Radiografía Oclusal"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                    <p v-if="catalogForm.errors.nombre"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ catalogForm.errors.nombre }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Descripción
                    </label>

                    <textarea v-model="catalogForm.descripcion" rows="3"
                        placeholder="Indica especificaciones, ayunas o requerimientos especiales..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t pt-4" style="border-color: var(--border)">
                <button type="button" @click="emit('cancel')"
                    class="rounded-2xl border bg-[var(--card)] px-6 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)]"
                    style="border-color: var(--border)">
                    Cancelar
                </button>

                <button type="submit" :disabled="catalogForm.processing"
                    class="rounded-2xl bg-[var(--text)] px-6 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:opacity-60">
                    {{ catalogForm.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>
