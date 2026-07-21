<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Modulo } from '@/types/Modulo';

const props = defineProps<{
    modulo: Modulo | null;
    editMode: boolean;
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

const form = useForm({
    nombre: '',
    estado: 'activo',
});

const cargarModulo = (modulo: Modulo | null) => {
    form.clearErrors();

    if (modulo) {
        form.nombre = modulo.nombre;
        form.estado = modulo.estado;
    } else {
        form.nombre = '';
        form.estado = 'activo';
    }
};

watch(
    () => props.modulo,
    cargarModulo,
    { immediate: true }
);

const submit = () => {
    if (props.editMode && props.modulo?.id) {
        form.put(route('modulo.update', props.modulo.id), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('saved');
            },
        });

        return;
    }

    form.post(route('modulo.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('saved');
        },
    });
};
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 border-[var(--border)]">
        <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
            {{ editMode ? 'Editar módulo' : 'Registrar módulo' }}
        </h2>

        <form class="mt-5 grid gap-4 md:grid-cols-2" @submit.prevent="submit">
            <div>
                <label for="nombre" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Nombre del módulo
                </label>

                <input id="nombre" v-model="form.nombre" type="text" placeholder="Ej: Pacientes"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.nombre" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.nombre }}
                </p>
            </div>

            <div v-if="editMode">
                <label for="estado" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Estado del módulo
                </label>

                <input id="estado" v-model="form.estado" type="text" placeholder="Ej: Activo"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.estado" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.estado }}
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 md:col-span-2 border-t pt-4 border-[var(--border)]">
                <button type="button" @click="emit('cancel')"
                    class="rounded-2xl border bg-[var(--card)] px-6 py-3.5 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)] border-[var(--border)]">
                    Cancelar
                </button>
                <button type="submit" :disabled="form.processing"
                    class="rounded-2xl bg-[var(--text)] px-8 py-3.5 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:opacity-60">
                    {{ form.processing ? 'Guardando...' : editMode ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>
