<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Doctor } from '@/types/doctor';

const props = defineProps<{
    doctor: Doctor | null;
    editMode: boolean;
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
    matricula_profesional: '',
    experiencia: 0,
    telefono_profesional: '',
    fecha_contratacion: '',
    estado: 'activo',
});

const cargarDoctor = (doctor: Doctor | null) => {
    form.clearErrors();

    if (doctor) {
        const usuario = doctor.persona?.usuarios?.[0];

        form.email = usuario?.email ?? '';
        form.url = usuario?.url ?? '';
        form.estado = usuario?.estado ?? 'activo';

        form.carnet_identidad = doctor.persona?.carnet_identidad ?? '';
        form.nombre = doctor.persona?.nombre ?? '';
        form.apellido = doctor.persona?.apellido ?? '';
        form.telefono = doctor.persona?.telefono ?? '';
        form.fecha_nacimiento = doctor.persona?.fecha_nacimiento ?? '';
        form.direccion = doctor.persona?.direccion ?? '';
        form.genero = doctor.persona?.genero ?? '';

        form.matricula_profesional = doctor.matricula_profesional ?? '';
        form.experiencia = Number(doctor.experiencia ?? 0);
        form.telefono_profesional = doctor.telefono_profesional ?? '';
        form.fecha_contratacion = doctor.fecha_contratacion ?? '';

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
        form.matricula_profesional = '';
        form.experiencia = 0;
        form.telefono_profesional = '';
        form.fecha_contratacion = '';
        form.estado = 'activo';
    }
};

watch(
    () => props.doctor,
    cargarDoctor,
    { immediate: true }
);

const submit = () => {
    if (props.editMode && props.doctor?.codigo_doctor) {
        form.put(route('doctor.update', props.doctor.codigo_doctor), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('saved');
            },
        });

        return;
    }

    form.post(route('doctor.store'), {
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
            {{ editMode ? 'Editar doctor' : 'Registrar doctor' }}
        </h2>

        <form class="mt-5 grid gap-4 md:grid-cols-2" @submit.prevent="submit">
            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Email
                </label>

                <input v-model="form.email" type="email" placeholder="Ej: example@gmail.com"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.email" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.email }}
                </p>
            </div>

            <div v-if="!editMode">
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Contraseña
                </label>

                <input v-model="form.password" type="password" placeholder="Mínimo 8 caracteres"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.password" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.password }}
                </p>
            </div>

            <div v-if="!editMode">
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Confirmar contraseña
                </label>

                <input v-model="form.password_confirmation" type="password" placeholder="Confirmar contraseña"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.password_confirmation"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Carnet de identidad
                </label>

                <input v-model="form.carnet_identidad" type="text" placeholder="Ej: 12345678"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.carnet_identidad"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.carnet_identidad }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Nombre
                </label>

                <input v-model="form.nombre" type="text" placeholder="Ej: Juan"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.nombre" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.nombre }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Apellido
                </label>

                <input v-model="form.apellido" type="text" placeholder="Ej: Pérez"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.apellido" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.apellido }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Fecha de nacimiento
                </label>

                <input v-model="form.fecha_nacimiento" type="date"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.fecha_nacimiento"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.fecha_nacimiento }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Dirección
                </label>

                <input v-model="form.direccion" type="text" placeholder="Ej: Av. Principal #123"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.direccion"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.direccion }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Género
                </label>

                <input v-model="form.genero" type="text" placeholder="Ej: Masculino"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.genero" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.genero }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Teléfono
                </label>

                <input v-model="form.telefono" type="text" placeholder="Ej: 70000000"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.telefono" class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.telefono }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Matrícula profesional
                </label>

                <input v-model="form.matricula_profesional" type="text" placeholder="Ej: 12345"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.matricula_profesional"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.matricula_profesional }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Experiencia (años)
                </label>

                <input v-model="form.experiencia" type="text" placeholder="Ej: 5"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.experiencia"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.experiencia }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Telefono profesional
                </label>

                <input v-model="form.telefono_profesional" type="text" placeholder="Ej: 5"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.telefono_profesional"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.telefono_profesional }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Fecha de contratación
                </label>

                <input v-model="form.fecha_contratacion" type="date" placeholder="Ej: 2023-01-01"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                <p v-if="form.errors.fecha_contratacion"
                    class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                    {{ form.errors.fecha_contratacion }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                    Estado
                </label>

                <select v-model="form.estado"
                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>

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
