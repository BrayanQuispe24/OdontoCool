<script setup lang="ts">
import BaseInput from '../Form/BaseInput.vue';
import BaseSelect from '../Form/BaseSelect.vue';
import BaseTextArea from '../Form/BaseTextArea.vue';

defineProps<{
    carnetIdentidad: string;
    nombre: string;
    apellido: string;
    telefono: string;
    fechaNacimiento: string;
    direccion: string;
    genero: string;
    editando: boolean;
    errors: Record<string, string | undefined>;
}>();

const emit = defineEmits<{
    'update:carnetIdentidad': [value: string];
    'update:nombre': [value: string];
    'update:apellido': [value: string];
    'update:telefono': [value: string];
    'update:fechaNacimiento': [value: string];
    'update:direccion': [value: string];
    'update:genero': [value: string];
}>(); 
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300"
        style="border-color: var(--border)">
        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]"> Datos
            personales </p>
        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <BaseInput :model-value="carnetIdentidad" name="carnet_identidad" label="Carnet" type="text"
                :disabled="!editando" :error="errors.carnet_identidad"
                @update:model-value=" emit('update:carnetIdentidad', String($event))" />
            <BaseInput :model-value="telefono" name="telefono" label="Teléfono" type="text" :disabled="!editando"
                :error="errors.telefono" @update:model-value=" emit('update:telefono', String($event))" />
            <BaseInput :model-value="nombre" name="nombre" label="Nombre" type="text" :disabled="!editando"
                :error="errors.nombre" @update:model-value=" emit('update:nombre', String($event))" />
            <BaseInput :model-value="apellido" name="apellido" label="Apellido" type="text" :disabled="!editando"
                :error="errors.apellido" @update:model-value=" emit('update:apellido', String($event))" />
            <BaseInput :model-value="fechaNacimiento" name="fecha_nacimiento" label="Fecha de nacimiento" type="date"
                :disabled="!editando" :error="errors.fecha_nacimiento"
                @update:model-value=" emit('update:fechaNacimiento', String($event))" />
            <BaseSelect :model-value="genero" name="genero" label="Género" :disabled="!editando"
                :options="[{ value: '', label: 'Sin género', }, { value: 'masculino', label: 'Masculino', }, { value: 'femenino', label: 'Femenino', }, { value: 'otro', label: 'Otro', },]"
                :error="errors.genero" @update:model-value=" emit('update:genero', String($event))" />
            <BaseTextArea :model-value="direccion" name="direccion" label="Dirección" :rows="3" :disabled="!editando"
                :error="errors.direccion" class="sm:col-span-2"
                @update:model-value=" emit('update:direccion', String($event))" />
        </div>
    </div>
</template>