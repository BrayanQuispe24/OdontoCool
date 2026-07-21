<script setup lang="ts">
import { router } from '@inertiajs/vue3';

type Filtros = {
    fecha_inicio: string;
    fecha_fin: string;
    doctor_ci: string | null;
};

type Doctor = {
    codigo_doctor: string;
    nombre_completo: string;
};

defineProps<{
    filtros: Filtros;
    doctores: Doctor[];
}>();

const emit = defineEmits<{
    'open-export': [];
}>();

const aplicarFiltros = (event: Event) => {
    const form = event.target as HTMLFormElement;
    const data = new FormData(form);

    router.get(
        route('reportes.index'),
        {
            fecha_inicio: data.get('fecha_inicio'),
            fecha_fin: data.get('fecha_fin'),
            doctor_ci: data.get('doctor_ci') || null,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const limpiarFiltros = () => {
    router.get(
        route('reportes.index'),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
        <div class="mb-4">
            <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                Panel de reportes
            </p>

            <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                Generar reportes del sistema
            </h2>

            <p class="mt-1 text-[length:var(--font-sm)] text-[var(--muted)]">
                Filtra la información por rango de fechas y, si deseas, por doctor.
            </p>
        </div>

        <form class="grid grid-cols-1 gap-4 md:grid-cols-4" @submit.prevent="aplicarFiltros">
            <div>
                <label class="mb-2 block text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                    Fecha inicio
                </label>

                <input name="fecha_inicio" type="date" :value="filtros.fecha_inicio"
                    class="w-full rounded-2xl border bg-[var(--input-bg)] px-4 py-3 text-[length:var(--font-sm)] font-bold text-[var(--input-text)] outline-none transition focus:ring-4 focus:ring-[var(--primary-soft)] border-[var(--border)]" />
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                    Fecha fin
                </label>

                <input name="fecha_fin" type="date" :value="filtros.fecha_fin"
                    class="w-full rounded-2xl border bg-[var(--input-bg)] px-4 py-3 text-[length:var(--font-sm)] font-bold text-[var(--input-text)] outline-none transition focus:ring-4 focus:ring-[var(--primary-soft)] border-[var(--border)]" />
            </div>

            <div>
                <label class="mb-2 block text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                    Doctor
                </label>

                <select name="doctor_ci" :value="filtros.doctor_ci ?? ''"
                    class="w-full rounded-2xl border bg-[var(--input-bg)] px-4 py-3 text-[length:var(--font-sm)] font-bold text-[var(--input-text)] outline-none transition focus:ring-4 focus:ring-[var(--primary-soft)] border-[var(--border)]">
                    <option value="">Todos los doctores</option>
                    <option v-for="doctor in doctores" :key="doctor.codigo_doctor" :value="doctor.codigo_doctor">
                        {{ doctor.nombre_completo }}
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 rounded-2xl bg-[var(--primary)] px-4 py-3 text-[length:var(--font-sm)] font-black text-white transition hover:bg-[var(--primary-hover)]">
                    Generar
                </button>

                <button type="button"
                    class="rounded-2xl border bg-[var(--section-soft)] px-4 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--primary-soft)] border-[var(--border)]"
                    @click="limpiarFiltros">
                    Limpiar
                </button>

                <button type="button"
                    class="rounded-2xl border border-sky-600 bg-sky-50 px-4 py-3 text-[length:var(--font-sm)] font-black text-sky-700 transition hover:bg-sky-100"
                    @click="emit('open-export')">
                    Exportar
                </button>
            </div>
        </form>
    </div>
</template>
