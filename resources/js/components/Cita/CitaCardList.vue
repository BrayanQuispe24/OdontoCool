<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Cita } from '@/types/Cita';

defineProps<{
    citas: Cita[];
    tienePermisoUpdate: boolean;
    tienePermisoDestroy: boolean;
}>();

const emit = defineEmits<{
    edit: [cita: Cita];
    delete: [id: number];
}>();

const getPacienteNombre = (cita: Cita) => {
    if (!cita.paciente) return 'Sin paciente';
    return `${cita.paciente.persona?.nombre ?? ''} ${cita.paciente.persona?.apellido ?? ''}`.trim();
};

const getDoctorNombre = (cita: Cita) => {
    if (!cita.doctor) return 'Sin doctor';
    return `${cita.doctor.persona?.nombre ?? ''} ${cita.doctor.persona?.apellido ?? ''}`.trim();
};

const getEstadoNombre = (cita: Cita) => {
    return cita.ultimo_estado_asignado?.estado_cita?.nombre ?? 'Sin estado';
};

const getInicialesPaciente = (cita: Cita) => {
    const nombre = getPacienteNombre(cita);
    if (nombre === 'Sin paciente') return 'SP';

    return nombre
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word[0])
        .join('')
        .toUpperCase();
};
</script>

<template>
    <div class="grid grid-cols-1 gap-5 lg:hidden">
        <article v-for="cita in citas" :key="cita.id_cita"
            class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)]"
            style="border-color: var(--border)">
            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"></div>

            <div class="relative z-10">
                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-xl)] font-black text-white shadow-lg shadow-teal-100">
                    {{ getInicialesPaciente(cita) }}
                </div>

                <span class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--primary)]">
                    {{ cita.fecha_cita }}
                </span>

                <h2 class="mt-1 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ getPacienteNombre(cita) }}
                </h2>

                <p class="mt-2 line-clamp-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                    {{ cita.motivo || 'Sin motivo registrado.' }}
                </p>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Inicio
                        </p>

                        <p class="mt-1 text-[length:var(--font-lg)] font-black text-[var(--title)]">
                            {{ cita.hora_inicio?.substring(0, 5) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Fin
                        </p>

                        <p class="mt-1 text-[length:var(--font-lg)] font-black text-[var(--title)]">
                            {{ cita.hora_fin?.substring(0, 5) }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 border-t pt-4" style="border-color: var(--border)">
                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Doctor asignado
                    </p>

                    <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        {{ getDoctorNombre(cita) }}
                    </p>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--primary-soft)] px-4 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)]">
                        <span class="h-2 w-2 rounded-full bg-[var(--primary)]"></span>
                        {{ getEstadoNombre(cita) }}
                    </span>

                    <div class="flex items-center gap-3">
                        <Link :href="route('citas.show', cita.id_cita)"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)]"
                            title="Ver cita">
                            👁
                        </Link>

                        <button v-if="tienePermisoUpdate"
                            type="button"
                            @click="emit('edit', cita)"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)]"
                            title="Editar cita">
                            ✎
                        </button>

                        <button v-if="tienePermisoDestroy"
                            type="button"
                            @click="emit('delete', cita.id_cita)"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500"
                            title="Eliminar cita">
                            🗑
                        </button>
                    </div>
                </div>
            </div>
        </article>
    </div>
</template>
