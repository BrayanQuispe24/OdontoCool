<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Cita } from '@/types/Cita';

const props = defineProps<{
    cita: Cita;
}>();

const getPacienteNombre = () => {
    if (!props.cita.paciente) return 'Sin paciente';

    return `${props.cita.paciente.persona?.nombre ?? ''} ${props.cita.paciente.persona?.apellido ?? ''}`.trim();
};

const getDoctorNombre = () => {
    if (!props.cita.doctor) return 'Sin doctor';

    return `${props.cita.doctor.persona?.nombre ?? ''} ${props.cita.doctor.persona?.apellido ?? ''}`.trim();
};

const getSecretariaNombre = () => {
    if (!props.cita.secretaria) return 'Sin secretaria asignada';

    return `${props.cita.secretaria.persona?.nombre ?? ''} ${props.cita.secretaria.persona?.apellido ?? ''}`.trim();
};

const getEstadoNombre = () => {
    return props.cita.ultimo_estado_asignado?.estado_cita?.nombre ?? 'Sin estado';
};

const getInicialesPaciente = () => {
    const nombre = getPacienteNombre();

    if (nombre === 'Sin paciente') return 'SP';

    return nombre
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word[0])
        .join('')
        .toUpperCase();
};

const formatHora = (hora?: string | null) => {
    return hora ? hora.substring(0, 5) : '--:--';
};

const formatDato = (dato?: string | number | null) => {
    return dato ?? 'No registrado';
};
</script>

<template>
    <Head title="Detalle de cita" />

    <AuthenticatedLayout>
        <template #title>
            Detalle de cita
        </template>

        <section class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Header -->
            <div
                class="overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
                style="border-color: var(--border)"
            >
                <div class="relative p-6 md:p-8">
                    <div class="absolute right-0 top-0 h-32 w-32 rounded-bl-full bg-[var(--primary-soft)]"></div>

                    <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-start gap-5">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-[var(--primary)] text-[length:var(--font-2xl)] font-black text-white shadow-[0_15px_35px_rgba(0,169,157,0.25)]">
                                {{ getInicialesPaciente() }}
                            </div>

                            <div>
                                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                                    Cita médica
                                </p>

                                <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                                    {{ getPacienteNombre() }}
                                </h1>

                                <p class="mt-2 max-w-2xl text-[length:var(--font-sm)] leading-6 text-[var(--muted)]">
                                    {{ cita.motivo || 'Sin motivo registrado.' }}
                                </p>

                                <div class="mt-4 flex flex-wrap gap-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--primary-soft)] px-4 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)]">
                                        <span class="h-2 w-2 rounded-full bg-[var(--primary)]"></span>
                                        {{ getEstadoNombre() }}
                                    </span>

                                    <span class="inline-flex rounded-full bg-[var(--section-soft)] px-4 py-2 text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                                        Código cita: {{ cita.id_cita }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <Link
                                :href="route('citas.index')"
                                class="rounded-2xl border bg-[var(--card)] px-6 py-3 text-center text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)]"
                                style="border-color: var(--border)"
                            >
                                ← Volver
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen rápido -->
            <div class="grid gap-5 md:grid-cols-3">
                <article
                    class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)]"
                    style="border-color: var(--border)"
                >
                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Fecha
                    </p>

                    <h2 class="mt-2 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                        {{ cita.fecha_cita }}
                    </h2>
                </article>

                <article
                    class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)]"
                    style="border-color: var(--border)"
                >
                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Hora inicio
                    </p>

                    <h2 class="mt-2 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                        {{ formatHora(cita.hora_inicio) }}
                    </h2>
                </article>

                <article
                    class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)]"
                    style="border-color: var(--border)"
                >
                    <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                        Hora fin
                    </p>

                    <h2 class="mt-2 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                        {{ formatHora(cita.hora_fin) }}
                    </h2>
                </article>
            </div>

            <!-- Información de la cita -->
            <div
                class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)]"
                style="border-color: var(--border)"
            >
                <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    Información de la cita
                </h2>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Motivo
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ cita.motivo || 'No registrado' }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Estado actual
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ getEstadoNombre() }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4 md:col-span-2">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Observación
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] leading-6 text-[var(--title)]">
                            {{ cita.observacion || 'Sin observaciones registradas.' }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Código historial
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ formatDato(cita.codigo_historial) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Secretaria
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ getSecretariaNombre() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Doctor y Secretaria -->
            <div class="grid gap-6 xl:grid-cols-2">
                <!-- Doctor asignado -->
                <div
                    class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)]"
                    style="border-color: var(--border)"
                >
                    <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                        Doctor asignado
                    </h2>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                            <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                Nombre
                            </p>

                            <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                                {{ getDoctorNombre() }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                            <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                Código doctor
                            </p>

                            <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                                {{ cita.doctor_ci }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                            <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                Matrícula profesional
                            </p>

                            <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                                {{ formatDato(cita.doctor?.matricula_profesional) }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                            <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                Teléfono profesional
                            </p>

                            <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                                {{ formatDato(cita.doctor?.telefono_profesional) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Secretaria responsable -->
                <div
                    class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)]"
                    style="border-color: var(--border)"
                >
                    <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                        Secretaria responsable
                    </h2>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                            <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                Nombre
                            </p>

                            <p class="mt-1 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                                {{ getSecretariaNombre() }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                            <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                Código secretaria
                            </p>

                            <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                                {{ formatDato(cita.secretaria?.codigo_secretaria) }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                            <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                Teléfono
                            </p>

                            <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                                {{ formatDato(cita.secretaria?.persona?.telefono) }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                            <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                Fecha contratación
                            </p>

                            <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                                {{ formatDato(cita.secretaria?.fecha_contratacion) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paciente -->
            <div
                class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)]"
                style="border-color: var(--border)"
            >
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                            Paciente
                        </p>

                        <h2 class="mt-1 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                            {{ getPacienteNombre() }}
                        </h2>
                    </div>

                    <span class="rounded-2xl bg-[var(--primary-soft)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--primary)]">
                        Código: {{ cita.paciente_ci }}
                    </span>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            CI
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ formatDato(cita.paciente?.persona?.carnet_identidad) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Teléfono
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ formatDato(cita.paciente?.persona?.telefono) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Género
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ formatDato(cita.paciente?.persona?.genero) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Fecha nacimiento
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ formatDato(cita.paciente?.persona?.fecha_nacimiento) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4 md:col-span-2">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Dirección
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ formatDato(cita.paciente?.persona?.direccion) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Contacto emergencia
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ formatDato(cita.paciente?.contacto_emergencia) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--section-soft)] p-4">
                        <p class="text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Teléfono emergencia
                        </p>

                        <p class="mt-1 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ formatDato(cita.paciente?.telefono_emergencia) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Historial de estados -->
            <div
                class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)]"
                style="border-color: var(--border)"
            >
                <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    Historial de estados
                </h2>

                <div
                    v-if="cita.asignaciones_estado_cita?.length"
                    class="mt-5 overflow-hidden rounded-2xl border"
                    style="border-color: var(--border)"
                >
                    <table class="w-full text-left text-[length:var(--font-sm)]">
                        <thead class="bg-[var(--section-soft)] text-[length:var(--font-xs)] uppercase tracking-wider text-[var(--muted)]">
                            <tr>
                                <th class="px-5 py-4 font-black">Estado</th>
                                <th class="px-5 py-4 font-black">Observación</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y" style="border-color: var(--border)">
                            <tr
                                v-for="asignacion in cita.asignaciones_estado_cita"
                                :key="`${asignacion.cita_id}-${asignacion.estado_cita_id}-${asignacion.observacion}`"
                                class="transition hover:bg-[var(--section-soft)]"
                            >
                                <td class="px-5 py-4 font-black text-[var(--title)]">
                                    {{ asignacion.estado_cita?.nombre ?? 'Sin estado' }}
                                </td>

                                <td class="px-5 py-4 text-[var(--muted)]">
                                    {{ asignacion.observacion ?? 'Sin observación' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-else
                    class="mt-5 rounded-2xl border border-dashed p-6 text-center text-[length:var(--font-sm)] font-bold text-[var(--muted)]"
                    style="border-color: var(--border)"
                >
                    No hay historial de estados registrado.
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>