<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Paciente } from '@/types/Paciente';

const props = defineProps<{
    paciente: Paciente | null;
    editMode: boolean;
    tieneRol: (rol: string) => boolean;
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

const form = useForm({
    email: '',
    url: '',
    password: '',
    password_confirmation: '',
    carnet_identidad: '',
    nombre: '',
    apellido: '',
    fecha_nacimiento: '',
    direccion: '',
    genero: '',
    telefono: '',
    contacto_emergencia: '',
    telefono_emergencia: '',
    estado: 'activo',
});

const cargarPaciente = (paciente: Paciente | null) => {
    form.clearErrors();

    if (paciente) {
        const usuario = paciente.persona?.usuarios?.[0];

        form.email = usuario?.email ?? '';
        form.url = usuario?.url ?? '';
        form.estado = usuario?.estado ?? 'activo';

        form.carnet_identidad = paciente.persona?.carnet_identidad ?? '';
        form.nombre = paciente.persona?.nombre ?? '';
        form.apellido = paciente.persona?.apellido ?? '';
        form.telefono = paciente.persona?.telefono ?? '';
        form.fecha_nacimiento = paciente.persona?.fecha_nacimiento ?? '';
        form.direccion = paciente.persona?.direccion ?? '';
        form.genero = paciente.persona?.genero ?? '';

        form.contacto_emergencia = paciente.contacto_emergencia ?? '';
        form.telefono_emergencia = paciente.telefono_emergencia ?? '';

        form.password = '';
        form.password_confirmation = '';
    } else {
        form.email = '';
        form.url = '';
        form.password = '';
        form.password_confirmation = '';
        form.carnet_identidad = '';
        form.nombre = '';
        form.apellido = '';
        form.fecha_nacimiento = '';
        form.direccion = '';
        form.genero = '';
        form.telefono = '';
        form.contacto_emergencia = '';
        form.telefono_emergencia = '';
        form.estado = 'activo';
    }
};

watch(
    () => props.paciente,
    cargarPaciente,
    { immediate: true }
);

const submit = () => {
    if (props.editMode && props.paciente?.codigo_paciente) {
        form.put(route('paciente.update', props.paciente.codigo_paciente), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('saved');
            },
        });
        return;
    }

    form.post(route('paciente.store'), {
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
            {{ editMode ? 'Editar paciente' : 'Registrar paciente' }}
        </h2>

        <form class="mt-5 grid gap-4 md:grid-cols-2" @submit.prevent="submit">
            <!-- Email -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Email
                </label>

                <input :disabled="editMode && (!tieneRol('Paciente') || !tieneRol('Administrador'))"
                    v-model="form.email" type="email" placeholder="Ej: example@gmail.com"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.email" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.email }}
                </p>
            </div>

            <!-- Contraseñas -->
            <template v-if="!editMode">
                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Contraseña
                    </label>

                    <input v-model="form.password" type="password" placeholder="Mínimo 8 caracteres"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                        style="--tw-ring-color: var(--primary-soft)" />

                    <p v-if="form.errors.password" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.password }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Confirmar contraseña
                    </label>

                    <input v-model="form.password_confirmation" type="password" placeholder="Confirmar contraseña"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                        style="--tw-ring-color: var(--primary-soft)" />

                    <p v-if="form.errors.password_confirmation"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.password_confirmation }}
                    </p>
                </div>
            </template>

            <!-- Carnet -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Carnet de identidad
                </label>

                <input v-model="form.carnet_identidad" type="text" placeholder="Ej: 12345678"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.carnet_identidad"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.carnet_identidad }}
                </p>
            </div>

            <!-- Nombre -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Nombre
                </label>

                <input v-model="form.nombre" type="text" placeholder="Ej: Juan"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.nombre" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.nombre }}
                </p>
            </div>

            <!-- Apellido -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Apellido
                </label>

                <input v-model="form.apellido" type="text" placeholder="Ej: Pérez"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.apellido" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.apellido }}
                </p>
            </div>

            <!-- Fecha Nacimiento -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Fecha de nacimiento
                </label>

                <input v-model="form.fecha_nacimiento" type="date"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.fecha_nacimiento"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.fecha_nacimiento }}
                </p>
            </div>

            <!-- Dirección -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Dirección
                </label>

                <input v-model="form.direccion" type="text" placeholder="Ej: Av. Principal #123"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.direccion"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.direccion }}
                </p>
            </div>

            <!-- Género -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Género
                </label>

                <input v-model="form.genero" type="text" placeholder="Ej: Masculino"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.genero" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.genero }}
                </p>
            </div>

            <!-- Teléfono -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Teléfono
                </label>

                <input v-model="form.telefono" type="text" placeholder="Ej: 70000000"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.telefono" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.telefono }}
                </p>
            </div>

            <!-- Contacto de emergencia -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Contacto de emergencia
                </label>

                <input v-model="form.contacto_emergencia" type="text" placeholder="Ej: Mamá"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.contacto_emergencia"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.contacto_emergencia }}
                </p>
            </div>

            <!-- Teléfono de emergencia -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Teléfono de emergencia
                </label>

                <input v-model="form.telefono_emergencia" type="text" placeholder="Ej: 70000000"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.telefono_emergencia"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.telefono_emergencia }}
                </p>
            </div>

            <!-- Estado -->
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Estado
                </label>

                <select v-model="form.estado"
                    :disabled="editMode && (!tieneRol('Paciente') || !tieneRol('Administrador'))"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4 border-[var(--border)]"
                    style="--tw-ring-color: var(--primary-soft)">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>

                <p v-if="form.errors.estado" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.estado }}
                </p>
            </div>

            <!-- Form Actions -->
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
