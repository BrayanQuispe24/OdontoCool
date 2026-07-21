<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Propietario } from '@/types/Propietario';
import BaseSelect from '../Form/BaseSelect.vue';
import BaseInput from '../Form/BaseInput.vue';

const props = defineProps<{
    propietario: Propietario | null;
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

const editMode = computed(() => props.propietario !== null);

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
    fecha_inicio: '',
    porcentaje_participacion: '',
    estado: 'activo',
});

const limpiarFormulario = () => {
    form.reset();
    form.clearErrors();
    form.estado = 'activo';
};

const cargarPropietario = (propietario: Propietario | null) => {
    limpiarFormulario();

    if (!propietario) {
        return;
    }

    const usuario = propietario.persona?.usuarios?.[0];

    form.email = usuario?.email ?? '';
    form.url = usuario?.url ?? '';
    form.estado = usuario?.estado ?? 'activo';

    form.carnet_identidad = propietario.persona?.carnet_identidad ?? '';
    form.nombre = propietario.persona?.nombre ?? '';
    form.apellido = propietario.persona?.apellido ?? '';
    form.telefono = propietario.persona?.telefono ?? '';
    form.fecha_nacimiento = propietario.persona?.fecha_nacimiento ?? '';
    form.direccion = propietario.persona?.direccion ?? '';
    form.genero = propietario.persona?.genero ?? '';

    form.fecha_inicio = propietario.fecha_inicio ?? '';
    form.porcentaje_participacion = String(
        propietario.porcentaje_participacion ?? '',
    );
};

watch(
    () => props.propietario,
    cargarPropietario,
    {
        immediate: true,
    },
);

const submit = () => {
    if (props.propietario) {
        form.put(
            route(
                'administrador.update',
                props.propietario.codigo_propietario,
            ),
            {
                preserveScroll: true,
                onSuccess: () => {
                    limpiarFormulario();
                    emit('saved');
                },
            },
        );

        return;
    }

    form.post(route('administrador.store'), {
        preserveScroll: true,
        onSuccess: () => {
            limpiarFormulario();
            emit('saved');
        },
    });
};
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)]"
        style="border-color: var(--border)">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-[var(--title)]">
                {{ editMode ? 'Editar propietario' : 'Registrar propietario' }}
            </h2>

            <button type="button" class="font-bold text-red-500" @click="emit('cancel')">
                Cancelar
            </button>
        </div>

        <form class="mt-5 grid gap-4 md:grid-cols-2" @submit.prevent="submit">
            <BaseInput v-model="form.email" name="email" label="Email" type="email" placeholder="Ej: example@gmail.com"
                :error="form.errors.email" />

            <BaseInput v-if="!editMode" v-model="form.password" name="password" label="Contraseña" type="password"
                placeholder="Mínimo 8 caracteres" :error="form.errors.password" />

            <BaseInput v-if="!editMode" v-model="form.password_confirmation" name="password_confirmation"
                label="Confirmar contraseña" type="password" placeholder="Repita la contraseña"
                :error="form.errors.password_confirmation" />

            <BaseInput v-model="form.carnet_identidad" name="carnet_identidad" label="Carnet de identidad"
                placeholder="Ej: 12345678" :error="form.errors.carnet_identidad" />

            <BaseInput v-model="form.nombre" name="nombre" label="Nombre" placeholder="Ej: Juan"
                :error="form.errors.nombre" />

            <BaseInput v-model="form.apellido" name="apellido" label="Apellido" placeholder="Ej: Pérez"
                :error="form.errors.apellido" />

            <BaseInput v-model="form.fecha_nacimiento" name="fecha_nacimiento" label="Fecha de nacimiento" type="date"
                :error="form.errors.fecha_nacimiento" />

            <BaseInput v-model="form.direccion" name="direccion" label="Dirección" placeholder="Ej: Av. Principal #123"
                :error="form.errors.direccion" />

            <BaseInput v-model="form.telefono" name="telefono" label="Teléfono" placeholder="Ej: 70000000"
                :error="form.errors.telefono" />
            <BaseSelect v-model="form.genero" name="genero" label="Género" :options="[
                { value: '', label: 'Seleccione un género' },
                { value: 'masculino', label: 'Masculino' },
                { value: 'femenino', label: 'Femenino' },
                { value: 'otro', label: 'Otro' },
            ]" :error="form.errors.genero" />

            <BaseInput v-model="form.fecha_inicio" name="fecha_inicio" label="Fecha de inicio" type="date"
                :error="form.errors.fecha_inicio" />

            <BaseInput v-model="form.porcentaje_participacion" name="porcentaje_participacion"
                label="Porcentaje de participación" type="number" placeholder="Ej: 50"
                :error="form.errors.porcentaje_participacion" />

            <BaseSelect v-model="form.estado" name="estado" label="Estado" :options="[
                { value: 'activo', label: 'Activo' },
                { value: 'inactivo', label: 'Inactivo' },
            ]" :error="form.errors.estado" />

            <div class="flex justify-center gap-3 md:col-span-2">
                <button type="button" class="rounded-2xl border px-8 py-4 font-black"
                    style="border-color: var(--border)" @click="emit('cancel')">
                    Cancelar
                </button>

                <button type="submit" :disabled="form.processing"
                    class="rounded-2xl bg-[var(--text)] px-8 py-4 font-black text-[var(--card)] disabled:opacity-60">
                    {{
                        form.processing
                            ? 'Guardando...'
                            : editMode
                                ? 'Actualizar'
                                : 'Guardar'
                    }}
                </button>
            </div>
        </form>
    </div>
</template>