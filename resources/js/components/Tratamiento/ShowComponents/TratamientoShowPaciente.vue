<script setup lang="ts">
import { Tratamiento } from '@/types/Tratamiento';

defineProps<{
    tratamiento: Tratamiento;
}>();
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
            Paciente
        </p>
        <h2 class="mt-1 text-[length:var(--font-2xl)] font-black text-[var(--title)] mb-4">
            Información del Paciente
        </h2>
        
        <div
            class="grid gap-6 md:grid-cols-4 bg-[var(--section-soft)] border p-5 rounded-2xl transition-colors duration-300"
            style="border-color: var(--border)"
        >
            <div>
                <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Nombre Completo</p>
                <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                    {{ tratamiento.historial_clinico?.paciente?.persona?.nombre }} {{ tratamiento.historial_clinico?.paciente?.persona?.apellido }}
                </p>
            </div>
            <div>
                <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Carnet de Identidad</p>
                <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                    {{ tratamiento.historial_clinico?.paciente?.persona?.carnet_identidad || tratamiento.historial_clinico?.paciente_ci }}
                </p>
            </div>
            <div>
                <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Teléfono / Celular</p>
                <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                    {{ tratamiento.historial_clinico?.paciente?.persona?.telefono || 'No registrado' }}
                </p>
            </div>
            <div>
                <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Historial Clínico</p>
                <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                    Ficha #{{ tratamiento.codigo_historial }}
                </p>
            </div>
            <div
                class="md:col-span-2 border-t pt-3"
                style="border-color: var(--border)"
            >
                <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Dirección de Domicilio</p>
                <p class="mt-1 text-[length:var(--font-sm)] text-[var(--text)]">
                    {{ tratamiento.historial_clinico?.paciente?.persona?.direccion || 'No registrada' }}
                </p>
            </div>
            <div
                class="border-t pt-3"
                style="border-color: var(--border)"
            >
                <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Contacto de Emergencia</p>
                <p class="mt-1 text-[length:var(--font-sm)] text-[var(--text)]">
                    {{ tratamiento.historial_clinico?.paciente?.contacto_emergencia || 'No registrado' }}
                </p>
            </div>
            <div
                class="border-t pt-3"
                style="border-color: var(--border)"
            >
                <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Teléfono de Emergencia</p>
                <p class="mt-1 text-[length:var(--font-sm)] text-[var(--text)]">
                    {{ tratamiento.historial_clinico?.paciente?.telefono_emergencia || 'No registrado' }}
                </p>
            </div>
        </div>
    </div>
</template>
