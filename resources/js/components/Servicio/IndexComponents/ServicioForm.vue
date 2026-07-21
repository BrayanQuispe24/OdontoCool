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
            {{ editMode ? 'Editar servicio' : 'Registrar servicio' }}
        </h2>

        <!-- Errors Alert -->
        <div
            v-if="Object.keys(form.errors).length > 0"
            class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-[length:var(--font-xs)] font-bold text-red-500"
        >
            <p class="mb-1 text-[length:var(--font-sm)] font-black">
                Por favor corrige los siguientes errores:
            </p>

            <ul class="list-disc space-y-1 pl-4">
                <li v-for="(error, field) in form.errors" :key="field">
                    {{ field }}: {{ error }}
                </li>
            </ul>
        </div>

        <form class="mt-5 grid gap-4 md:grid-cols-2" @submit.prevent="emit('submit')">
            <!-- Nombre -->
            <div>
                <label for="nombre" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Nombre del servicio
                </label>

                <input
                    id="nombre"
                    v-model="form.nombre"
                    type="text"
                    placeholder="Ej: Profilaxis Dental"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                />

                <p v-if="form.errors.nombre" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.nombre }}
                </p>
            </div>

            <!-- Tipo -->
            <div>
                <label for="tipo" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Tipo / Especialidad
                </label>

                <input
                    id="tipo"
                    v-model="form.tipo"
                    type="text"
                    placeholder="Ej: Odontopediatría, Ortodoncia"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                />

                <p v-if="form.errors.tipo" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.tipo }}
                </p>
            </div>

            <!-- Descripción -->
            <div class="md:col-span-2">
                <label for="descripcion" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Descripción
                </label>

                <textarea
                    id="descripcion"
                    v-model="form.descripcion"
                    rows="3"
                    placeholder="Detalle sobre el procedimiento o tratamiento..."
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                ></textarea>

                <p v-if="form.errors.descripcion" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.descripcion }}
                </p>
            </div>

            <!-- Estado -->
            <div v-if="editMode">
                <label for="estado" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Estado
                </label>

                <select
                    id="estado"
                    v-model="form.estado"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                >
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="INACTIVO">INACTIVO</option>
                </select>

                <p v-if="form.errors.estado" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.estado }}
                </p>
            </div>

            <!-- Sección de Precio -->
            <div class="mt-2 border-t pt-4 md:col-span-2" style="border-color: var(--border)">
                <h3 class="mb-4 text-[length:var(--font-lg)] font-bold text-[var(--title)]">
                    Precio y Vigencia
                </h3>

                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Monto y Moneda -->
                    <div>
                        <label for="monto" class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            Monto del precio
                        </label>

                        <div class="flex gap-2">
                            <input
                                id="monto"
                                v-model="form.monto"
                                type="number"
                                step="0.01"
                                placeholder="Ej: 150.00"
                                required
                                class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                            />

                            <select
                                v-model="form.moneda"
                                required
                                class="rounded-2xl border bg-[var(--section-soft)] py-4 pl-4 pr-10 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                                style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                            >
                                <option value="BOB">BOB</option>
                                <option value="USD">USD</option>
                            </select>
                        </div>

                        <p v-if="form.errors.monto" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                            {{ form.errors.monto }}
                        </p>

                        <p v-if="form.errors.moneda" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                            {{ form.errors.moneda }}
                        </p>
                    </div>

                    <!-- Fechas de Vigencia -->
                    <div>
                        <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            Vigencia del precio
                        </label>

                        <div class="flex items-center gap-2">
                            <input
                                v-model="form.fecha_inicio"
                                type="date"
                                required
                                class="w-full rounded-2xl border bg-[var(--section-soft)] px-4 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                                style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                            />

                            <span class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                                al
                            </span>

                            <input
                                v-model="form.fecha_fin"
                                type="date"
                                required
                                class="w-full rounded-2xl border bg-[var(--section-soft)] px-4 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                                style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                            />
                        </div>

                        <p v-if="form.errors.fecha_inicio" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                            {{ form.errors.fecha_inicio }}
                        </p>

                        <p v-if="form.errors.fecha_fin" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                            {{ form.errors.fecha_fin }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-4 flex justify-end gap-3 md:col-span-2">
                <button
                    v-if="editMode"
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
