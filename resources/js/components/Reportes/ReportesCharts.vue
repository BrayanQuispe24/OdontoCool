<script setup lang="ts">
import { computed } from 'vue';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    LineElement,
    PointElement,
    ArcElement,
    CategoryScale,
    LinearScale,
} from 'chart.js';
import { Bar, Line } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, BarElement, LineElement, PointElement, ArcElement, CategoryScale, LinearScale);

type PuntoFecha = { fecha: string; cantidad: number };
type CitaDoctor = { doctor: string; cantidad: number };
type ServicioMasPrestado = { servicio: string; cantidad: number; total: number };
type UsuarioRegistrado = { fecha: string; cantidad: number };
type SolicitudAnalisis = { analisis: string; cantidad: number };
type SolicitudAnalisisDoctor = { doctor: string; cantidad: number };

const props = defineProps<{
    citasPorFecha: PuntoFecha[];
    citasPorDoctor: CitaDoctor[];
    serviciosMasPrestados: ServicioMasPrestado[];
    usuariosRegistrados: UsuarioRegistrado[];
    solicitudesAnalisis: SolicitudAnalisis[];
    solicitudesAnalisisPorDoctor: SolicitudAnalisisDoctor[];
}>();

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' as const },
    },
};

const citasFechaData = computed(() => ({
    labels: props.citasPorFecha.map((i) => i.fecha),
    datasets: [{ label: 'Citas realizadas', data: props.citasPorFecha.map((i) => Number(i.cantidad)) }],
}));

const citasDoctorData = computed(() => ({
    labels: props.citasPorDoctor.map((i) => i.doctor),
    datasets: [{ label: 'Citas por doctor', data: props.citasPorDoctor.map((i) => Number(i.cantidad)) }],
}));

const serviciosData = computed(() => ({
    labels: props.serviciosMasPrestados.map((i) => i.servicio),
    datasets: [{ label: 'Cantidad prestada', data: props.serviciosMasPrestados.map((i) => Number(i.cantidad)) }],
}));

const usuariosData = computed(() => ({
    labels: props.usuariosRegistrados.map((i) => i.fecha),
    datasets: [{ label: 'Usuarios registrados', data: props.usuariosRegistrados.map((i) => Number(i.cantidad)) }],
}));

const analisisData = computed(() => ({
    labels: props.solicitudesAnalisis.map((i) => i.analisis),
    datasets: [{ label: 'Solicitudes por análisis', data: props.solicitudesAnalisis.map((i) => Number(i.cantidad)) }],
}));

const analisisDoctorData = computed(() => ({
    labels: props.solicitudesAnalisisPorDoctor.map((i) => i.doctor),
    datasets: [{ label: 'Solicitudes por doctor', data: props.solicitudesAnalisisPorDoctor.map((i) => Number(i.cantidad)) }],
}));
</script>

<template>
    <!-- CITAS Y SERVICIOS -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <article class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
            <h3 class="mb-4 text-[length:var(--font-base)] font-black text-[var(--title)]">
                Citas realizadas por fecha
            </h3>
            <div class="h-[320px]">
                <Line :data="citasFechaData" :options="chartOptions" />
            </div>
        </article>

        <article class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
            <h3 class="mb-4 text-[length:var(--font-base)] font-black text-[var(--title)]">
                Citas por doctor
            </h3>
            <div class="h-[320px]">
                <Bar :data="citasDoctorData" :options="chartOptions" />
            </div>
        </article>

        <article class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
            <h3 class="mb-4 text-[length:var(--font-base)] font-black text-[var(--title)]">
                Servicios más prestados
            </h3>
            <div class="h-[320px]">
                <Bar :data="serviciosData" :options="chartOptions" />
            </div>
        </article>

        <article class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
            <h3 class="mb-4 text-[length:var(--font-base)] font-black text-[var(--title)]">
                Usuarios registrados por fecha
            </h3>
            <div class="h-[320px]">
                <Line :data="usuariosData" :options="chartOptions" />
            </div>
        </article>
    </div>

    <!-- SOLICITUDES DE ANÁLISIS -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <article class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
            <h3 class="mb-4 text-[length:var(--font-base)] font-black text-[var(--title)]">
                Solicitudes de análisis por tipo
            </h3>
            <div class="h-[320px]">
                <Bar :data="analisisData" :options="chartOptions" />
            </div>
        </article>

        <article class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
            <h3 class="mb-4 text-[length:var(--font-base)] font-black text-[var(--title)]">
                Solicitudes de análisis por doctor
            </h3>
            <div class="h-[320px]">
                <Bar :data="analisisDoctorData" :options="chartOptions" />
            </div>
        </article>
    </div>
</template>
