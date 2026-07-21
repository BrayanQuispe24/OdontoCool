<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Permiso } from '@/types/Permiso';
import type { Modulo } from '@/types/Modulo';

const props = defineProps<{
    permiso: Permiso | null;
    editMode: boolean;
    modulos: Array<Modulo>;
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

const form = useForm({
    nombre: '',
    estado: 'activo',
    modulo_id: 0,
});

const cargarPermiso = (permiso: Permiso | null) => {
    form.clearErrors();

    if (permiso) {
        form.nombre = permiso.nombre;
        form.estado = permiso.estado;
        form.modulo_id = +permiso.modulo_id;
    } else {
        form.nombre = '';
        form.estado = 'activo';
        form.modulo_id = 0;
    }
};

watch(
    () => props.permiso,
    cargarPermiso,
    { immediate: true }
);

const submit = () => {
    if (props.editMode && props.permiso?.id) {
        form.put(route('permiso.update', props.permiso.id), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('saved');
            },
        });
        return;
    }

    form.post(route('permiso.store'), {
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
            {{ editMode ? 'Editar permiso' : 'Registrar permiso' }}
        </h2>

        <form class="mt-5 grid gap-4 md:grid-cols-2" @submit.prevent="submit">
            <!-- Nombre -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Nombre del permiso
                </label>

                <input v-model="form.nombre" type="text" placeholder="Ej: Pacientes"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.nombre" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.nombre }}
                </p>
            </div>

            <!-- Módulo -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Módulo
                </label>

                <select v-model="form.modulo_id"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)">
                    <option value="0" disabled>Selecciona un módulo</option>
                    <option v-for="modulo in modulos" :key="modulo.id" :value="modulo.id">
                        {{ modulo.nombre }}
                    </option>
                </select>

                <p v-if="form.errors.modulo_id" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.modulo_id }}
                </p>
            </div>

            <!-- Estado (solo en edición) -->
            <div v-if="editMode">
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Estado del permiso
                </label>

                <select v-model="form.estado"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>

                <p v-if="form.errors.estado" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.estado }}
                </p>
            </div>

            <!-- Actions -->
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
