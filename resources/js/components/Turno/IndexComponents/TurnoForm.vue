<script setup lang="ts">
defineProps<{
    form: any;
    editMode: boolean;
    estados: string[];
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
            {{ editMode ? 'Editar turno' : 'Registrar turno' }}
        </h2>

        <form class="mt-5 grid gap-4 md:grid-cols-2" @submit.prevent="emit('submit')">
            <div>
                <label for="nombre" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Nombre del turno
                </label>

                <input
                    id="nombre"
                    v-model="form.nombre"
                    type="text"
                    placeholder="Ej: Mañana, Tarde, etc."
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                />

                <p v-if="form.errors.nombre" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.nombre }}
                </p>
            </div>

            <div>
                <label for="hora_inicio" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Hora de inicio
                </label>

                <input
                    id="hora_inicio"
                    v-model="form.hora_inicio"
                    type="time"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                />

                <p v-if="form.errors.hora_inicio" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.hora_inicio }}
                </p>
            </div>

            <div>
                <label for="hora_fin" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Hora de fin
                </label>

                <input
                    id="hora_fin"
                    v-model="form.hora_fin"
                    type="time"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                />

                <p v-if="form.errors.hora_fin" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.hora_fin }}
                </p>
            </div>

            <div v-if="editMode">
                <label for="estado" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Estado del turno
                </label>

                <select
                    id="estado"
                    v-model="form.estado"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                >
                    <option value="">Seleccionar estado</option>
                    <option v-for="estado in estados" :key="estado" :value="estado">
                        {{ estado }}
                    </option>
                </select>

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
