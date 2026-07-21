<script setup lang="ts">
import BaseInput from '../Form/BaseInput.vue';

defineProps<{
    codigoPropietario: string;
    fechaInicio: string;
    porcentajeParticipacion: string | number;
    editando: boolean; errors: Record<string, string | undefined>;
}>();
const emit = defineEmits<{ 'update:fechaInicio': [value: string]; 'update:porcentajeParticipacion': [value: string]; }>();
</script>
<template>
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300"
        style="border-color: var(--border)">
        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]"> Datos de
            propietario </p>
        <div class="mt-5 grid gap-5 sm:grid-cols-3">
            <div>
                <p class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]"> Código propietario </p>
                <p class="mt-2 break-words text-[length:var(--font-lg)] font-black text-[var(--title)]"> {{
                    codigoPropietario }} </p>
            </div>
            <BaseInput :model-value="fechaInicio" name="fecha_inicio" label="Fecha de inicio" type="date"
                :disabled="!editando" :error="errors.fecha_inicio"
                @update:model-value=" emit('update:fechaInicio', String($event))" />
            <BaseInput :model-value="porcentajeParticipacion" name="porcentaje_participacion" label="Participación"
                type="number" placeholder="Ej: 50" :disabled="!editando" :error="errors.porcentaje_participacion"
                @update:model-value=" emit('update:porcentajeParticipacion', String($event),)" />
        </div>
    </div>
</template>