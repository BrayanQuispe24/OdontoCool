<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Bitacora } from '@/types/Bitacora';

const props = defineProps<{
    bitacora: Bitacora;
}>();

const getIniciales = (nombre?: string, apellido?: string) => {
    const n = nombre?.charAt(0) ?? '';
    const a = apellido?.charAt(0) ?? '';
    return `${n}${a}`.toUpperCase() || 'SYS';
};

const getBrowserAndOS = (userAgent?: string | null) => {
    if (!userAgent) return 'Desconocido';
    let os = 'Desconocido';
    let browser = 'Desconocido';

    if (userAgent.includes('Windows')) os = 'Windows';
    else if (userAgent.includes('Macintosh') || userAgent.includes('Mac OS')) os = 'macOS';
    else if (userAgent.includes('Linux')) os = 'Linux';
    else if (userAgent.includes('Android')) os = 'Android';
    else if (userAgent.includes('iPhone') || userAgent.includes('iPad')) os = 'iOS';

    if (userAgent.includes('Edg')) browser = 'Edge';
    else if (userAgent.includes('Chrome')) browser = 'Chrome';
    else if (userAgent.includes('Firefox')) browser = 'Firefox';
    else if (userAgent.includes('Safari')) browser = 'Safari';

    return `${browser} on ${os}`;
};

const isUpdate = computed(() => {
    return props.bitacora.detalles && typeof props.bitacora.detalles === 'object' && 'cambios' in props.bitacora.detalles;
});

const isError = computed(() => {
    return props.bitacora.detalles && typeof props.bitacora.detalles === 'object' && 'exception' in props.bitacora.detalles;
});

const hasAtributos = computed(() => {
    return props.bitacora.detalles && typeof props.bitacora.detalles === 'object' && 'atributos' in props.bitacora.detalles;
});

const isLoginEvent = computed(() => {
    return props.bitacora.modulo === 'AUTENTICACIÓN';
});

// Translation dictionary for database field names to friendly Spanish titles
const friendlyFieldNames: Record<string, string> = {
    // General
    id: 'ID del Registro',
    estado: 'Estado',
    created_at: 'Creado el',
    updated_at: 'Última actualización',
    
    // Tratamiento
    objetivo_tratamiento: 'Objetivo del Tratamiento',
    observacion: 'Observación',
    fecha_inicio: 'Fecha de Inicio',
    fecha_fin_estimada: 'Fecha Fin Estimada',
    fecha_fin_real: 'Fecha Fin Real',
    diagnostico_id: 'ID Diagnóstico',
    codigo_historial: 'Código de Historial Clínico',

    // Citas
    fecha_cita: 'Fecha de la Cita',
    hora_inicio: 'Hora de Inicio',
    hora_fin: 'Hora de Fin',
    motivo: 'Motivo de Consulta',
    paciente_ci: 'Cédula Paciente',
    secretaria_ci: 'Cédula Secretaria',
    doctor_ci: 'Cédula Doctor',
    estado_cita_id: 'ID Estado de la Cita',

    // Historial
    alergias: 'Alergias del Paciente',
    antecedentes_medicos: 'Antecedentes Médicos',
    enfermedades_base: 'Enfermedades de Base',
    motivo_apertura: 'Motivo de Apertura',
    fecha_apertura: 'Fecha de Apertura',
    fecha_actualizacion: 'Fecha de Actualización',
    observaciones_generales: 'Observaciones Generales',

    // Boletas
    descuento: 'Descuento Aplicado',
    fecha_emision: 'Fecha de Emisión',
    total: 'Monto Total Facturado',
    estado_pago: 'Estado del Pago',
    id_modo_pago: 'ID Modo de Pago',

    // Cuotas
    numero_cuota: 'Número de Cuota',
    monto_cuota: 'Monto de la Cuota',
    fecha_vencimiento: 'Fecha de Vencimiento',
    fecha_pago: 'Fecha de Pago',
    id_transaccion: 'ID Transacción Financiera',
    comprobante: 'Archivo del Comprobante',
    id_metodo_pago: 'ID Método de Pago',
    id_boleta: 'ID Boleta Asociada',
};

const getFriendlyName = (field: string | number) => {
    const key = String(field);
    return friendlyFieldNames[key] || key;
};
</script>

<template>
    <Head title="Detalle de Auditoría" />

    <AuthenticatedLayout>
        <template #title>
            Detalle de Auditoría
        </template>

        <section class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- ENCABEZADO -->
            <div class="overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
                style="border-color: var(--border)">
                <div class="h-32 bg-gradient-to-r from-[var(--primary)] via-[#18dccf] to-[#bcefeb]"></div>

                <div class="relative p-6">
                    <div class="-mt-20 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                        <div class="flex flex-col gap-4 md:flex-row md:items-end">
                            <div class="relative">
                                <div class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-3xl border-4 bg-[var(--primary)] text-[length:var(--font-3xl)] font-black text-white shadow-xl"
                                    style="border-color: var(--card)">
                                    <span>
                                        {{ getIniciales(bitacora.persona?.nombre, bitacora.persona?.apellido) }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                                    Auditoría de Bitácora
                                </p>

                                <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                                    {{ bitacora.persona ? `${bitacora.persona.nombre} ${bitacora.persona.apellido}` : 'Sistema / Admin' }}
                                </h1>

                                <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                                    Módulo afectado: <span class="font-bold text-[var(--title)]">{{ bitacora.modulo }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <Link :href="route('bitacora.index')"
                                class="rounded-2xl border px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--bg)]"
                                style="border-color: var(--border)">
                                Volver al Listado
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETALLES DE LA ACCIÓN -->
            <div class="grid gap-6 md:grid-cols-3">
                <!-- Información General -->
                <div class="md:col-span-1 space-y-6">
                    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
                        style="border-color: var(--border)">
                        <h2 class="text-[length:var(--font-lg)] font-black text-[var(--title)] border-b pb-3 mb-4" style="border-color: var(--border)">
                            Datos del Evento
                        </h2>

                        <div class="space-y-4 text-[length:var(--font-sm)]">
                            <div>
                                <span class="block font-bold text-[var(--muted)] uppercase text-[length:var(--font-xs)]">Usuario</span>
                                <p class="text-[var(--title)] font-semibold">
                                    {{ bitacora.persona ? `${bitacora.persona.nombre} ${bitacora.persona.apellido}` : 'Sistema / Admin' }}
                                </p>
                                <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                    CI: {{ bitacora.persona_id || 'Acción de Sistema' }}
                                </p>
                            </div>

                            <div>
                                <span class="block font-bold text-[var(--muted)] uppercase text-[length:var(--font-xs)]">Fecha</span>
                                <p class="text-[var(--title)] font-semibold">
                                    {{ bitacora.fecha.toString().substring(0, 10) }}
                                </p>
                            </div>

                            <div>
                                <span class="block font-bold text-[var(--muted)] uppercase text-[length:var(--font-xs)]">Hora</span>
                                <p class="text-[var(--title)] font-semibold">
                                    {{ bitacora.hora }}
                                </p>
                            </div>

                            <div>
                                <span class="block font-bold text-[var(--muted)] uppercase text-[length:var(--font-xs)]">Módulo</span>
                                <span class="inline-block mt-1 rounded-xl bg-[var(--primary-soft)] px-3 py-1 text-[length:var(--font-xs)] font-black text-[var(--primary)] uppercase">
                                    {{ bitacora.modulo }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Dispositivo e IP -->
                    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
                        style="border-color: var(--border)">
                        <h2 class="text-[length:var(--font-lg)] font-black text-[var(--title)] border-b pb-3 mb-4" style="border-color: var(--border)">
                            Dispositivo y Conexión
                        </h2>

                        <div class="space-y-4 text-[length:var(--font-sm)]">
                            <div>
                                <span class="block font-bold text-[var(--muted)] uppercase text-[length:var(--font-xs)]">Dirección IP</span>
                                <p class="text-[var(--title)] font-semibold">
                                    {{ bitacora.ip_address || 'No registrada' }}
                                </p>
                            </div>

                            <div>
                                <span class="block font-bold text-[var(--muted)] uppercase text-[length:var(--font-xs)]">Sistema / Navegador</span>
                                <p class="text-[var(--title)] font-semibold">
                                    {{ getBrowserAndOS(bitacora.user_agent) }}
                                </p>
                            </div>

                            <div>
                                <span class="block font-bold text-[var(--muted)] uppercase text-[length:var(--font-xs)]">User Agent Completo</span>
                                <p class="text-[length:var(--font-xs)] text-[var(--text)] break-all leading-relaxed bg-[var(--section-soft)] p-3 rounded-2xl border" style="border-color: var(--border)">
                                    {{ bitacora.user_agent || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalle de la Acción y Cambios (Auditor-friendly payload) -->
                <div class="md:col-span-2 space-y-6">
                    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
                        style="border-color: var(--border)">
                        <h2 class="text-[length:var(--font-lg)] font-black text-[var(--title)] border-b pb-3 mb-4" style="border-color: var(--border)">
                            Acción Auditada
                        </h2>

                        <div class="rounded-2xl border bg-[var(--section-soft)] p-5 text-[length:var(--font-base)] font-bold text-[var(--title)]" style="border-color: var(--border)">
                            {{ bitacora.accion }}
                        </div>
                    </div>

                    <!-- Auditoría Amigable de Cambios / Payload -->
                    <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
                        style="border-color: var(--border)">
                        <h2 class="text-[length:var(--font-lg)] font-black text-[var(--title)] border-b pb-3 mb-4" style="border-color: var(--border)">
                            Detalles de la Auditoría
                        </h2>

                        <!-- 1. CASO DE ERROR DE SISTEMA -->
                        <div v-if="isError" class="space-y-4">
                            <div class="rounded-2xl border border-red-200 dark:border-red-900/30 bg-red-50/40 dark:bg-red-950/20 p-5">
                                <h3 class="font-black text-red-600 dark:text-red-400 text-[length:var(--font-sm)] uppercase tracking-wider mb-2">
                                    Detalle del Error Detectado
                                </h3>
                                <p class="text-sm font-semibold text-[var(--title)] mb-3 break-words">
                                    {{ bitacora.detalles.message }}
                                </p>
                                <div class="text-xs text-[var(--muted)] space-y-1 font-mono">
                                    <p><span class="font-bold text-[var(--title)]">Tipo:</span> {{ bitacora.detalles.exception }}</p>
                                    <p><span class="font-bold text-[var(--title)]">Archivo:</span> {{ bitacora.detalles.file }}</p>
                                    <p><span class="font-bold text-[var(--title)]">Línea:</span> {{ bitacora.detalles.line }}</p>
                                </div>
                            </div>

                            <details class="group">
                                <summary class="flex cursor-pointer select-none items-center justify-between rounded-2xl bg-[var(--section-soft)] p-4 text-sm font-bold text-[var(--title)] hover:opacity-90">
                                    <span>Ver Traza del Error (Stack Trace)</span>
                                    <span class="transition group-open:rotate-180">▼</span>
                                </summary>
                                <pre class="mt-3 overflow-x-auto rounded-2xl border bg-[var(--section-soft)] p-4 text-[length:var(--font-xs)] font-mono text-[var(--text)] max-h-96" style="border-color: var(--border)">{{ bitacora.detalles.trace }}</pre>
                            </details>
                        </div>

                        <!-- 2. CASO DE MODIFICACIÓN (Comparativa de Cambios) -->
                        <div v-else-if="isUpdate" class="space-y-6">
                            <div class="rounded-2xl bg-amber-50/40 dark:bg-amber-950/15 border border-amber-200 dark:border-amber-900/30 p-4 text-xs font-semibold text-amber-700 dark:text-amber-400">
                                ℹ️ A continuación se listan únicamente los campos modificados en esta acción.
                            </div>

                            <div class="overflow-hidden rounded-2xl border" style="border-color: var(--border)">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-[var(--section-soft)] text-xs uppercase text-[var(--muted)]">
                                            <tr>
                                                <th class="px-4 py-3 font-bold">Campo Modificado</th>
                                                <th class="px-4 py-3 font-bold">Valor Anterior</th>
                                                <th class="px-4 py-3 font-bold">Valor Nuevo</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y" style="border-color: var(--border)">
                                            <tr v-for="(newVal, field) in bitacora.detalles.cambios" :key="field" class="hover:bg-[var(--section-soft)] transition">
                                                <td class="px-4 py-3.5 font-bold text-[var(--title)]">
                                                    {{ getFriendlyName(field) }}
                                                    <span class="block text-[10px] font-medium text-[var(--muted)] font-mono">{{ field }}</span>
                                                </td>
                                                <td class="px-4 py-3.5 text-red-600 dark:text-red-400 font-mono break-all max-w-[200px]" :title="bitacora.detalles.original?.[field] ?? 'N/A'">
                                                    {{ bitacora.detalles.original?.[field] === null ? 'N/A' : (bitacora.detalles.original?.[field] === '' ? '(Vacío)' : bitacora.detalles.original?.[field]) }}
                                                </td>
                                                <td class="px-4 py-3.5 text-green-600 dark:text-green-400 font-mono break-all max-w-[200px]" :title="newVal">
                                                    {{ newVal === null ? 'N/A' : (newVal === '' ? '(Vacío)' : newVal) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 3. CASO DE LOGIN/AUTENTICACIÓN -->
                        <div v-else-if="isLoginEvent" class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div v-for="(val, key) in bitacora.detalles" :key="key" class="rounded-2xl border p-4 bg-[var(--section-soft)]" style="border-color: var(--border)">
                                    <span class="block text-xs font-bold text-[var(--muted)] uppercase tracking-wider">{{ String(key).replace('_', ' ') }}</span>
                                    <p class="text-sm font-semibold text-[var(--title)] mt-1 font-mono">
                                        {{ typeof val === 'boolean' ? (val ? 'Sí' : 'No') : val }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- 4. CASO DE CREACIÓN / ELIMINACIÓN (Atributos completos del registro) -->
                        <div v-else-if="hasAtributos" class="space-y-4">
                            <h3 class="text-xs font-bold text-[var(--muted)] uppercase tracking-wider">
                                Atributos del Registro afectado (ID: {{ bitacora.detalles.modelo_id }})
                            </h3>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div v-for="(val, field) in bitacora.detalles.atributos" :key="field" class="rounded-2xl border p-4 bg-[var(--section-soft)] hover:bg-[var(--bg)] transition" style="border-color: var(--border)">
                                    <span class="block text-xs font-bold text-[var(--muted)] uppercase">
                                        {{ getFriendlyName(field) }}
                                    </span>
                                    <p class="text-sm font-semibold text-[var(--title)] mt-1 font-mono break-words">
                                        {{ val === null ? 'N/A' : (val === '' ? '(Vacío)' : val) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- 5. CASO POR DEFECTO / CARGA RAW DE DATOS -->
                        <div v-else>
                            <pre class="overflow-x-auto rounded-3xl bg-[var(--section-soft)] p-6 text-[length:var(--font-xs)] font-mono text-[var(--text)] border max-h-[500px]" style="border-color: var(--border)">{{ 
                                bitacora.detalles 
                                    ? JSON.stringify(bitacora.detalles, null, 2)
                                    : '// No hay datos adicionales registrados para este evento.'
                            }}</pre>
                        </div>

                        <!-- SECCIÓN EXPANSIBLE: payload original JSON completo por si el auditor técnico lo requiere -->
                        <div v-if="bitacora.detalles" class="mt-8 pt-6 border-t" style="border-color: var(--border)">
                            <details class="group">
                                <summary class="flex cursor-pointer select-none items-center gap-2 text-xs font-bold text-[var(--muted)] hover:text-[var(--title)]">
                                    <span>🔍 Ver datos JSON estructurados completos (Payload Técnico)</span>
                                    <span class="transition group-open:rotate-180">▼</span>
                                </summary>
                                <pre class="mt-4 overflow-x-auto rounded-2xl border bg-[var(--section-soft)] p-4 text-[length:var(--font-xs)] font-mono text-[var(--text)] max-h-72" style="border-color: var(--border)">{{ JSON.stringify(bitacora.detalles, null, 2) }}</pre>
                            </details>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
