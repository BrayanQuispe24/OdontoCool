<script setup lang="ts">
import { Tratamiento } from '@/types/Tratamiento';
import { Link } from '@inertiajs/vue3';

defineProps<{
    tratamientos: Tratamiento[];
    canShow: boolean;
    canUpdate: boolean;
    canDestroy: boolean;
    isDoctor: boolean;
}>();

const emit = defineEmits<{
    'edit': [tratamiento: Tratamiento];
    'delete': [id: number];
    'restore': [id: number];
}>();

const getIniciales = (nombre?: string, apellido?: string) => {
    const n = nombre?.charAt(0) ?? '';
    const a = apellido?.charAt(0) ?? '';
    return `${n}${a}`.toUpperCase() || 'TR';
};
</script>

<template>
    <div class="space-y-6">
        <!-- Tabla para desktop -->
        <div class="hidden overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 lg:block"
            style="border-color: var(--border)">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[length:var(--font-sm)]">
                    <thead
                        class="bg-[var(--section-soft)] text-[length:var(--font-xs)] uppercase tracking-wider text-[var(--muted)]">
                        <tr>
                            <th class="px-6 py-4 font-black">Paciente</th>
                            <th class="px-6 py-4 font-black">Diagnóstico</th>
                            <th class="px-6 py-4 font-black">Objetivo</th>
                            <th class="px-6 py-4 font-black">Fechas</th>
                            <th class="px-6 py-4 font-black">Estado</th>
                            <th class="px-6 py-4 text-right font-black">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y" style="border-color: var(--border)">
                        <tr v-for="tratamiento in tratamientos" :key="tratamiento.id"
                            class="transition hover:bg-[var(--section-soft)]">

                            <!-- Paciente -->
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-sm)] font-black text-white shrink-0">
                                        {{
                                            getIniciales(tratamiento.historial_clinico?.paciente?.persona?.nombre,
                                        tratamiento.historial_clinico?.paciente?.persona?.apellido) }}
                                    </div>

                                    <div>
                                        <p class="font-black text-[var(--title)]">
                                            {{ tratamiento.historial_clinico?.paciente?.persona?.nombre }} {{
                                                tratamiento.historial_clinico?.paciente?.persona?.apellido }}
                                        </p>

                                        <p class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                                            Historial #{{ tratamiento.codigo_historial }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Diagnóstico -->
                            <td class="px-6 py-5">
                                <div v-if="tratamiento.diagnostico" class="max-w-xs">
                                    <p class="font-bold text-[var(--title)]">
                                        {{ tratamiento.diagnostico.tipo_diagnostico }}
                                    </p>
                                    <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                        Gravedad: {{ tratamiento.diagnostico.gravedad }}
                                    </p>
                                </div>
                                <span v-else class="text-[var(--muted)] italic">Sin diagnóstico</span>
                            </td>

                            <!-- Objetivo -->
                            <td class="px-6 py-5 max-w-xs">
                                <p class="font-semibold text-[var(--title)] truncate"
                                    :title="tratamiento.objetivo_tratamiento">
                                    {{ tratamiento.objetivo_tratamiento }}
                                </p>
                                <p class="text-[length:var(--font-xs)] text-[var(--muted)] truncate"
                                    :title="tratamiento.observacion">
                                    {{ tratamiento.observacion || 'Sin observaciones.' }}
                                </p>
                            </td>

                            <!-- Fechas -->
                            <td class="px-6 py-5 text-[length:var(--font-xs)] text-[var(--text)]">
                                <p>📅 <span class="font-bold">Inicio:</span> {{ tratamiento.fecha_inicio }}</p>
                                <p>🏁 <span class="font-bold">Est. Fin:</span> {{ tratamiento.fecha_fin_estimada
                                    }}</p>
                                <p v-if="tratamiento.fecha_fin_real">✅ <span class="font-bold">Real:</span> {{
                                    tratamiento.fecha_fin_real }}</p>
                            </td>

                            <!-- Estado -->
                            <td class="px-6 py-5">
                                <span v-if="tratamiento.estado === 'ACTIVO'"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-[var(--primary-soft)] px-3 py-1.5 text-[length:var(--font-xs)] font-black text-[var(--primary)]">
                                    <span class="h-2 w-2 rounded-full bg-[var(--primary)]"></span>
                                    Activo
                                </span>
                                <span v-else
                                    class="inline-flex items-center gap-1.5 rounded-full bg-red-50 dark:bg-red-950/30 px-3.5 py-1.5 text-[length:var(--font-xs)] font-black text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/30">
                                    <span class="h-2 w-2 rounded-full bg-red-600"></span>
                                    Inactivo
                                </span>
                            </td>

                            <!-- Acciones -->
                            <td class="px-6 py-5">
                                <div class="flex justify-end gap-2">
                                    <Link v-if="canShow"
                                        :href="route('tratamiento.show', tratamiento.id)"
                                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--card)] hover:text-[var(--primary)] text-lg"
                                        title="Ver Detalles">
                                        👁
                                    </Link>

                                    <button v-if="canUpdate" type="button"
                                        @click="emit('edit', tratamiento)"
                                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--card)] hover:text-[var(--primary)] text-lg"
                                        title="Editar Tratamiento">
                                        ✏️
                                    </button>

                                    <button
                                        v-if="tratamiento.estado !== 'ACTIVO' && canDestroy && isDoctor"
                                        type="button" @click="emit('restore', tratamiento.id)"
                                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--card)] hover:text-emerald-500 text-lg"
                                        title="Restaurar Tratamiento">
                                        ↩
                                    </button>

                                    <button
                                        v-else-if="tratamiento.estado === 'ACTIVO' && canDestroy"
                                        type="button" @click="emit('delete', tratamiento.id)"
                                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--card)] hover:text-red-500 text-lg"
                                        title="Inactivar Tratamiento">
                                        ✕
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tarjetas para móvil/tablet -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:hidden">
            <article v-for="tratamiento in tratamientos" :key="tratamiento.id"
                class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)]"
                style="border-color: var(--border)">
                <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"></div>

                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div>
                        <div class="mb-4 flex items-start justify-between">
                            <div
                                class="flex h-12 w-14 shrink-0 items-center justify-center rounded-xl bg-[var(--primary)] text-[length:var(--font-lg)] font-black text-white shadow-md shadow-teal-100">
                                TR
                            </div>

                            <span v-if="tratamiento.estado === 'ACTIVO'"
                                class="inline-flex items-center gap-1 rounded-full bg-[var(--primary-soft)] px-3 py-1 text-[length:var(--font-xs)] font-black text-[var(--primary)]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[var(--primary)]"></span>
                                Activo
                            </span>

                            <span v-else
                                class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-[length:var(--font-xs)] font-black text-red-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>
                                Inactivo
                            </span>
                        </div>

                        <div v-if="tratamiento.historial_clinico?.paciente?.persona"
                            class="mb-1 text-[length:var(--font-xs)] font-black text-[var(--primary)]">
                            Paciente: {{ tratamiento.historial_clinico.paciente.persona.nombre }} {{
                                tratamiento.historial_clinico.paciente.persona.apellido }}
                            <span class="font-normal text-[var(--muted)]">
                                | Historial #{{ tratamiento.codigo_historial }}
                            </span>
                        </div>

                        <div v-if="tratamiento.diagnostico"
                            class="mb-3 rounded-xl border bg-[var(--section-soft)] p-2.5 text-[length:var(--font-xs)] font-bold text-[var(--title)]"
                            style="border-color: var(--border)">
                            🩺 Diagnóstico: {{ tratamiento.diagnostico.tipo_diagnostico }} ({{
                            tratamiento.diagnostico.gravedad }})
                        </div>

                        <h2 class="line-clamp-1 text-[length:var(--font-lg)] font-black text-[var(--title)]">
                            {{ tratamiento.objetivo_tratamiento }}
                        </h2>

                        <div class="mt-2 space-y-1 text-[length:var(--font-xs)] text-[var(--muted)]">
                            <p>
                                📅 <span class="font-bold">Inicio:</span>
                                {{ tratamiento.fecha_inicio }}
                            </p>

                            <p>
                                🏁 <span class="font-bold">Est. Fin:</span>
                                {{ tratamiento.fecha_fin_estimada }}
                            </p>

                            <p v-if="tratamiento.fecha_fin_real">
                                ✅ <span class="font-bold">Real Fin:</span>
                                {{ tratamiento.fecha_fin_real }}
                            </p>
                        </div>

                        <p class="mt-3 line-clamp-2 text-[length:var(--font-xs)] italic text-[var(--muted)]">
                            "{{ tratamiento.observacion || 'Sin observaciones registradas.' }}"
                        </p>

                        <div class="mt-4 border-t pt-3" style="border-color: var(--border)">
                            <p
                                class="mb-2 text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                                Dientes tratados ({{
                                    tratamiento.tratamientos_dientes
                                        ?.length || 0
                                }})
                            </p>

                            <div class="flex flex-wrap gap-1">
                                <span v-for="td in tratamiento.tratamientos_dientes" :key="td.id"
                                    class="inline-block rounded-lg border bg-[var(--section-soft)] px-2 py-1 text-[length:var(--font-xs)] font-extrabold text-[var(--primary)]"
                                    style="border-color: var(--border)"
                                    :title="`${td.diente?.nombre} - ${td.cara_dental}`">
                                    🦷 #{{ td.diente?.numero }} ({{ td.cara_dental }})
                                </span>

                                <span v-if="!tratamiento.tratamientos_dientes?.length"
                                    class="text-[length:var(--font-xs)] italic text-[var(--muted)]">
                                    Ninguno
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between border-t pt-3"
                        style="border-color: var(--border)">
                        <Link :href="route('tratamiento.show', tratamiento.id)"
                            class="text-[length:var(--font-xs)] font-black text-[var(--primary)] hover:underline">
                            Ver Detalles →
                        </Link>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="emit('edit', tratamiento)"
                                class="rounded-lg p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)]"
                                title="Editar Tratamiento">
                                ✎
                            </button>

                            <button v-if="tratamiento.estado !== 'ACTIVO'" type="button"
                                @click="emit('restore', tratamiento.id)"
                                class="rounded-lg p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-emerald-500"
                                title="Restaurar Tratamiento">
                                ↩
                            </button>

                            <button v-else type="button" @click="emit('delete', tratamiento.id)"
                                class="rounded-lg p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-red-500"
                                title="Inactivar Tratamiento">
                                🗑
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>
