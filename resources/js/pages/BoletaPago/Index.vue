<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ModoPago } from '@/types/ModoPago';
import { MetodoPago } from '@/types/MetodoPago';
import { Paciente } from '@/types/Paciente';
import { Secretaria } from '@/types/Secretaria';
import { Tratamiento as BaseTratamiento } from '@/types/Tratamiento';
import { ServicioPrestado } from '@/types/ServicioPrestado';
import { BoletaPago } from '@/types/BoletaPago';
import { PageProps } from '@/types';

interface Tratamiento extends BaseTratamiento {
    servicio_prestado?: ServicioPrestado[];
}

const props = defineProps<{
    boletas: BoletaPago[];
    modoPagos: ModoPago[];
    metodoPagos: MetodoPago[];
    pacientes: Paciente[];
    secretarias: Secretaria[];
    serviciosDisponibles: any[];
    tratamientosDisponibles: Tratamiento[];
    error?: string;
}>();

const page = usePage<PageProps>();

const authUser = computed(() => page.props.auth.user);
const permisos = computed<string[]>(() => page.props.auth.permisos ?? []);
const tieneRol = (rol: string) => {
    return authUser.value?.rol?.nombre === rol;
};

const activeTab = ref<'list' | 'create'>('list');
const search = ref('');
const expandedBoletaId = ref<number | null>(null);

// Forms
const form = useForm({
    descuento: 0,
    id_modo_pago: '',
    paciente_ci: '',
    secretaria_ci: '',
    servicio_prestado_ids: [] as number[],
    tipo_credito: 'meses',
    cantidad_cuotas: 1,
});

const selectedTratamientoId = ref<number | null>(null);
const selectedTratamiento = computed(() => {
    if (!selectedTratamientoId.value) return null;
    return props.tratamientosDisponibles.find(t => t.id === selectedTratamientoId.value) || null;
});

// Watch treatment selection to auto-fill patient & select all services
const handleTratamientoChange = () => {
    const t = selectedTratamiento.value;
    if (t) {
        form.paciente_ci = t.historial_clinico?.paciente_ci || '';
        form.servicio_prestado_ids = (t.servicio_prestado || []).map(s => s.id);
    } else {
        form.paciente_ci = '';
        form.servicio_prestado_ids = [];
    }
};

const handleServiceToggle = (id: number) => {
    const idx = form.servicio_prestado_ids.indexOf(id);
    if (idx > -1) {
        form.servicio_prestado_ids.splice(idx, 1);
    } else {
        form.servicio_prestado_ids.push(id);
    }
};

// Calculations
const selectedServicesObjects = computed(() => {
    const t = selectedTratamiento.value;
    if (!t || !t.servicio_prestado) return [];
    return t.servicio_prestado.filter(s => form.servicio_prestado_ids.includes(s.id));
});

const calculatedSubtotal = computed(() => {
    return selectedServicesObjects.value.reduce((total, s) => total + parseFloat(s.subtotal as any), 0);
});

const calculatedTotal = computed(() => {
    return Math.max(0, calculatedSubtotal.value - (form.descuento || 0));
});

const isCreditSelected = computed(() => {
    const mode = props.modoPagos.find(m => m.id_modo_pago === Number(form.id_modo_pago));
    return mode?.nombre === 'CREDITO';
});

// Payment modals / receipt states
const paymentModalOpen = ref(false);
const activeInstallment = ref<{ boletaId: number; nroCuota: number; monto: number } | null>(null);
const paymentForm = useForm({
    id_boleta: 0,
    numero_cuota: 0,
    id_metodo_pago: '',
    comprobante: null as File | null,
});

const openPaymentModal = (boletaId: number, nroCuota: number, monto: number) => {
    activeInstallment.value = { boletaId, nroCuota, monto };
    paymentForm.id_boleta = boletaId;
    paymentForm.numero_cuota = nroCuota;
    paymentForm.id_metodo_pago = props.metodoPagos[0]?.id_metodo_pago ? String(props.metodoPagos[0].id_metodo_pago) : '';
    paymentForm.comprobante = null;
    paymentModalOpen.value = true;
};

const handleReceiptFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        paymentForm.comprobante = target.files[0];
    }
};

const submitPayment = () => {
    paymentForm.post(route('boleta_pago.pagar'), {
        preserveScroll: true,
        onSuccess: () => {
            paymentModalOpen.value = false;
            activeInstallment.value = null;
        }
    });
};

// QR Payment Modal States
const qrModalOpen = ref(false);
const qrBase64 = ref<string | null>(null);
const qrTransactionId = ref<string | null>(null);
const qrCheckoutUrl = ref<string | null>(null);
const qrInstallmentId = ref<number | null>(null);
const qrChecking = ref(false);
const qrError = ref<string | null>(null);
const isQrZoomed = ref(false);

const generateQR = async (idCuota: number) => {
    qrError.value = null;
    qrBase64.value = null;
    qrModalOpen.value = true;
    qrInstallmentId.value = idCuota;

    try {
        const response = await fetch(route('boleta_pago.generar_qr', { id_cuota: idCuota }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
            }
        });
        const data = await response.json();
        if (data.success) {
            qrBase64.value = data.qr_base64;
            qrTransactionId.value = data.transaction_id;
            qrCheckoutUrl.value = data.checkout_url;
        } else {
            qrError.value = data.message || 'Error al generar el código QR.';
        }
    } catch (e: any) {
        qrError.value = e.message || 'Error de conexión al servidor.';
    }
};

const verifyQRStatus = () => {
    if (!qrInstallmentId.value) return;
    qrChecking.value = true;
    qrError.value = null;

    router.post(route('boleta_pago.verificar_qr', { id_cuota: qrInstallmentId.value }), {}, {
        preserveScroll: true,
        onSuccess: () => {
            const flash = page.props.flash as any;
            if (flash?.estado === 'PAGADO') {
                qrModalOpen.value = false;
                qrInstallmentId.value = null;
            } else {
                qrError.value = flash?.success || flash?.error || 'El pago aún se encuentra PENDIENTE de procesamiento.';
            }
        },
        onError: (errors) => {
            qrError.value = Object.values(errors).join('\n') || 'Error de comunicación al consultar el estado de la transacción.';
        },
        onFinish: () => {
            qrChecking.value = false;
        }
    });
};

const verifyQRStatusDirect = (idCuota: number) => {
    router.post(route('boleta_pago.verificar_qr', { id_cuota: idCuota }), {}, {
        preserveScroll: true,
    });
};

// Filtered bills
const filteredBoletas = computed(() => {
    return props.boletas.filter(b => {
        const term = search.value.trim().toLowerCase();
        if (!term) return true;

        const pName = `${b.paciente?.persona?.nombre} ${b.paciente?.persona?.apellido}`.toLowerCase();
        const ci = (b.paciente_ci || '').toLowerCase();
        const idStr = String(b.id_boleta);

        if (pName.includes(term) || ci.includes(term)) return true;
        if (idStr.includes(term) || term.includes(idStr)) return true;

        // Strip common prefixes like "boleta", "folio", "#"
        const cleanTerm = term.replace(/^(boleta\s*#?|folio\s*#?|#)/g, '').trim();
        return cleanTerm !== '' && idStr.includes(cleanTerm);
    });
});

const submitCreateBoleta = () => {
    form.post(route('boleta_pago.store'), {
        onSuccess: () => {
            form.reset();
            selectedTratamientoId.value = null;
            activeTab.value = 'list';
        }
    });
};

const deleteBoleta = (id: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta boleta de pago? Todos los servicios asociados volverán a quedar sin facturar.')) {
        router.delete(route('boleta_pago.destroy', id), {
            preserveScroll: true,
        });
    }
};

const toggleExpandBoleta = (id: number) => {
    expandedBoletaId.value = expandedBoletaId.value === id ? null : id;
};

const canPayCuota = (boleta: any, cuota: any) => {
    if (cuota.numero_cuota === 1) return true;
    const prevCuota = boleta.cuota_boleta?.find((c: any) => c.numero_cuota === cuota.numero_cuota - 1);
    if (!prevCuota) return false;
    return prevCuota.estado === 'PAGADO' || prevCuota.estado === 'REVISIÓN' || prevCuota.estado === 'REVISION';
};

const formatPath = (path?: string) => {
    if (!path) return '';
    const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';
    const base = publicBase.replace(/\/+$/, '');
    const cleanPath = path.replace('public/', '').replace(/^\/+/, '');
    return `${base}/storage/${cleanPath}`;
};

onMounted(() => {
    const page = usePage<any>();
    const flashedQr = page.props.flash?.qr_base64;
    const flashedCuotaId = page.props.flash?.qr_cuota_id;
    if (flashedQr && flashedCuotaId) {
        qrBase64.value = flashedQr;
        qrInstallmentId.value = Number(flashedCuotaId);
        qrModalOpen.value = true;
    }
});
</script>

<template>
    <Head title="Facturación" />

    <AuthenticatedLayout>
        <template #title> Facturación y Pagos </template>

        <section class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            <!-- Header Card -->
            <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300" style="border-color: var(--border)">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                            Facturación
                        </p>
                        <h1 class="mt-2 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                            {{tieneRol('Paciente')?'Mis boletas de pago':'Boletas de Pago'}}
                        </h1>
                        <p class="mt-2 max-w-2xl text-[length:var(--font-sm)] leading-6 text-[var(--muted)]">
                            {{tieneRol('Paciente')?'Visualiza y gestiona tus boletas de pago emitidas por la clínica.':'Gestiona la emisión y control de boletas de pago para los pacientes de la clínica.'}}
                        </p>
                    </div>

                    <div class="flex gap-2" v-if="!tieneRol('Paciente')">
                        <button
                            type="button"
                            @click="activeTab = 'list'"
                            class="rounded-2xl px-5 py-3 text-[length:var(--font-sm)] font-black transition"
                            :class="activeTab === 'list' ? 'bg-[var(--text)] text-[var(--card)]' : 'bg-[var(--section-soft)] text-[var(--text)] hover:bg-[var(--primary-soft)]'"
                        >
                            📋 Boletas Emitidas
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'create'"
                            class="rounded-2xl px-5 py-3 text-[length:var(--font-sm)] font-black transition"
                            :class="activeTab === 'create' ? 'bg-[var(--text)] text-[var(--card)]' : 'bg-[var(--section-soft)] text-[var(--text)] hover:bg-[var(--primary-soft)]'"
                        >
                            ➕ Emitir Boleta
                        </button>
                    </div>
                </div>
            </div>

            <!-- TAB 1: LIST -->
            <div v-show="activeTab === 'list'" class="space-y-5" >
                <!-- Search -->
                <div class="rounded-3xl border bg-[var(--card)] p-5 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
                 style="border-color: var(--border)"
                 v-if="!tieneRol('Paciente')">
                    <div class="relative max-w-md" >
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                            🔍
                        </span>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar por paciente, CI o folio de boleta..."
                            class="w-full rounded-2xl border bg-[var(--section-soft)] py-3.5 pl-12 pr-5 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--primary)] focus:bg-[var(--card)]"
                            style="border-color: var(--border)"
                        />
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredBoletas.length === 0" class="rounded-3xl border border-dashed bg-[var(--card)] p-12 text-center shadow-[0_15px_40px_rgba(0,169,157,0.08)]" style="border-color: var(--border)">
                    <span class="text-4xl">🎟️</span>
                    <h2 class="mt-4 text-[length:var(--font-xl)] font-black text-[var(--title)]">No se encontraron boletas de pago</h2>
                    <p class="text-[length:var(--font-sm)] text-[var(--muted)] mt-1">
                        Intenta con otra búsqueda o emite una nueva boleta de pago desde la pestaña superior.
                    </p>
                </div>

                <!-- Listing Cards -->
                <div v-else class="space-y-4">
                    <div
                        v-for="boleta in filteredBoletas"
                        :key="boleta.id_boleta"
                        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_12px_30px_rgba(0,169,157,0.06)] hover:shadow-[0_18px_50px_rgba(0,169,157,0.12)] transition-colors duration-300"
                        style="border-color: var(--border)"
                    >
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between border-b pb-4" style="border-color: var(--border)">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-[length:var(--font-2xl)] border" style="border-color: var(--border)">
                                    🧾
                                </div>
                                <div>
                                    <h3 class="text-[length:var(--font-base)] font-black text-[var(--title)]">
                                        Boleta #{{ boleta.id_boleta }}
                                    </h3>
                                    <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                        Emitida el {{ boleta.fecha_emision }} | Modalidad: <span class="font-black text-[var(--primary)]">{{ boleta.modo_pago?.nombre }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex sm:flex-wrap items-center gap-1 sm:gap-3">
                                <div>
                                    <span class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider block">Estado Pago</span>
                                    <span class="rounded-full px-3 py-1 text-[length:var(--font-xs)] font-black" :class="{
                                        'bg-amber-100 text-amber-700 border border-amber-200': boleta.estado_pago === 'PENDIENTE',
                                        'bg-blue-100 text-blue-700 border border-blue-200': boleta.estado_pago === 'PARCIAL',
                                        'bg-emerald-100 text-emerald-700 border border-emerald-200': boleta.estado_pago === 'PAGADO',
                                        'bg-rose-100 text-rose-700 border border-rose-200': boleta.estado_pago === 'ELIMINADO',
                                    }">
                                        {{ boleta.estado_pago }}
                                    </span>
                                </div>

                                <div class="text-right">
                                    <span class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider block">Total Cobrado</span>
                                    <span class="text-[length:var(--font-base)] font-black text-[var(--title)]">Bs. {{ parseFloat(boleta.total as any).toFixed(2) }}</span>
                                </div>

                                <div class="flex gap-1.5 ml-2">
                                    <button
                                        type="button"
                                        @click="toggleExpandBoleta(boleta.id_boleta)"
                                        class="rounded-xl bg-[var(--primary-soft)] px-3.5 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:bg-[var(--border)]"
                                    >
                                        {{ expandedBoletaId === boleta.id_boleta ? 'Ocultar detalles ▴' : 'Ver detalles ▾' }}
                                    </button>
                                    <button
                                        v-if="boleta.estado_pago !== 'ELIMINADO' && !boleta.cuota_boleta?.some(c => c.estado !== 'PENDIENTE' && c.estado !== 'ANULADO' && c.estado !== 'ELIMINADO') && !tieneRol('Paciente')"
                                        type="button"
                                        @click="deleteBoleta(boleta.id_boleta)"
                                        class="rounded-xl p-2 text-slate-400 hover:bg-red-50 hover:text-red-500 transition"
                                        title="Eliminar Boleta"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Info Grid & Expanded Details -->
                        <div class="mt-4 grid gap-6 md:grid-cols-3 text-[length:var(--font-xs)]">
                            <div>
                                <span class="font-bold text-[var(--muted)] block">Paciente</span>
                                <span class="font-black text-[var(--title)]">
                                    {{ boleta.paciente?.persona?.nombre }} {{ boleta.paciente?.persona?.apellido }}
                                </span>
                                <span class="text-[var(--muted)] block">CI: {{ boleta.paciente_ci }}</span>
                            </div>
                            <div>
                                <span class="font-bold text-[var(--muted)] block">Encargado de Registro</span>
                                <span class="font-black text-[var(--title)]">
                                    {{ boleta.secretaria?.persona ? `${boleta.secretaria.persona.nombre} ${boleta.secretaria.persona.apellido}` : 'Sistema / Admin' }}
                                </span>
                                <span class="text-[var(--muted)] block">Descuento aplicado: Bs. {{ parseFloat(boleta.descuento as any).toFixed(2) }}</span>
                            </div>
                            <div>
                                <span class="font-bold text-[var(--muted)] block">Cant. Servicios Facturados</span>
                                <span class="font-black text-[var(--title)]">{{ boleta.servicio_prestado?.length || 0 }} servicio(s)</span>
                            </div>
                        </div>

                        <!-- EXPANDED DETALLES -->
                        <div v-if="expandedBoletaId === boleta.id_boleta" class="mt-6 border-t pt-5 space-y-6" style="border-color: var(--border)">
                            <!-- Billed Services List -->
                            <div>
                                <h4 class="text-[length:var(--font-xs)] font-black uppercase tracking-wider text-[var(--title)] mb-3">
                                    Servicios Prestados Incluidos
                                </h4>
<!-- Servicios en tabla para desktop -->
<div class="hidden overflow-x-auto rounded-2xl border md:block" style="border-color: var(--border)">
    <table class="w-full border-collapse bg-[var(--section-soft)] text-left text-[length:var(--font-xs)]">
        <thead>
            <tr class="border-b bg-[var(--bg)] text-[10px] font-black uppercase tracking-wider text-[var(--muted)]"
                style="border-color: var(--border)">
                <th class="px-5 py-3">Servicio</th>
                <th class="px-5 py-3 text-center">Cantidad</th>
                <th class="px-5 py-3 text-right">Precio Unitario</th>
                <th class="px-5 py-3 text-right">Descuento</th>
                <th class="px-5 py-3 text-right">Subtotal</th>
            </tr>
        </thead>

        <tbody class="divide-y" style="border-color: var(--border)">
            <tr v-for="s in boleta.servicio_prestado" :key="s.id"
                class="transition-colors hover:bg-[var(--primary-soft)]/20">
                <td class="px-5 py-2.5 font-bold text-[var(--title)]">
                    {{ s.servicio?.nombre }}
                </td>
                <td class="px-5 py-2.5 text-center">{{ s.cantidad }}</td>
                <td class="px-5 py-2.5 text-right">Bs. {{ parseFloat(s.precio as any).toFixed(2) }}</td>
                <td class="px-5 py-2.5 text-right text-red-500">- Bs. {{ parseFloat(s.descuento as any).toFixed(2) }}</td>
                <td class="px-5 py-2.5 text-right font-black text-[var(--primary)]">Bs. {{ parseFloat(s.subtotal as any).toFixed(2) }}</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Servicios en tarjetas para mobile -->
<div class="grid gap-3 md:hidden">
    <article
        v-for="s in boleta.servicio_prestado"
        :key="s.id"
        class="rounded-2xl border bg-[var(--section-soft)] p-4"
        style="border-color: var(--border)"
    >
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                    {{ s.servicio?.nombre }}
                </p>

                <p class="mt-1 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                    Cantidad: {{ s.cantidad }}
                </p>
            </div>

            <span class="rounded-xl bg-[var(--primary-soft)] px-3 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)]">
                Bs. {{ parseFloat(s.subtotal as any).toFixed(2) }}
            </span>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3 text-[length:var(--font-xs)]">
            <div class="rounded-xl bg-[var(--card)] p-3">
                <p class="font-bold text-[var(--muted)]">Precio Unitario</p>
                <p class="mt-1 font-black text-[var(--title)]">
                    Bs. {{ parseFloat(s.precio as any).toFixed(2) }}
                </p>
            </div>

            <div class="rounded-xl bg-[var(--card)] p-3">
                <p class="font-bold text-[var(--muted)]">Descuento</p>
                <p class="mt-1 font-black text-red-500">
                    - Bs. {{ parseFloat(s.descuento as any).toFixed(2) }}
                </p>
            </div>
        </div>
    </article>
</div>
                            </div>

                            <!-- Installments List -->
                            <div>
                                <h4 class="text-[length:var(--font-xs)] font-black uppercase tracking-wider text-[var(--title)] mb-3 flex items-center justify-between">
                                    <span>Plan de Pagos y Cuotas</span>
                                    <span class="hidden sm:block text-[10px] font-bold text-[var(--muted)] italic">Cronograma generado por crédito/contado</span>
                                </h4>

                                <div v-if="boleta.cuota_boleta?.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div
                                        v-for="cuota in boleta.cuota_boleta"
                                        :key="cuota.numero_cuota"
                                        class="rounded-2xl border p-4 bg-[var(--section-soft)]"
                                        :class="cuota.estado === 'PAGADO' ? 'border-emerald-200 bg-emerald-50/20' : (cuota.estado === 'ELIMINADO' ? 'border-rose-200 bg-rose-50/10' : 'border-[var(--border)]')"
                                    >
                                        <div class="flex items-center justify-between border-b pb-2 mb-2" style="border-color: var(--border)">
                                            <span class="font-black text-[var(--title)]">Cuota Nro {{ cuota.numero_cuota }}</span>
                                            <span class="rounded-full px-2 py-0.5 text-[9px] font-black" :class="cuota.estado === 'PAGADO' ? 'bg-emerald-100 text-emerald-700' : (cuota.estado === 'ELIMINADO' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700')">
                                                {{ cuota.estado }}
                                            </span>
                                        </div>

                                        <div class="space-y-1 text-[var(--text)] text-[length:var(--font-xs)]">
                                            <p><span class="font-bold">Monto:</span> Bs. {{ parseFloat(cuota.monto_cuota as any).toFixed(2) }}</p>
                                            <p><span class="font-bold">Vence:</span> {{ cuota.fecha_vencimiento }}</p>
                                            <p v-if="cuota.fecha_pago"><span class="font-bold">Pagado:</span> {{ cuota.fecha_pago }}</p>
                                            <p v-if="cuota.metodo_pago"><span class="font-bold">Método:</span> {{ cuota.metodo_pago.nombre }}</p>
                                            <p v-if="cuota.id_transaccion"><span class="font-bold">ID Transacción QR:</span> <span class="font-mono text-[10px] text-[var(--muted)] block break-all mt-0.5">{{ cuota.id_transaccion }}</span></p>
 
                                            <!-- Late Fine details -->
                                            <div v-if="cuota.cuota_multa" class="mt-2 border-t border-red-100 pt-2 text-[11px] text-red-600">
                                                <p class="font-bold">⚠️ Multa Registrada:</p>
                                                <p>Monto: Bs. {{ parseFloat(cuota.cuota_multa.monto_multa as any).toFixed(2) }} ({{ cuota.cuota_multa.estado }})</p>
                                                <p class="italic text-[10px]">"{{ cuota.cuota_multa.motivo }}"</p>
                                            </div>
                                        </div>

                                        <!-- Installment Actions -->
                                        <div v-if="cuota.estado !== 'PAGADO' && cuota.estado !== 'ELIMINADO' && boleta.estado_pago !== 'ELIMINADO'" class="mt-3 flex flex-col gap-1.5 border-t pt-3" style="border-color: var(--border)">
                                            <div v-if="canPayCuota(boleta, cuota)" class="flex flex-col gap-1.5 w-full">
                                                <div v-if="cuota.estado === 'PENDIENTE' || cuota.estado === 'ANULADO'" class="flex gap-1.5 w-full">
                                                    <button
                                                        type="button"
                                                        @click="openPaymentModal(boleta.id_boleta, cuota.numero_cuota, cuota.monto_cuota)"
                                                        class="flex-1 rounded-xl bg-[var(--primary-soft)] py-2 text-[10px] font-black text-[var(--primary)] transition hover:bg-[var(--border)]"
                                                    >
                                                        💵 Efectivo
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @click="generateQR(cuota.id_cuota)"
                                                        class="flex-1 rounded-xl bg-[var(--text)] py-2 text-[10px] font-black text-[var(--card)] transition hover:opacity-90"
                                                    >
                                                        📲 Pago QR
                                                    </button>
                                                </div>
                                                <button
                                                    v-if="cuota.id_transaccion"
                                                    type="button"
                                                    @click="verifyQRStatusDirect(cuota.id_cuota)"
                                                    class="w-full rounded-xl bg-amber-500 py-1.5 text-[10px] font-black text-white transition hover:bg-amber-600"
                                                >
                                                    🔄 Verificar QR Activo
                                                </button>
                                            </div>
                                            <div v-else class="text-[10px] text-slate-400 italic flex items-center gap-1.5">
                                                🔒 No disponible hasta cobrar la cuota anterior.
                                            </div>
                                        </div>

                                        <div v-else-if="cuota.comprobante" class="mt-3 text-[10px] text-[var(--muted)] border-t pt-3" style="border-color: var(--border)">
                                            <span class="font-bold block text-[var(--muted)] uppercase tracking-wider">Comprobante</span>
                                            <a
                                                v-if="cuota.comprobante !== 'PAGO_QR_PAGOFACIL'"
                                                :href="formatPath(cuota.comprobante)"
                                                target="_blank"
                                                class="text-blue-600 font-bold hover:underline"
                                            >
                                                Ver Recibo de Pago 🖼️
                                            </a>
                                            <span v-else class="text-emerald-600 font-bold">✓ Pago digital procesado por PagoFacil QR</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: CREATE -->
            <div v-show="activeTab === 'create'" class="space-y-6">
                <div class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300" style="border-color: var(--border)">
                    <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)] mb-4">Emitir Boleta de Pago</h2>

                    <form @submit.prevent="submitCreateBoleta" class="space-y-6">
                        <div class="grid gap-5 md:grid-cols-2">
                            <!-- Select Tratamiento -->
                            <div>
                                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--text)]">Seleccionar Tratamiento a Cobrar</label>
                                <select
                                    v-model="selectedTratamientoId"
                                    @change="handleTratamientoChange"
                                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:border-[var(--primary)] focus:bg-[var(--card)]"
                                    style="border-color: var(--border)"
                                >
                                    <option :value="null">-- Seleccionar tratamiento --</option>
                                    <option v-for="t in tratamientosDisponibles" :key="t.id" :value="t.id">
                                        Tratamiento #{{ t.id }} - {{ t.objetivo_tratamiento }} (Paciente: {{ t.historial_clinico?.paciente?.persona?.nombre }} {{ t.historial_clinico?.paciente?.persona?.apellido }})
                                    </option>
                                </select>
                            </div>

                            <!-- Paciente (Readonly display) -->
                            <div>
                                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--text)]">Código de Paciente (CI)</label>
                                <input
                                    v-model="form.paciente_ci"
                                    type="text"
                                    readonly
                                    required
                                    placeholder="Selecciona un tratamiento primero..."
                                    class="w-full rounded-2xl border bg-[var(--bg)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none cursor-not-allowed opacity-80"
                                    style="border-color: var(--border)"
                                />
                            </div>

                            <!-- Modo de Pago -->
                            <div>
                                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--text)]">Modo de Pago</label>
                                <select
                                    v-model="form.id_modo_pago"
                                    required
                                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:border-[var(--primary)] focus:bg-[var(--card)]"
                                    style="border-color: var(--border)"
                                >
                                    <option value="">-- Seleccionar modo --</option>
                                    <option v-for="m in modoPagos" :key="m.id_modo_pago" :value="m.id_modo_pago">
                                        {{ m.nombre }}
                                    </option>
                                </select>
                            </div>

                            <!-- Secretaria Registradora -->
                            <div>
                                <label class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--text)]">Registrado Por (Secretaria / Admin)</label>
                                <select
                                    v-model="form.secretaria_ci"
                                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:border-[var(--primary)] focus:bg-[var(--card)]"
                                    style="border-color: var(--border)"
                                >
                                    <option value="">-- Seleccionar encargada (opcional) --</option>
                                    <option v-for="s in secretarias" :key="s.codigo_secretaria" :value="s.codigo_secretaria">
                                        {{ s.persona?.nombre }} {{ s.persona?.apellido }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- CREDIT OPTIONS -->
                        <div v-if="isCreditSelected" class="rounded-2xl border bg-[var(--section-soft)] p-5 grid gap-4 md:grid-cols-2" style="border-color: var(--border)">
                            <div>
                                <label class="mb-2 block text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Período de Cuota</label>
                                <select
                                    v-model="form.tipo_credito"
                                    required
                                    class="w-full rounded-xl border bg-[var(--card)] px-4 py-3 text-[length:var(--font-xs)] text-[var(--title)] outline-none"
                                    style="border-color: var(--border)"
                                >
                                    <option value="semanas">Semanas</option>
                                    <option value="meses">Meses</option>
                                </select>
                                <span class="text-[10px] text-[var(--muted)] mt-1.5 block italic">
                                    (Subtotal por período: Bs. {{ (calculatedTotal / Math.max(1, form.cantidad_cuotas || 1)).toFixed(2) }})
                                </span>
                            </div>
                            <div>
                                <label class="mb-2 block text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Cantidad de Cuotas</label>
                                <input
                                    v-model.number="form.cantidad_cuotas"
                                    type="number"
                                    min="1"
                                    required
                                    class="w-full rounded-xl border bg-[var(--card)] px-4 py-3 text-[length:var(--font-xs)] text-[var(--title)] outline-none"
                                    style="border-color: var(--border)"
                                />
                            </div>
                        </div>

                        <!-- SERVICES LIST OF SELECTED TREATMENT -->
                        <div v-if="selectedTratamiento" class="border-t pt-5 space-y-4" style="border-color: var(--border)">
                            <h3 class="text-[length:var(--font-base)] font-black text-[var(--title)]">Servicios Clínicos del Tratamiento</h3>
                            <p class="text-[length:var(--font-xs)] text-[var(--muted)]">Selecciona los servicios del tratamiento que deseas incluir en esta boleta.</p>

                            <div v-if="selectedTratamiento.servicio_prestado?.length === 0" class="text-center py-6 bg-[var(--section-soft)] rounded-2xl border" style="border-color: var(--border)">
                                No hay servicios prestados activos para facturar en este tratamiento.
                            </div>

                            <div v-else class="space-y-3">
                                <div
                                    v-for="s in selectedTratamiento.servicio_prestado"
                                    :key="s.id"
                                    class="flex items-center justify-between border rounded-2xl p-4 bg-[var(--section-soft)] hover:bg-[var(--primary-soft)]/20 transition-colors" style="border-color: var(--border)"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="checkbox"
                                            :checked="form.servicio_prestado_ids.includes(s.id)"
                                            @change="handleServiceToggle(s.id)"
                                            class="h-5 w-5 rounded border-[var(--border)] text-[var(--primary)] focus:ring-[var(--primary)] bg-[var(--card)]"
                                        />
                                        <div>
                                            <p class="text-[length:var(--font-sm)] font-black text-[var(--title)]">{{ s.servicio?.nombre }}</p>
                                            <p class="text-[length:var(--font-xs)] text-[var(--muted)]">Cantidad: {{ s.cantidad }} | Precio: Bs. {{ s.precio }} | Desc: Bs. {{ s.descuento }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[length:var(--font-sm)] font-black text-[var(--primary)]">Bs. {{ parseFloat(s.subtotal as any).toFixed(2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-10 rounded-2xl border border-dashed bg-[var(--section-soft)] text-[var(--muted)]" style="border-color: var(--border)">
                            Selecciona un tratamiento arriba para poder ver sus servicios pendientes de pago.
                        </div>

                        <!-- Calculations Card -->
                        <div class="border-t pt-5 flex flex-col items-end space-y-3" style="border-color: var(--border)">
                            <div class="w-full md:max-w-xs space-y-2 text-[length:var(--font-xs)]">
                                <div class="flex justify-between text-[var(--muted)]">
                                    <span>Subtotal Servicios</span>
                                    <span>Bs. {{ calculatedSubtotal.toFixed(2) }}</span>
                                </div>

                                <div class="flex justify-between items-center text-[var(--muted)]">
                                    <span>Descuento Adicional</span>
                                    <input
                                        v-model.number="form.descuento"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="w-24 rounded-lg border bg-[var(--card)] px-2.5 py-1 text-right text-[length:var(--font-xs)] text-[var(--title)] outline-none" style="border-color: var(--border)"
                                    />
                                </div>

                                <div class="flex justify-between font-black text-[var(--title)] border-t pt-2 text-[length:var(--font-base)]" style="border-color: var(--border)">
                                    <span>Total a Pagar</span>
                                    <span>Bs. {{ calculatedTotal.toFixed(2) }}</span>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button
                                    type="button"
                                    @click="activeTab = 'list'"
                                    class="rounded-xl border bg-[var(--card)] px-6 py-3.5 text-[length:var(--font-xs)] font-black text-[var(--text)] hover:bg-[var(--section-soft)]" style="border-color: var(--border)"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing || form.servicio_prestado_ids.length === 0"
                                    class="rounded-xl bg-[var(--text)] px-6 py-3.5 text-[length:var(--font-xs)] font-black text-[var(--card)] hover:opacity-90 disabled:opacity-60"
                                >
                                    Emitir Boleta
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- CASH RECEIPT PAYMENT MODAL -->
        <div v-if="paymentModalOpen && activeInstallment" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-[var(--card)] rounded-3xl border p-6 shadow-2xl space-y-4" style="border-color: var(--border)">
                <div class="flex justify-between items-center border-b pb-3" style="border-color: var(--border)">
                    <h3 class="text-[length:var(--font-lg)] font-black text-[var(--title)]">Registrar Pago en Efectivo</h3>
                    <button type="button" @click="paymentModalOpen = false" class="text-[var(--muted)] hover:text-[var(--text)] text-[length:var(--font-lg)]">✕</button>
                </div>

                <form @submit.prevent="submitPayment" class="space-y-4">
                    <div>
                        <span class="text-[length:var(--font-xs)] text-[var(--muted)] block">Folio de Boleta: #{{ activeInstallment.boletaId }}</span>
                        <span class="text-[length:var(--font-xs)] text-[var(--muted)] block">Número de Cuota: {{ activeInstallment.nroCuota }}</span>
                        <span class="text-[length:var(--font-base)] font-black text-[var(--primary)] block mt-1">Monto a pagar: Bs. {{ parseFloat(activeInstallment.monto as any).toFixed(2) }}</span>
                    </div>

                    <!-- Metodo de Pago selector -->
                    <div>
                        <label class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Método de Cobro</label>
                        <select
                            v-model="paymentForm.id_metodo_pago"
                            required
                            class="w-full rounded-xl border bg-[var(--section-soft)] px-3.5 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none" style="border-color: var(--border)"
                        >
                            <option v-for="met in metodoPagos" :key="met.id_metodo_pago" :value="met.id_metodo_pago">
                                {{ met.nombre }} ({{ met.descripcion || 'Sin descripción' }})
                            </option>
                        </select>
                    </div>

                    <!-- Receipt Upload -->
                    <div>
                        <label class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Subir Comprobante (Firma/Imagen) <span class="text-red-500">*</span></label>
                        <input
                            type="file"
                            accept="image/*"
                            required
                            @change="handleReceiptFileChange"
                            class="w-full text-[length:var(--font-xs)] text-[var(--muted)] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[length:var(--font-xs)] file:font-black file:bg-[var(--primary-soft)] file:text-[var(--primary)] file:hover:bg-[var(--border)]"
                        />
                        <p class="text-[10px] text-slate-400 mt-1">Sube una foto o captura del recibo de caja firmado.</p>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t" style="border-color: var(--border)">
                        <button
                            type="button"
                            @click="paymentModalOpen = false"
                            class="rounded-xl border bg-[var(--card)] px-4 py-2.5 text-[length:var(--font-xs)] font-black text-[var(--text)] hover:bg-[var(--section-soft)]" style="border-color: var(--border)"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="paymentForm.processing"
                            class="rounded-xl bg-[var(--text)] px-4 py-2.5 text-[length:var(--font-xs)] font-black text-[var(--card)] hover:opacity-90 disabled:opacity-60"
                        >
                            Registrar Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- QR MODAL WITH PAGO FACIL INTEGRATION -->
        <div v-if="qrModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-[var(--card)] rounded-3xl border p-6 shadow-2xl text-center space-y-4" style="border-color: var(--border)">
                <div class="flex justify-between items-center border-b pb-3 text-left" style="border-color: var(--border)">
                    <h3 class="text-[length:var(--font-lg)] font-black text-[var(--title)]">Pago Electrónico QR</h3>
                    <button type="button" @click="qrModalOpen = false" class="text-[var(--muted)] hover:text-[var(--text)] text-[length:var(--font-lg)]">✕</button>
                </div>

                <div class="space-y-4">
                    <!-- Loading QR -->
                    <div v-if="!qrBase64 && !qrError" class="py-12 flex flex-col items-center justify-center space-y-3">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[var(--primary)]"></div>
                        <p class="text-[length:var(--font-xs)] text-[var(--muted)] font-bold">Generando código QR con PagoFacil...</p>
                    </div>

                    <!-- Error QR -->
                    <div v-else-if="qrError" class="py-6 rounded-2xl bg-red-50 border border-red-200 p-4 text-xs font-bold text-red-500">
                        <p class="mb-1 text-sm font-black">Error al procesar QR:</p>
                        <p>{{ qrError }}</p>
                    </div>

                    <!-- Display QR -->
                    <div v-else class="space-y-4">
                        <p class="text-[length:var(--font-xs)] text-[var(--muted)]">Escanea este código QR desde tu aplicación bancaria móvil para realizar el pago de la cuota.</p>
                        
                        <div class="mx-auto border p-3 rounded-2xl bg-white w-56 h-56 flex items-center justify-center shadow-inner cursor-zoom-in hover:scale-105 transition-transform" @click="isQrZoomed = true" title="Hacer clic para ampliar" style="border-color: var(--border)">
                            <img :src="qrBase64 ? (qrBase64.startsWith('data:image/') ? qrBase64 : 'data:image/png;base64,' + qrBase64) : undefined" alt="Código de Pago QR" class="max-w-full max-h-full" />
                        </div>

                        <div class="text-[length:var(--font-xs)] text-[var(--muted)]">
                            <p>ID de Transacción: <span class="font-bold text-[var(--title)]">{{ qrTransactionId }}</span></p>
                            <a v-if="qrCheckoutUrl" :href="qrCheckoutUrl" target="_blank" class="text-blue-600 font-bold hover:underline mt-1 block">Ir a pasarela de PagoFacil 🔗</a>
                        </div>
                    </div>
                </div>

                <!-- Footer buttons -->
                <div class="flex justify-between gap-2 pt-3 border-t" style="border-color: var(--border)">
                    <button
                        type="button"
                        @click="qrModalOpen = false"
                        class="rounded-xl border bg-[var(--card)] px-4 py-2.5 text-[length:var(--font-xs)] font-black text-[var(--text)] hover:bg-[var(--section-soft)]" style="border-color: var(--border)"
                    >
                        Cerrar
                    </button>
                    <button
                        v-if="qrBase64"
                        type="button"
                        @click="verifyQRStatus"
                        :disabled="qrChecking"
                        class="flex-1 rounded-xl bg-[var(--primary)] px-4 py-2.5 text-[length:var(--font-xs)] font-black text-white hover:bg-[var(--primary-hover)] disabled:opacity-60"
                    >
                        {{ qrChecking ? 'Consultando...' : 'Verificar Pago ✓' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- QR ZOOM MODAL -->
        <div v-if="isQrZoomed && qrBase64" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" @click="isQrZoomed = false">
            <div class="relative bg-[var(--card)] rounded-3xl p-6 border shadow-2xl max-w-sm w-full flex flex-col items-center space-y-4" style="border-color: var(--border)" @click.stop>
                <div class="w-full flex justify-between items-center border-b pb-2" style="border-color: var(--border)">
                    <h4 class="text-[length:var(--font-sm)] font-black text-[var(--title)]">Código QR Ampliado</h4>
                    <button type="button" @click="isQrZoomed = false" class="text-[var(--muted)] hover:text-[var(--text)] font-bold">Cerrar ✕</button>
                </div>
                <div class="bg-white p-4 rounded-2xl border shadow-inner w-80 h-80 flex items-center justify-center" style="border-color: var(--border)">
                    <img :src="qrBase64.startsWith('data:image/') ? qrBase64 : 'data:image/png;base64,' + qrBase64" alt="Código de Pago QR Ampliado" class="max-w-full max-h-full select-none" />
                </div>
                <p class="text-[10px] text-[var(--muted)] font-bold text-center">Haz clic fuera de la tarjeta o presiona "Cerrar" para volver.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
