<script setup lang="ts">
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { HistorialClinico } from '@/types/HistorialClinico';
import type { PageProps } from '@/types';

import HistorialClinicoShowHeader from '@/components/HistorialClinico/ShowComponents/HistorialClinicoShowHeader.vue';
import HistorialClinicoShowPatientCard from '@/components/HistorialClinico/ShowComponents/HistorialClinicoShowPatientCard.vue';
import HistorialClinicoShowApertura from '@/components/HistorialClinico/ShowComponents/HistorialClinicoShowApertura.vue';
import HistorialClinicoShowOdonto from '@/components/HistorialClinico/ShowComponents/HistorialClinicoShowOdonto.vue';
import HistorialClinicoShowCitas from '@/components/HistorialClinico/ShowComponents/HistorialClinicoShowCitas.vue';
import HistorialClinicoShowTratamientos from '@/components/HistorialClinico/ShowComponents/HistorialClinicoShowTratamientos.vue';

const { historial } = defineProps<{
    historial: HistorialClinico;
}>();

const page = usePage<PageProps>();
const authUser = computed(() => page.props.auth.user);
const permisos = computed<string[]>(() => page.props.auth.permisos ?? []);

const fechaImpresion = new Intl.DateTimeFormat('es-BO', {
    dateStyle: 'full',
    timeStyle: 'short',
}).format(new Date());

const formatoFecha = (value?: string | null) => {
    if (!value) return 'No registrado';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;
    return new Intl.DateTimeFormat('es-BO', {
        dateStyle: 'medium',
    }).format(date);
};

const nombreCompleto = (persona?: { nombre?: string | null; apellido?: string | null } | null) => {
    const nombre = `${persona?.nombre ?? ''} ${persona?.apellido ?? ''}`.trim();
    return nombre || 'No registrado';
};

const tieneRol = (rol: string) => {
    return authUser.value?.rol?.nombre === rol;
};

const exportarPdf = async () => {
    const [{ default: jsPDF }, { default: autoTable }] = await Promise.all([
        import('jspdf'),
        import('jspdf-autotable'),
    ]);

    const doc = new jsPDF({ orientation: 'p', unit: 'mm', format: 'a4' }) as InstanceType<typeof jsPDF> & {
        lastAutoTable?: { finalY?: number };
    };

    const pacienteNombre = nombreCompleto(historial.paciente?.persona);
    const citaConDoctor = historial.citas?.find((cita) => Boolean(cita.doctor));
    const doctorResponsable = citaConDoctor?.doctor?.persona
        ? nombreCompleto(citaConDoctor.doctor.persona)
        : 'No registrado';
    const generadoPor = authUser.value?.nombre_completo || authUser.value?.name || 'Usuario del sistema';

    const colors = {
        primary: [0, 169, 157] as [number, number, number],
        primaryDark: [0, 122, 113] as [number, number, number],
        title: [17, 24, 39] as [number, number, number],
        muted: [107, 114, 128] as [number, number, number],
        border: [203, 213, 225] as [number, number, number],
        soft: [240, 253, 250] as [number, number, number],
    };

    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const marginX = 12;

    const drawPageChrome = (pageNumber: number) => {
        doc.setFillColor(...colors.primary);
        doc.rect(0, 0, pageWidth, 24, 'F');

        doc.setTextColor(255, 255, 255);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(15);
        doc.text('OdontoCool', marginX, 10);

        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.text(`Historial clínico #${historial.codigo_historial}`, marginX, 16);
        doc.text(`Paciente: ${pacienteNombre}`, 92, 16);
        doc.text(`Fecha: ${fechaImpresion}`, 145, 16);

        doc.setTextColor(...colors.title);
        doc.setDrawColor(...colors.border);
        doc.setLineWidth(0.3);
        doc.line(marginX, 28, pageWidth - marginX, 28);

        doc.setFontSize(8);
        doc.setTextColor(...colors.muted);
        doc.text(`Página ${pageNumber}`, pageWidth - marginX, pageHeight - 8, { align: 'right' });
    };

    const sectionTitle = (title: string, subtitle?: string) => {
        const startY = (doc.lastAutoTable?.finalY ?? 32) + 10;

        if (startY > pageHeight - 20) {
            doc.addPage();
            drawPageChrome(doc.getNumberOfPages());
            doc.setPage(doc.getNumberOfPages());
        }

        const y = doc.lastAutoTable?.finalY ? doc.lastAutoTable.finalY + 10 : 34;

        doc.setTextColor(...colors.primaryDark);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(11);
        doc.text(title, marginX, y);

        if (subtitle) {
            doc.setTextColor(...colors.muted);
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(8);
            doc.text(subtitle, marginX, y + 4);
        }

        return subtitle ? y + 7 : y + 4;
    };

    const renderTable = (title: string, head: string[][], body: string[][], subtitle?: string) => {
        const startY = sectionTitle(title, subtitle);

        autoTable(doc, {
            startY,
            margin: { left: marginX, right: marginX, top: 32, bottom: 18 },
            head,
            body,
            theme: 'grid',
            styles: {
                fontSize: 8,
                cellPadding: 2.2,
                textColor: colors.title,
                lineColor: colors.border,
            },
            headStyles: {
                fillColor: colors.primaryDark,
                textColor: [255, 255, 255],
                fontStyle: 'bold',
            },
            alternateRowStyles: {
                fillColor: colors.soft,
            },
            didDrawPage: () => {
                drawPageChrome(doc.getNumberOfPages());
            },
        });
    };

    drawPageChrome(1);

    renderTable(
        'Datos del paciente',
        [['Campo', 'Detalle']],
        [
            ['Nombre completo', pacienteNombre],
            ['Cédula de identidad', historial.paciente_ci || 'No registrado'],
            ['Contacto de emergencia', historial.paciente?.contacto_emergencia || 'No registrado'],
            ['Teléfono de emergencia', historial.paciente?.telefono_emergencia || 'No registrado'],
            ['Estado de la ficha', historial.estado || 'No registrado'],
        ],
    );

    renderTable(
        'Condición de salud base',
        [['Campo', 'Detalle']],
        [
            ['Alergias', historial.alergias || 'Ninguna alergia registrada.'],
            ['Patologías crónicas / base', historial.enfermedades_base || 'Sin enfermedades de base.'],
            ['Antecedentes médicos generales', historial.antecedentes_medicos || 'No registra antecedentes médicos relevantes.'],
        ],
    );

    renderTable(
        'Apertura de ficha',
        [['Campo', 'Detalle']],
        [
            ['Motivo de apertura', historial.motivo_apertura || 'No registrado'],
            ['Fecha de apertura', formatoFecha(historial.fecha_apertura)],
            ['Fecha de actualización', formatoFecha(historial.fecha_actualizacion)],
            ['Doctor responsable', doctorResponsable],
            ['Observaciones generales', historial.observaciones_generales || 'Sin observaciones'],
            ['Generado por', generadoPor],
            ['Fecha de generación', fechaImpresion],
        ],
    );

    if (historial.antecedentes_odontologicos) {
        renderTable(
            'Antecedentes odontológicos previos',
            [['Campo', 'Detalle']],
            [
                ['Fecha de registro', formatoFecha(historial.antecedentes_odontologicos.fecha_registro)],
                ['Observación general', historial.antecedentes_odontologicos.observacion_general || 'Sin observación general'],
            ],
        );

        const detalles = historial.antecedentes_odontologicos.detalle_antecedentes ?? [];

        if (detalles.length > 0) {
            renderTable(
                'Detalle de tratamientos odontológicos',
                [['Tratamiento', 'Fecha', 'Lugar / Clínica', 'Descripción', 'Observación']],
                detalles.map((detalle) => [
                    detalle.nombre_tratamiento || 'No registrado',
                    formatoFecha(detalle.fecha_tratamiento),
                    detalle.lugar_tratamiento || 'No registrado',
                    detalle.descripcion || 'Sin descripción',
                    detalle.observacion || 'Sin observación',
                ]),
            );
        }
    }

    const citas = historial.citas ?? [];

    if (citas.length > 0) {
        renderTable(
            'Historial de citas',
            [['#', 'Fecha', 'Horario', 'Motivo', 'Doctor', 'Diagnóstico']],
            citas.map((cita) => [
                String(cita.id_cita ?? ''),
                formatoFecha(cita.fecha_cita),
                `${cita.hora_inicio || '--:--'} - ${cita.hora_fin || '--:--'}`,
                cita.motivo || 'Sin motivo',
                cita.doctor ? nombreCompleto(cita.doctor.persona) : 'Sin doctor',
                cita.diagnostico
                    ? `${cita.diagnostico.tipo_diagnostico || 'Sin tipo'} | ${cita.diagnostico.gravedad || 'Sin gravedad'}`
                    : 'Sin diagnóstico',
            ]),
        );
    }

    const tratamientos = historial.tratamiento ?? [];

    if (tratamientos.length > 0) {
        renderTable(
            'Tratamientos clínicos',
            [['#', 'Objetivo', 'Periodo', 'Estado', 'Observación']],
            tratamientos.map((tratamiento) => [
                String(tratamiento.id ?? ''),
                tratamiento.objetivo_tratamiento || 'Sin objetivo',
                `${formatoFecha(tratamiento.fecha_inicio)} al ${formatoFecha(tratamiento.fecha_fin_estimada)}`,
                tratamiento.estado || 'Sin estado',
                tratamiento.observacion || 'Sin observación',
            ]),
        );
    }

    const totalPages = doc.getNumberOfPages();

    for (let pageIndex = 1; pageIndex <= totalPages; pageIndex += 1) {
        doc.setPage(pageIndex);
        drawPageChrome(pageIndex);
    }

    doc.save(`historial-clinico-${historial.codigo_historial}.pdf`);
};
</script>

<template>
    <Head :title="`Ficha Clínica - ${historial.paciente?.persona?.nombre} ${historial.paciente?.persona?.apellido}`" />

    <AuthenticatedLayout>
        <template #title> Ficha Clínica Completa </template>

        <div class="historial-print space-y-6 p-4 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300 sm:p-6 lg:p-8">
            <!-- Print layout header (visible during print only) -->
            <div class="print-only hidden rounded-3xl border border-[var(--border)] bg-white px-6 py-5 shadow-sm">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <p class="text-[12px] font-black uppercase tracking-[0.28em] text-[var(--primary)]">
                            OdontoCool
                        </p>
                        <h2 class="mt-1 text-[22px] font-black text-slate-900">
                            Historial Clínico
                        </h2>
                        <p class="mt-1 text-[12px] text-slate-600">
                            Expediente de {{ historial.paciente?.persona?.nombre }} {{ historial.paciente?.persona?.apellido }}
                        </p>
                    </div>

                    <div class="text-right text-[11px] text-slate-600">
                        <p class="font-bold text-slate-800">Código: #{{ historial.codigo_historial }}</p>
                        <p>Fecha de impresión: {{ fechaImpresion }}</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 border-t border-slate-200 pt-4 md:grid-cols-3">
                    <div>
                        <span class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-500">Paciente</span>
                        <p class="mt-1 text-[13px] font-bold text-slate-900">{{ historial.paciente?.persona?.nombre }} {{ historial.paciente?.persona?.apellido }}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-500">CI</span>
                        <p class="mt-1 text-[13px] font-bold text-slate-900">{{ historial.paciente_ci }}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-500">Estado</span>
                        <p class="mt-1 text-[13px] font-bold text-slate-900">{{ historial.estado }}</p>
                    </div>
                </div>
            </div>

            <!-- Back Button and Action Headers -->
            <HistorialClinicoShowHeader
                :historial="historial"
                :tiene-rol-paciente="tieneRol('Paciente')"
                @export-pdf="exportarPdf"
            />

            <!-- Main clinical content grid -->
            <div class="grid gap-6 lg:grid-cols-3 print:grid-cols-1">
                <!-- Patient Card Details and Conditions (left column) -->
                <HistorialClinicoShowPatientCard :historial="historial" />

                <!-- Timeline and Dental Records (right columns) -->
                <div class="space-y-6 lg:col-span-2 print:col-span-1">
                    <!-- Apertura Motive -->
                    <HistorialClinicoShowApertura :historial="historial" />

                    <!-- Dental History Records -->
                    <HistorialClinicoShowOdonto :historial="historial" />

                    <!-- Timeline Appointments & Active Treatments -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Appointments history -->
                        <HistorialClinicoShowCitas :historial="historial" />

                        <!-- Active clinical treatments -->
                        <HistorialClinicoShowTratamientos :historial="historial" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
