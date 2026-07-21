<script setup lang="ts">
defineProps<{
    form: any;
    editMode: boolean;
}>();

const emit = defineEmits<{
    'submit': [];
    'cancel': [];
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
            {{ editMode ? 'Editar rol' : 'Registrar rol' }}
        </h2>

        <form class="mt-5 grid gap-4 md:grid-cols-2" @submit.prevent="emit('submit')">
            <div>
                <label for="nombre" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Nombre del rol
                </label>

                <input
                    id="nombre"
                    v-model="form.nombre"
                    type="text"
                    placeholder="Ej: Administrador"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                />

                <p v-if="form.errors.nombre" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.nombre }}
                </p>
            </div>

            <div>
                <label for="description" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Descripcion del rol
                </label>

                <input
                    id="description"
                    v-model="form.description"
                    type="text"
                    placeholder="Ej: Rol con todos los permisos"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                />

                <p v-if="form.errors.description" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.description }}
                </p>
            </div>

            <div v-if="editMode">
                <label for="estado" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Estado del rol
                </label>

                <input
                    id="estado"
                    v-model="form.estado"
                    type="text"
                    placeholder="Ej: Activo"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                />

                <p v-if="form.errors.estado" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.estado }}
                </p>
            </div>

            <div class="flex items-end gap-3 md:col-span-2">
                <button
                    type="button"
                    @click="emit('cancel')"
                    class="rounded-2xl border bg-[var(--card)] px-8 py-4 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)]"
                    style="border-color: var(--border)"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-2xl bg-[var(--text)] px-8 py-4 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:opacity-60"
                >
                    {{ form.processing ? 'Guardando...' : editMode ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>
