<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Cita } from '@/types/Cita';

defineProps<{
    citas: Cita[];
    tienePermisoUpdate: boolean;
}>();

const emit = defineEmits<{
    edit: [cita: Cita];
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
    <div class="hidden overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] lg:block"
        style="border-color: var(--border)">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[length:var(--font-sm)]">
                <thead class="bg-[var(--section-soft)] text-[length:var(--font-xs)] uppercase tracking-wider text-[var(--muted)]">
                    <tr>
                        <th class="px-6 py-4 font-black">Paciente</th>
                        <th class="px-6 py-4 font-black">Doctor</th>
                        <th class="px-6 py-4 font-black">Fecha</th>
                        <th class="px-6 py-4 font-black">Horario</th>
                        <th class="px-6 py-4 font-black">Motivo</th>
                        <th class="px-6 py-4 font-black">Estado</th>
                        <th class="px-6 py-4 text-right font-black">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y" style="border-color: var(--border)">
                    <tr v-for="cita in citas" :key="cita.id_cita"
                        class="transition hover:bg-[var(--section-soft)]">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-sm)] font-black text-white">
                                    {{ getInicialesPaciente(cita) }}
                                </div>

                                <div>
                                    <p class="font-black text-[var(--title)]">
                                        {{ getPacienteNombre(cita) }}
                                    </p>

                                    <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                                        Código: {{ cita.paciente_ci }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <p class="font-bold text-[var(--title)]">
                                {{ getDoctorNombre(cita) }}
                            </p>

                            <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                Código: {{ cita.doctor_ci }}
                            </p>
                        </td>

                        <td class="px-6 py-5">
                            <p class="font-black text-[var(--title)]">
                                {{ cita.fecha_cita }}
                            </p>
                        </td>

                        <td class="px-6 py-5">
                            <div class="inline-flex rounded-2xl bg-[var(--primary-soft)] px-4 py-2 font-black text-[var(--primary)]">
                                {{ cita.hora_inicio?.substring(0, 5) }}
                                -
                                {{ cita.hora_fin?.substring(0, 5) }}
                            </div>
                        </td>

                        <td class="max-w-[260px] px-6 py-5">
                            <p class="line-clamp-2 text-[var(--muted)]">
                                {{ cita.motivo || 'Sin motivo registrado.' }}
                            </p>
                        </td>

                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--primary-soft)] px-4 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)]">
                                <span class="h-2 w-2 rounded-full bg-[var(--primary)]"></span>
                                {{ getEstadoNombre(cita) }}
                            </span>
                        </td>

                        <td class="px-6 py-5">
                            <div class="flex justify-end gap-2">
                                <Link :href="route('citas.show', cita.id_cita)"
                                    class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--card)] hover:text-[var(--primary)]"
                                    title="Ver cita">
                                    👁
                                </Link>

                                <button v-if="tienePermisoUpdate"
                                    type="button"
                                    @click="emit('edit', cita)"
                                    class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--card)] hover:text-[var(--primary)]"
                                    title="Editar cita">
                                    ✎
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
