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
import { Line, Doughnut } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, BarElement, LineElement, PointElement, ArcElement, CategoryScale, LinearScale);

type FinanzasFecha = { fecha: string; total: number; cantidad: number };
type FinanzasEstado = { estado_pago: string; cantidad: number; total: number };

const props = defineProps<{
    resumen: {
        total_ingresos: number;
        total_pagado: number;
        total_pendiente: number;
    };
    finanzasPorFecha: FinanzasFecha[];
    finanzasPorEstado: FinanzasEstado[];
}>();

const formatoMoney = (value: number) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(Number(value ?? 0));
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' as const },
    },
};

const finanzasFechaData = computed(() => ({
    labels: props.finanzasPorFecha.map((i) => i.fecha),
    datasets: [{ label: 'Ingresos emitidos', data: props.finanzasPorFecha.map((i) => Number(i.total)) }],
}));

const finanzasEstadoData = computed(() => ({
    labels: props.finanzasPorEstado.map((i) => i.estado_pago),
    datasets: [{ label: 'Total por estado', data: props.finanzasPorEstado.map((i) => Number(i.total)) }],
}));
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
        <div class="mb-5">
            <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.2em] text-[var(--primary)]">
                Finanzas
            </p>

            <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
                Reporte financiero
            </h2>

            <p class="mt-1 text-[length:var(--font-sm)] text-[var(--muted)]">
                Resumen de boletas emitidas, pagadas y pendientes.
            </p>
        </div>

        <!-- KPI financieros -->
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <article class="rounded-2xl bg-[var(--section-soft)] p-4">
                <p class="text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                    Total emitido
                </p>
                <h3 class="mt-2 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ formatoMoney(resumen.total_ingresos) }}
                </h3>
            </article>

            <article class="rounded-2xl bg-[var(--section-soft)] p-4">
                <p class="text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                    Total pagado
                </p>
                <h3 class="mt-2 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ formatoMoney(resumen.total_pagado) }}
                </h3>
            </article>

            <article class="rounded-2xl bg-[var(--section-soft)] p-4">
                <p class="text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                    Total pendiente
                </p>
                <h3 class="mt-2 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    {{ formatoMoney(resumen.total_pendiente) }}
                </h3>
            </article>
        </div>

        <!-- Gráficos financieros -->
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div>
                <h3 class="mb-4 text-[length:var(--font-base)] font-black text-[var(--title)]">
                    Ingresos por fecha
                </h3>
                <div class="h-[320px]">
                    <Line :data="finanzasFechaData" :options="chartOptions" />
                </div>
            </div>

            <div>
                <h3 class="mb-4 text-[length:var(--font-base)] font-black text-[var(--title)]">
                    Finanzas por estado de pago
                </h3>
                <div class="h-[320px]">
                    <Doughnut :data="finanzasEstadoData" :options="chartOptions" />
                </div>
            </div>
        </div>
    </div>
</template>
