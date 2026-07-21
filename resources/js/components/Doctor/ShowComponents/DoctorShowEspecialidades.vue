<script setup lang="ts">
import type { DoctorShow } from '@/types/doctor';
import type { Especialidad } from '@/types/Especialidad';

defineProps<{
    doctor: DoctorShow;
    especialidades: Especialidad[];
    especialidadesForm: any;
    editando: boolean;
}>();

const emit = defineEmits<{
    'guardar-especialidades': [];
}>();
</script>

<template>
    <!-- ESPECIALIDADES DEL DOCTOR -->
    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300 border-[var(--border)]">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Especialidades
                </p>

                <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                    Especialidades asignadas actualmente al doctor.
                </p>
            </div>

            <button v-if="editando" type="button" :disabled="especialidadesForm.processing"
                class="rounded-2xl bg-[var(--primary)] px-5 py-3 text-[length:var(--font-sm)] font-black text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                @click="emit('guardar-especialidades')">
                {{ especialidadesForm.processing ? 'Guardando...' : 'Guardar especialidades' }}
            </button>
        </div>

        <div v-if="!editando" class="mt-5">
            <div v-if="doctor.especialidades?.length" class="flex flex-wrap gap-3">
                <span v-for="especialidad in doctor.especialidades" :key="especialidad.id"
                    class="rounded-full bg-[var(--primary-soft)] px-4 py-2 text-[length:var(--font-sm)] font-black text-[var(--primary)]">
                    {{ especialidad.nombre }}
                </span>
            </div>

            <div v-else
                class="rounded-2xl border border-dashed p-5 text-center text-[length:var(--font-sm)] font-bold text-[var(--muted)]"
                style="border-color: var(--border)">
                Este doctor todavía no tiene especialidades asignadas.
            </div>
        </div>

        <div v-else class="mt-5">
            <div v-if="especialidades.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <label v-for="especialidad in especialidades" :key="especialidad.id"
                    class="flex cursor-pointer items-start gap-3 rounded-2xl border bg-[var(--bg)] p-4 transition hover:bg-[var(--section-soft)]"
                    style="border-color: var(--border)">
                    <input v-model="especialidadesForm.especialidades" type="checkbox"
                        :value="especialidad.id"
                        class="mt-1 h-5 w-5 rounded border-[var(--border)] text-[var(--primary)] focus:ring-[var(--primary)]" />

                    <div>
                        <p class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                            {{ especialidad.nombre }}
                        </p>

                        <p v-if="especialidad.descripcion"
                            class="mt-1 line-clamp-2 text-[length:var(--font-xs)] text-[var(--muted)]">
                            {{ especialidad.descripcion }}
                        </p>
                    </div>
                </label>
            </div>

            <div v-else
                class="rounded-2xl border border-dashed p-5 text-center text-[length:var(--font-sm)] font-bold text-[var(--muted)]"
                style="border-color: var(--border)">
                No existen especialidades activas para asignar.
            </div>

            <p v-if="especialidadesForm.errors.especialidades"
                class="mt-3 text-[length:var(--font-sm)] font-bold text-red-500">
                {{ especialidadesForm.errors.especialidades }}
            </p>
        </div>
    </div>
</template>
