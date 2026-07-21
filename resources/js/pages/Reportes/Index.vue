<script setup lang="ts">
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import ReportesFilters from '@/components/Reportes/ReportesFilters.vue';
import ReportesSummary from '@/components/Reportes/ReportesSummary.vue';
import ReportesCharts from '@/components/Reportes/ReportesCharts.vue';
import ReportesFinanzas from '@/components/Reportes/ReportesFinanzas.vue';
import ReportesServiciosTable from '@/components/Reportes/ReportesServiciosTable.vue';
import ReportesExportModal from '@/components/Reportes/ReportesExportModal.vue';

type Filtros = {
    fecha_inicio: string;
    fecha_fin: string;
    doctor_ci: string | null;
};

type Doctor = {
    codigo_doctor: string;
    nombre_completo: string;
};

type PuntoFecha = { fecha: string; cantidad: number };
type CitaDoctor = { doctor: string; cantidad: number };
type ServicioMasPrestado = { servicio: string; cantidad: number; total: number };
type UsuarioRegistrado = { fecha: string; cantidad: number };
type FinanzasFecha = { fecha: string; total: number; cantidad: number };
type FinanzasEstado = { estado_pago: string; cantidad: number; total: number };
type SolicitudAnalisis = { analisis: string; cantidad: number };
type SolicitudAnalisisDoctor = { doctor: string; cantidad: number };

type AuthUser = { name?: string; nombre_completo?: string };
type PageProps = { auth?: { user?: AuthUser | null } };

const props = defineProps<{
    filtros: Filtros;
    doctores: Doctor[];
    resumen: {
        total_citas: number;
        total_servicios: number;
        total_usuarios: number;
        total_ingresos: number;
        total_pagado: number;
        total_pendiente: number;
        total_solicitudes_analisis: number;
    };
    graficos: {
        citas_por_fecha: PuntoFecha[];
        citas_por_doctor: CitaDoctor[];
        servicios_mas_prestados: ServicioMasPrestado[];
        usuarios_registrados: UsuarioRegistrado[];
        finanzas_por_fecha: FinanzasFecha[];
        finanzas_por_estado: FinanzasEstado[];
        solicitudes_analisis: SolicitudAnalisis[];
        solicitudes_analisis_por_doctor: SolicitudAnalisisDoctor[];
    };
}>();

const page = usePage<PageProps>();
const showExportModal = ref(false);

// --- Helpers ---
const formatoMoney = (value: number) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(Number(value ?? 0));
};

const fechaFormateada = (value: string) => {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;
    return new Intl.DateTimeFormat('es-BO', { year: 'numeric', month: '2-digit', day: '2-digit' }).format(date);
};

const getNombreGenerador = () => {
    return page.props.auth?.user?.nombre_completo || page.props.auth?.user?.name || 'Usuario del sistema';
};

const getNombreDoctorFiltro = () => {
    if (!props.filtros.doctor_ci) return 'Todos los doctores';
    return props.doctores.find((d) => d.codigo_doctor === props.filtros.doctor_ci)?.nombre_completo ?? props.filtros.doctor_ci;
};

const getPeriodoReporte = () => {
    return `${fechaFormateada(props.filtros.fecha_inicio)} - ${fechaFormateada(props.filtros.fecha_fin)}`;
};

// --- Export payload ---
const buildSummaryRows = () => ([
    ['Citas realizadas', String(props.resumen.total_citas)],
    ['Servicios prestados', String(props.resumen.total_servicios)],
    ['Usuarios registrados', String(props.resumen.total_usuarios)],
    ['Ingresos emitidos', formatoMoney(props.resumen.total_ingresos)],
    ['Total pagado', formatoMoney(props.resumen.total_pagado)],
    ['Total pendiente', formatoMoney(props.resumen.total_pendiente)],
    ['Solicitudes de análisis', String(props.resumen.total_solicitudes_analisis)],
]);

const buildReportPayload = () => ({
    clinicName: 'OdontoCool',
    generatedBy: getNombreGenerador(),
    period: getPeriodoReporte(),
    doctor: getNombreDoctorFiltro(),
    filters: props.filtros,
    summary: buildSummaryRows(),
    citasPorFecha: props.graficos.citas_por_fecha,
    citasPorDoctor: props.graficos.citas_por_doctor,
    serviciosMasPrestados: props.graficos.servicios_mas_prestados,
    usuariosRegistrados: props.graficos.usuarios_registrados,
    finanzasPorFecha: props.graficos.finanzas_por_fecha,
    finanzasPorEstado: props.graficos.finanzas_por_estado,
    solicitudesAnalisis: props.graficos.solicitudes_analisis,
    solicitudesAnalisisPorDoctor: props.graficos.solicitudes_analisis_por_doctor,
});

// --- Export functions ---
const exportarExcel = async () => {
    const XLSX = await import('xlsx');
    const workbook = XLSX.utils.book_new();
    const payload = buildReportPayload();

    const summarySheet = XLSX.utils.aoa_to_sheet([
        ['OdontoCool'], ['Reporte general'],
        ['Periodo', payload.period], ['Doctor', payload.doctor],
        ['Generado por', payload.generatedBy], [],
        ['Indicador', 'Valor'], ...payload.summary,
    ]);
    XLSX.utils.book_append_sheet(workbook, summarySheet, 'Resumen');

    const appendTable = (name: string, headers: string[], rows: string[][]) => {
        const sheet = XLSX.utils.aoa_to_sheet([headers, ...rows]);
        XLSX.utils.book_append_sheet(workbook, sheet, name);
    };

    appendTable('Citas_Fecha', ['Fecha', 'Cantidad'], payload.citasPorFecha.map((i) => [i.fecha, String(i.cantidad)]));
    appendTable('Citas_Doctor', ['Doctor', 'Cantidad'], payload.citasPorDoctor.map((i) => [i.doctor, String(i.cantidad)]));
    appendTable('Servicios', ['Servicio', 'Cantidad', 'Total'], payload.serviciosMasPrestados.map((i) => [i.servicio, String(i.cantidad), formatoMoney(i.total)]));
    appendTable('Usuarios', ['Fecha', 'Cantidad'], payload.usuariosRegistrados.map((i) => [i.fecha, String(i.cantidad)]));
    appendTable('Finanzas_Fecha', ['Fecha', 'Total', 'Cantidad'], payload.finanzasPorFecha.map((i) => [i.fecha, formatoMoney(i.total), String(i.cantidad)]));
    appendTable('Finanzas_Estado', ['Estado', 'Cantidad', 'Total'], payload.finanzasPorEstado.map((i) => [i.estado_pago, String(i.cantidad), formatoMoney(i.total)]));
    appendTable('Analisis', ['Análisis', 'Cantidad'], payload.solicitudesAnalisis.map((i) => [i.analisis, String(i.cantidad)]));
    appendTable('Analisis_Doctor', ['Doctor', 'Cantidad'], payload.solicitudesAnalisisPorDoctor.map((i) => [i.doctor, String(i.cantidad)]));

    const buffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `reporte-odontocool-${props.filtros.fecha_inicio}-${props.filtros.fecha_fin}.xlsx`;
    link.click();
    URL.revokeObjectURL(url);
    showExportModal.value = false;
};

const exportarPdf = async () => {
    const [{ default: jsPDF }, autoTable] = await Promise.all([import('jspdf'), import('jspdf-autotable')]);

    const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' }) as InstanceType<typeof jsPDF> & { lastAutoTable?: { finalY?: number } };
    const payload = buildReportPayload();
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 36;

    doc.setFillColor(0, 169, 157);
    doc.rect(0, 0, pageWidth, 92, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(24);
    doc.text('OdontoCool', margin, 34);
    doc.setFontSize(14);
    doc.text('Reporte general del sistema', margin, 56);
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text(`Periodo: ${payload.period}`, margin, 74);
    doc.text(`Doctor: ${payload.doctor}`, margin + 210, 74);
    doc.text(`Generado por: ${payload.generatedBy}`, margin + 420, 74);

    doc.setTextColor(31, 41, 55);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(12);
    doc.text('Resumen ejecutivo', margin, 120);

    autoTable.default(doc, {
        startY: 132, head: [['Indicador', 'Valor']], body: payload.summary, theme: 'grid',
        styles: { fontSize: 10, cellPadding: 6, textColor: [31, 41, 55], lineColor: [203, 213, 225] },
        headStyles: { fillColor: [15, 118, 110], textColor: [255, 255, 255], fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [240, 253, 250] },
    });

    const addTable = (title: string, head: string[][], body: string[][]) => {
        let startY = doc.lastAutoTable?.finalY ? doc.lastAutoTable.finalY + 24 : 150;
        if (startY > doc.internal.pageSize.getHeight() - 120) {
            doc.addPage();
            doc.setFillColor(0, 169, 157);
            doc.rect(0, 0, pageWidth, 36, 'F');
            startY = 150;
        }
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(12);
        doc.setTextColor(31, 41, 55);
        doc.text(title, margin, startY);
        autoTable.default(doc, {
            startY: startY + 10, head, body, theme: 'grid',
            styles: { fontSize: 9, cellPadding: 5, textColor: [31, 41, 55], lineColor: [203, 213, 225] },
            headStyles: { fillColor: [15, 118, 110], textColor: [255, 255, 255], fontStyle: 'bold' },
            alternateRowStyles: { fillColor: [248, 250, 252] },
            margin: { left: margin, right: margin }, pageBreak: 'auto',
        });
    };

    addTable('Citas realizadas por fecha', [['Fecha', 'Cantidad']], payload.citasPorFecha.map((i) => [i.fecha, String(i.cantidad)]));
    addTable('Citas por doctor', [['Doctor', 'Cantidad']], payload.citasPorDoctor.map((i) => [i.doctor, String(i.cantidad)]));
    addTable('Servicios más prestados', [['Servicio', 'Cantidad', 'Total']], payload.serviciosMasPrestados.map((i) => [i.servicio, String(i.cantidad), formatoMoney(i.total)]));
    addTable('Usuarios registrados por fecha', [['Fecha', 'Cantidad']], payload.usuariosRegistrados.map((i) => [i.fecha, String(i.cantidad)]));
    addTable('Ingresos por fecha', [['Fecha', 'Total', 'Cantidad']], payload.finanzasPorFecha.map((i) => [i.fecha, formatoMoney(i.total), String(i.cantidad)]));
    addTable('Finanzas por estado de pago', [['Estado', 'Cantidad', 'Total']], payload.finanzasPorEstado.map((i) => [i.estado_pago, String(i.cantidad), formatoMoney(i.total)]));
    addTable('Solicitudes de análisis por tipo', [['Análisis', 'Cantidad']], payload.solicitudesAnalisis.map((i) => [i.analisis, String(i.cantidad)]));
    addTable('Solicitudes de análisis por doctor', [['Doctor', 'Cantidad']], payload.solicitudesAnalisisPorDoctor.map((i) => [i.doctor, String(i.cantidad)]));

    doc.save(`reporte-odontocool-${props.filtros.fecha_inicio}-${props.filtros.fecha_fin}.pdf`);
    showExportModal.value = false;
};
</script>

<template>
    <Head title="Reportes" />

    <AuthenticatedLayout>
        <template #title>
            Reportes
        </template>

        <section class="space-y-6">
            <!-- Filtros -->
            <ReportesFilters
                :filtros="filtros"
                :doctores="doctores"
                @open-export="showExportModal = true"
            />

            <!-- Resumen KPI -->
            <ReportesSummary :resumen="resumen" />

            <!-- Gráficos principales -->
            <ReportesCharts
                :citas-por-fecha="graficos.citas_por_fecha"
                :citas-por-doctor="graficos.citas_por_doctor"
                :servicios-mas-prestados="graficos.servicios_mas_prestados"
                :usuarios-registrados="graficos.usuarios_registrados"
                :solicitudes-analisis="graficos.solicitudes_analisis"
                :solicitudes-analisis-por-doctor="graficos.solicitudes_analisis_por_doctor"
            />

            <!-- Reporte financiero -->
            <ReportesFinanzas
                :resumen="resumen"
                :finanzas-por-fecha="graficos.finanzas_por_fecha"
                :finanzas-por-estado="graficos.finanzas_por_estado"
            />

            <!-- Tabla de servicios -->
            <ReportesServiciosTable :servicios="graficos.servicios_mas_prestados" />
        </section>

        <!-- Modal de exportación -->
        <ReportesExportModal
            :show="showExportModal"
            :periodo="getPeriodoReporte()"
            :doctor="getNombreDoctorFiltro()"
            @close="showExportModal = false"
            @export-pdf="exportarPdf"
            @export-excel="exportarExcel"
        />
    </AuthenticatedLayout>
</template>