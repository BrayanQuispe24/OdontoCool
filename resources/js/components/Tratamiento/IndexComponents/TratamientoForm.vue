<script setup lang="ts">
import { Diente } from '@/types/Diente';
import { Diagnostico } from '@/types/Diagnostico';
import { HistorialClinico } from '@/types/HistorialClinico';
import { Servicio } from '@/types/Servicio';
import { computed } from 'vue';

const props = defineProps<{
    form: any;
    editMode: boolean;
    dientes: Diente[];
    diagnosticosSelect: Diagnostico[];
    historiales: HistorialClinico[];
    servicios: Servicio[];
    activeFormTab: 'general' | 'dientes' | 'receta' | 'servicios';
    activeToothIndex: number;
    tieneRol: (rol: string) => boolean;
}>();

const emit = defineEmits<{
    'update:activeFormTab': [tab: 'general' | 'dientes' | 'receta' | 'servicios'];
    'update:activeToothIndex': [index: number];
    'submit': [];
    'cancel': [];
}>();

const carastDentales = [
    'General',
    'Vestibular',
    'Oclusal',
    'Palatina',
    'Lingual',
    'Mesial',
    'Distal',
];

const hasNonDotErrors = computed(() => {
    return Object.keys(props.form.errors).some(key => !key.includes('.'));
});

const getToothNumber = (dienteId: number) => {
    const diente = props.dientes.find((d) => d.id === dienteId);
    return diente ? `#${diente.numero}` : 'S/N';
};

const getToothName = (dienteId: number) => {
    const diente = props.dientes.find((d) => d.id === dienteId);
    return diente ? diente.nombre : 'Seleccionar diente';
};

const getToothError = (index: number): string | undefined => {
    return (props.form.errors as any)[`dientes_tratamientos.${index}.tratamiento_planificado`];
};

const addTooth = () => {
    const firstToothId = props.dientes.length > 0 ? props.dientes[0].id : 0;

    props.form.dientes_tratamientos.push({
        diente_id: firstToothId,
        cara_dental: 'General',
        tratamiento_planificado: '',
        observacion: '',
    });

    emit('update:activeToothIndex', props.form.dientes_tratamientos.length - 1);
};

const removeTooth = (index: number) => {
    props.form.dientes_tratamientos.splice(index, 1);

    if (props.activeToothIndex >= props.form.dientes_tratamientos.length) {
        emit(
            'update:activeToothIndex',
            Math.max(0, props.form.dientes_tratamientos.length - 1),
        );
    }
};

const getServicioPrecioVigente = (servicio: any): number => {
    if (!servicio || !servicio.asignaciones_precio || servicio.asignaciones_precio.length === 0) {
        return 0;
    }
    const asignacion = servicio.asignaciones_precio.find((ap: any) => ap.estado === 'ACTIVO');
    if (asignacion && asignacion.precio) {
        return parseFloat(asignacion.precio.monto);
    }
    return 0;
};

const addServicio = () => {
    const list = props.servicios || [];
    const firstServicio = list.length > 0 ? list[0] : null;
    const precio = firstServicio ? getServicioPrecioVigente(firstServicio) : 0;

    props.form.servicios_prestados.push({
        servicio_id: firstServicio ? firstServicio.id : 0,
        cantidad: 1,
        precio: precio,
        descuento: 0,
        fecha_servicio: new Date().toISOString().split('T')[0],
        observacion: '',
    });
};

const removeServicio = (index: number) => {
    props.form.servicios_prestados.splice(index, 1);
};

const handleServicioChange = (index: number) => {
    const sp = props.form.servicios_prestados[index];
    if (!sp) return;
    const list = props.servicios || [];
    const servicio = list.find((s) => s.id === sp.servicio_id);
    if (servicio) {
        sp.precio = getServicioPrecioVigente(servicio);
    }
};

const totalServicios = computed(() => {
    return props.form.servicios_prestados.reduce((total: number, sp: any) => {
        const itemSubtotal = (sp.precio * sp.cantidad) - sp.descuento;
        return total + (itemSubtotal > 0 ? itemSubtotal : 0);
    }, 0);
});

const addRecetaDetalle = () => {
    props.form.receta.detalles.push({
        descripcion: '',
        dosis: '',
        duracion: '',
        frecuencia: '',
    });
};

const removeRecetaDetalle = (index: number) => {
    props.form.receta.detalles.splice(index, 1);
};

const isFieldNonDot = (field: string | number): boolean => {
    return !String(field).includes('.');
};
</script>

<template>
    <div
        class="rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <h2 class="text-[length:var(--font-xl)] font-black text-[var(--title)]">
            {{ editMode ? 'Editar Tratamiento' : 'Registrar Tratamiento' }}
        </h2>

        <!-- Errors Alert -->
        <div v-if="hasNonDotErrors"
            class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-bold text-red-500">
            <p class="mb-1 text-[length:var(--font-sm)] font-black">
                Por favor corrige los siguientes errores:
            </p>

            <ul class="list-disc space-y-1 pl-4">
                <template v-for="(error, field) in form.errors" :key="field">
                    <li v-if="isFieldNonDot(field)">
                        {{ error }}
                    </li>
                </template>
            </ul>
        </div>

        <!-- Form Tabs Header -->
        <div class="mt-6 flex overflow-x-auto border-b" style="border-color: var(--border)">
            <button type="button" @click="emit('update:activeFormTab', 'general')"
                class="whitespace-nowrap border-b-2 px-6 py-3 text-[length:var(--font-sm)] font-black transition-all"
                :class="activeFormTab === 'general'
                        ? 'border-[var(--primary)] text-[var(--primary)]'
                        : 'border-transparent text-[var(--muted)] hover:text-[var(--title)]'
                    ">
                📝 Datos Generales
            </button>

            <button type="button" @click="emit('update:activeFormTab', 'dientes')"
                class="flex items-center gap-2 whitespace-nowrap border-b-2 px-6 py-3 text-[length:var(--font-sm)] font-black transition-all"
                :class="activeFormTab === 'dientes'
                        ? 'border-[var(--primary)] text-[var(--primary)]'
                        : 'border-transparent text-[var(--muted)] hover:text-[var(--title)]'
                    ">
                齿 Tratamientos de Dientes

                <span
                    class="rounded-full border bg-[var(--primary-soft)] px-2 py-0.5 text-[length:var(--font-xs)] font-black text-[var(--primary)]"
                    style="border-color: var(--border)">
                    {{ form.dientes_tratamientos.length }}
                </span>
            </button>

            <button type="button" @click="emit('update:activeFormTab', 'receta')"
                class="flex items-center gap-2 whitespace-nowrap border-b-2 px-6 py-3 text-[length:var(--font-sm)] font-black transition-all"
                :class="activeFormTab === 'receta'
                        ? 'border-[var(--primary)] text-[var(--primary)]'
                        : 'border-transparent text-[var(--muted)] hover:text-[var(--title)]'
                    ">
                💊 Receta e Indicaciones

                <span v-if="form.receta?.detalles?.length"
                    class="rounded-full border bg-[var(--primary-soft)] px-2 py-0.5 text-[length:var(--font-xs)] font-black text-[var(--primary)]"
                    style="border-color: var(--border)">
                    {{ form.receta?.detalles?.length ?? 0 }}
                </span>
            </button>

            <button
                type="button"
                @click="emit('update:activeFormTab', 'servicios')"
                class="flex items-center gap-2 border-b-2 px-6 py-3 text-[length:var(--font-sm)] font-black transition-all"
                :class="
                    activeFormTab === 'servicios'
                        ? 'border-[var(--primary)] text-[var(--primary)]'
                        : 'border-transparent text-[var(--muted)] hover:text-[var(--title)]'
                "
            >
                🛠️ Servicios
                <span
                    v-if="form.servicios_prestados.length"
                    class="rounded-full border bg-[var(--primary-soft)] px-2 py-0.5 text-[length:var(--font-xs)] font-black text-[var(--primary)]"
                    style="border-color: var(--border)"
                >
                    {{ form.servicios_prestados.length }}
                </span>
            </button>
        </div>

        <form @submit.prevent="emit('submit')" class="mt-6 space-y-6">
            <!-- TAB 1: General Info -->
            <div v-show="activeFormTab === 'general'" class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="objetivo_tratamiento"
                        class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Objetivo del Tratamiento
                    </label>

                    <input :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')"
                        id="objetivo_tratamiento" v-model="form.objetivo_tratamiento" type="text"
                        placeholder="Ej: Tratamiento de conducto, Estética dental general, etc."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                    <p v-if="form.errors.objetivo_tratamiento"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.objetivo_tratamiento }}
                    </p>
                </div>

                <div>
                    <label for="codigo_historial"
                        class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Historial Clínico del Paciente
                    </label>

                    <select :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" id="codigo_historial"
                        v-model="form.codigo_historial" required
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                        <option value="">Selecciona el historial clínico...</option>

                        <option v-for="historial in historiales" :key="historial.codigo_historial"
                            :value="historial.codigo_historial">
                            {{ historial.paciente?.persona?.nombre }} {{ historial.paciente?.persona?.apellido
                            }} (CI:
                            {{ historial.paciente_ci }} - Ficha #{{ historial.codigo_historial }})
                        </option>
                    </select>

                    <p v-if="form.errors.codigo_historial"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.codigo_historial }}
                    </p>
                </div>

                <div>
                    <label for="diagnostico_id"
                        class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Diagnóstico Clínico del Paciente
                    </label>

                    <select :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" id="diagnostico_id"
                        v-model="form.diagnostico_id" required
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                        <option value="">Selecciona el diagnóstico clínico...</option>

                        <option v-for="diag in diagnosticosSelect" :key="diag.id" :value="diag.id">
                            Ficha Diagnóstico #{{ diag.id }} - {{ diag.tipo_diagnostico }} (Paciente: {{
                                diag.cita?.paciente?.persona?.nombre }} {{ diag.cita?.paciente?.persona?.apellido
                            }})
                        </option>
                    </select>

                    <p v-if="form.errors.diagnostico_id"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.diagnostico_id }}
                    </p>
                </div>

                <div>
                    <label for="fecha_inicio"
                        class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Fecha de Inicio
                    </label>

                    <input :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" id="fecha_inicio"
                        v-model="form.fecha_inicio" type="date"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                    <p v-if="form.errors.fecha_inicio"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.fecha_inicio }}
                    </p>
                </div>

                <div>
                    <label for="fecha_fin_estimada"
                        class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Fecha Fin Estimada
                    </label>

                    <input :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" id="fecha_fin_estimada"
                        v-model="form.fecha_fin_estimada" type="date"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                    <p v-if="form.errors.fecha_fin_estimada"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.fecha_fin_estimada }}
                    </p>
                </div>

                <div>
                    <label for="fecha_fin_real"
                        class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Fecha Fin Real (Opcional)
                    </label>

                    <input :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" id="fecha_fin_real"
                        v-model="form.fecha_fin_real" type="date"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />

                    <p v-if="form.errors.fecha_fin_real"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.fecha_fin_real }}
                    </p>
                </div>

                <div>
                    <label for="estado"
                        class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Estado
                    </label>

                    <select :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" id="estado"
                        v-model="form.estado"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                        <option value="ACTIVO">Activo</option>
                        <option value="INACTIVO">Inactivo</option>
                    </select>

                    <p v-if="form.errors.estado"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.estado }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label for="observacion"
                        class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                        Observaciones Generales
                    </label>

                    <textarea :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" id="observacion"
                        v-model="form.observacion" rows="3"
                        placeholder="Observaciones generales sobre el plan de tratamiento..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"></textarea>

                    <p v-if="form.errors.observacion"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.observacion }}
                    </p>
                </div>
            </div>

            <!-- TAB 2: Tooth treatments dynamic tabs -->
            <div v-show="activeFormTab === 'dientes'" class="space-y-4">
                <div class="flex items-center justify-between border-b pb-3"
                    style="border-color: var(--border)">
                    <div>
                        <h3 class="text-[length:var(--font-base)] font-black text-[var(--title)]">
                            Hojas de Tratamiento por Diente
                        </h3>

                        <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                            Añade los dientes a intervenir en este
                            tratamiento y configura su respectivo
                            procedimiento.
                        </p>
                    </div>

                    <button v-if="tieneRol('Doctor') || tieneRol('Administrador')" type="button"
                        @click="addTooth"
                        class="rounded-xl border bg-[var(--card)] px-4 py-2.5 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:bg-[var(--primary-soft)]"
                        style="border-color: var(--primary)">
                        + Agregar Diente
                    </button>
                </div>

                <div v-if="form.dientes_tratamientos.length === 0"
                    class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed bg-[var(--section-soft)] p-8 text-center"
                    style="border-color: var(--border)">
                    <span class="text-[length:var(--font-3xl)]">🦷</span>

                    <h4 class="mt-3 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        Sin dientes asignados
                    </h4>

                    <p class="mt-1 max-w-xs text-[length:var(--font-xs)] text-[var(--muted)]">
                        Un tratamiento completo usualmente actúa sobre
                        uno o más dientes. Presiona el botón para
                        agregar el primero.
                    </p>

                    <button type="button" @click="addTooth"
                        class="mt-4 rounded-xl bg-[var(--primary)] px-4 py-2.5 text-[length:var(--font-xs)] font-black text-white shadow-md transition hover:bg-[var(--primary-hover)]">
                        Asignar Diente
                    </button>
                </div>

                <div v-else class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                    <div class="max-h-96 space-y-1.5 overflow-y-auto pr-2 lg:col-span-1">
                        <p
                            class="px-2 text-[length:var(--font-xs)] font-bold uppercase tracking-wider text-[var(--muted)]">
                            Dientes añadidos
                        </p>

                        <div v-for="(dt, idx) in form.dientes_tratamientos" :key="idx"
                            class="group flex cursor-pointer items-center justify-between rounded-xl border px-3 py-2.5 text-[length:var(--font-xs)] font-bold transition"
                            :class="activeToothIndex === idx
                                    ? 'border-[var(--primary)] bg-[var(--primary-soft)] text-[var(--primary)]'
                                    : 'bg-[var(--section-soft)] text-[var(--muted)] hover:bg-[var(--primary-soft)]'
                                " style="border-color: var(--border)" @click="emit('update:activeToothIndex', idx)">
                            <span class="flex items-center gap-1.5 truncate">
                                <span
                                    class="shrink-0 rounded border bg-[var(--card)] px-1 text-[length:var(--font-xs)] font-black text-[var(--primary)]"
                                    style="border-color: var(--border)">
                                    {{ getToothNumber(dt.diente_id) }}
                                </span>

                                <span class="truncate">
                                    {{ getToothName(dt.diente_id) }}
                                </span>
                            </span>

                            <button v-if="tieneRol('Doctor') || tieneRol('Administrador')" type="button"
                                @click.stop="removeTooth(idx)"
                                class="rounded p-1 text-[length:var(--font-xs)] font-black text-[var(--muted)] hover:text-red-500"
                                title="Eliminar diente">
                                ✕
                            </button>
                        </div>
                    </div>

                    <div v-if="form.dientes_tratamientos[activeToothIndex]" class="space-y-4 rounded-2xl border bg-[var(--section-soft)] p-5 lg:col-span-3"
                        style="border-color: var(--border)">
                        <div class="flex items-center justify-between border-b pb-2"
                            style="border-color: var(--border)">
                            <h4
                                class="flex items-center gap-2 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                                <span>
                                    Hoja de Tratamiento #{{ activeToothIndex + 1 }}
                                </span>

                                <span class="text-[length:var(--font-xs)] font-semibold text-[var(--muted)]">
                                    ({{
                                        getToothNumber(
                                            form.dientes_tratamientos[
                                                activeToothIndex
                                            ].diente_id,
                                        )
                                    }})
                                </span>
                            </h4>

                            <button v-if="tieneRol('Doctor') || tieneRol('Administrador')" type="button"
                                @click="removeTooth(activeToothIndex)"
                                class="text-[length:var(--font-xs)] font-black text-red-500 hover:underline">
                                Quitar este diente
                            </button>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-[length:var(--font-xs)] font-bold text-[var(--title)]">
                                    Seleccionar Diente
                                </label>

                                <select :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" v-model="form.dientes_tratamientos[
                                        activeToothIndex
                                    ].diente_id
                                    "
                                    class="w-full rounded-xl border bg-[var(--card)] px-4 py-3 text-[length:var(--font-xs)] text-[var(--title)] outline-none transition focus:ring-4"
                                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                                    <option v-for="diente in dientes" :key="diente.id" :value="diente.id">
                                        {{ diente.numero }} -
                                        {{ diente.nombre }} ({{
                                            diente.ubicacion
                                        }})
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-[length:var(--font-xs)] font-bold text-[var(--title)]">
                                    Cara Dental
                                </label>

                                <select :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" v-model="form.dientes_tratamientos[
                                        activeToothIndex
                                    ].cara_dental
                                    "
                                    class="w-full rounded-xl border bg-[var(--card)] px-4 py-3 text-[length:var(--font-xs)] text-[var(--title)] outline-none transition focus:ring-4"
                                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)">
                                    <option v-for="cara in carastDentales" :key="cara" :value="cara">
                                        {{ cara }}
                                    </option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-[length:var(--font-xs)] font-bold text-[var(--title)]"
                                >
                                    Procedimiento Planificado <span class="text-red-500">*</span>
                                </label>

                                <textarea :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" v-model="form.dientes_tratamientos[
                                        activeToothIndex
                                    ].tratamiento_planificado
                                    " rows="3" required
                                    placeholder="Detalla el procedimiento clínico a realizar sobre este diente..."
                                    class="w-full rounded-xl border bg-[var(--card)] px-4 py-3 text-[length:var(--font-xs)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:ring-4"
                                    :class="getToothError(activeToothIndex) ? 'border-red-500 bg-red-950/20 focus:ring-red-500/20 focus:border-red-500' : 'focus:border-[var(--primary)] focus:ring-[var(--primary-soft)]'"
                                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                ></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-[length:var(--font-xs)] font-bold text-[var(--title)]">
                                    Observación del Diente (Opcional)
                                </label>

                                <textarea :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')" v-model="form.dientes_tratamientos[
                                        activeToothIndex
                                    ].observacion
                                    " rows="2"
                                    placeholder="Notas u observaciones de diagnóstico para este diente..."
                                    class="w-full rounded-xl border bg-[var(--card)] px-4 py-3 text-[length:var(--font-xs)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:ring-4"
                                    style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: Recetas & Indicaciones -->
            <div v-show="activeFormTab === 'receta'" class="space-y-6">
                <div v-if="tieneRol('Doctor') || tieneRol('Administrador')"
                    class="rounded-2xl border border-dashed bg-[var(--section-soft)] p-5"
                    style="border-color: var(--border)">
                    <label class="flex cursor-pointer items-center gap-3">
                        <input v-model="form.incluir_receta" type="checkbox"
                            class="h-4 w-4 rounded border-[var(--primary)] text-[var(--primary)] focus:ring-[var(--primary)]" />

                        <span class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                            Incluir Receta e Indicaciones Médicas para este Tratamiento
                        </span>
                    </label>

                    <p class="mt-1 pl-7 text-[length:var(--font-xs)] text-[var(--muted)]">
                        Si está marcado, se registrarán las indicaciones y recetas médicas emitidas para el paciente en este tratamiento.
                    </p>
                </div>

                <div v-if="form.incluir_receta" class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-1">
                        <div>
                            <label for="observacion_general"
                                class="mb-2 block text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                                Observación General
                            </label>

                            <input :disabled="!tieneRol('Doctor') && !tieneRol('Administrador')"
                                id="observacion_general" v-model="form.receta.observacion_general" type="text"
                                placeholder="Ej: Reposo absoluto por 24 horas, evitar alimentos calientes o sólidos."
                                class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-4 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />
                        </div>
                    </div>

                    <div class="border-t pt-6" style="border-color: var(--border)">
                        <div class="mb-4 flex items-center justify-between">
                            <h4 class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                                Medicamentos e Indicaciones Detalladas
                            </h4>

                            <button type="button" @click="addRecetaDetalle" v-if="tieneRol('Doctor')"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-[var(--primary-soft)] px-3.5 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:opacity-80">
                                + Agregar Medicamento
                            </button>
                        </div>

                        <div v-if="form.receta.detalles.length === 0"
                            class="rounded-2xl border bg-[var(--section-soft)] py-8 text-center"
                            style="border-color: var(--border)">
                            <span class="text-[length:var(--font-2xl)]">💊</span>

                            <p class="mt-2 text-[length:var(--font-xs)] text-[var(--muted)]">
                                No has agregado ningún medicamento a esta receta.
                            </p>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="(detalle, idx) in form.receta.detalles" :key="idx"
                                class="relative rounded-2xl border bg-[var(--card)] p-5 shadow-sm"
                                style="border-color: var(--border)">
                                <button type="button" @click="removeRecetaDetalle(idx)" v-if="tieneRol('Doctor')"
                                    class="absolute right-4 top-4 text-[var(--muted)] transition hover:text-red-500"
                                    title="Eliminar este medicamento">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <div class="grid gap-4 md:grid-cols-4 pr-6">
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)]">Medicamento (Descripción) <span class="text-red-500">*</span></label>
                                        <input
                                            v-model="detalle.descripcion" :disabled="!tieneRol('Doctor')"
                                            type="text"
                                            required
                                            placeholder="Ej: Ibuprofeno 400mg"
                                            class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--primary)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)]">Dosis <span class="text-red-500">*</span></label>
                                        <input
                                            v-model="detalle.dosis"
                                            :disabled="!tieneRol('Doctor')"
                                            type="text"
                                            required
                                            placeholder="Ej: 1 tableta"
                                            class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--primary)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)]">Frecuencia <span class="text-red-500">*</span></label>
                                        <input
                                            v-model="detalle.frecuencia"
                                            :disabled="!tieneRol('Doctor')"
                                            type="text"
                                            required
                                            placeholder="Ej: Cada 8 horas"
                                            class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--primary)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)]">Duración <span class="text-red-500">*</span></label>
                                        <input
                                            v-model="detalle.duracion"
                                            :disabled="!tieneRol('Doctor')"
                                            type="text"
                                            required
                                            placeholder="Ej: 3 días"
                                            class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:border-[var(--primary)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: Servicios Prestados -->
            <div v-show="activeFormTab === 'servicios'" class="space-y-6">
                <div class="flex items-center justify-between border-b pb-3"
                    style="border-color: var(--border)">
                    <div>
                        <h3 class="text-[length:var(--font-base)] font-black text-[var(--title)]">
                            Servicios Prestados a este Tratamiento
                        </h3>
                        <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                            Asocia los servicios clínicos que se le prestarán al paciente durante este tratamiento.
                        </p>
                    </div>
                    <button type="button" @click="addServicio"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[var(--primary-soft)] px-3.5 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:opacity-90">
                        + Agregar Servicio
                    </button>
                </div>

                <div v-if="form.servicios_prestados.length === 0"
                    class="text-center py-12 rounded-2xl border border-dashed bg-[var(--section-soft)]"
                    style="border-color: var(--border)">
                    <span class="text-3xl">🛠️</span>
                    <h4 class="mt-3 text-[length:var(--font-sm)] font-black text-[var(--title)]">No hay servicios asociados</h4>
                    <p class="text-[length:var(--font-xs)] text-[var(--muted)] mt-1 max-w-xs mx-auto">
                        Presiona el botón de arriba para agregar servicios prestados a este tratamiento.
                    </p>
                </div>

                <div v-else class="space-y-4">
                    <div v-for="(sp, idx) in form.servicios_prestados" :key="idx"
                        class="relative rounded-2xl border bg-[var(--section-soft)] p-5 shadow-sm space-y-4"
                        style="border-color: var(--border)">
                        <button type="button" @click="removeServicio(idx)"
                            class="absolute right-4 top-4 text-[var(--muted)] transition hover:text-red-500"
                            title="Remover este servicio">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div class="grid gap-4 md:grid-cols-4 pr-6">
                            <!-- Servicio Selection -->
                            <div class="md:col-span-2">
                                <label
                                    class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)]">Seleccionar Servicio <span class="text-red-500">*</span></label>
                                <select v-model="sp.servicio_id" required @change="handleServicioChange(idx)"
                                    class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none focus:border-[var(--primary)]"
                                    style="border-color: var(--border)">
                                    <option v-for="serv in (servicios || [])" :key="serv.id" :value="serv.id">
                                        {{ serv.nombre }} ({{ serv.tipo }})
                                    </option>
                                </select>
                            </div>

                            <!-- Fecha de Servicio -->
                            <div>
                                <label
                                    class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)]">Fecha del Servicio <span class="text-red-500">*</span></label>
                                <input v-model="sp.fecha_servicio" type="date" required
                                    class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none focus:border-[var(--primary)]"
                                    style="border-color: var(--border)" />
                            </div>

                            <!-- Cantidad -->
                            <div>
                                <label
                                    class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)]">Cantidad <span class="text-red-500">*</span></label>
                                <input v-model.number="sp.cantidad" type="number" min="1" required
                                    class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none focus:border-[var(--primary)]"
                                    style="border-color: var(--border)" />
                            </div>

                            <!-- Precio Unitario -->
                            <div>
                                <label
                                    class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)]">Precio Unitario (Bs) <span class="text-red-500">*</span></label>
                                <input v-model.number="sp.precio" type="number" step="0.01" min="0" required
                                    readonly
                                    class="w-full rounded-xl border bg-[var(--section-soft)] cursor-not-allowed px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none"
                                    style="border-color: var(--border)" />
                            </div>

                            <!-- Descuento -->
                            <div>
                                <label
                                    class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)]">Descuento (Bs)</label>
                                <input v-model.number="sp.descuento" type="number" step="0.01" min="0"
                                    class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none focus:border-[var(--primary)]"
                                    style="border-color: var(--border)" />
                            </div>

                            <!-- Subtotal Calculado (Display) -->
                            <div>
                                <label
                                    class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider">Subtotal</label>
                                <div class="px-3 py-2.5 text-[length:var(--font-xs)] font-black text-[var(--title)] bg-[var(--card)] rounded-xl border"
                                    style="border-color: var(--border)">
                                    Bs. {{ ((sp.precio * sp.cantidad) - (sp.descuento || 0)).toFixed(2) }}
                                </div>
                            </div>

                            <!-- Observación -->
                            <div class="md:col-span-4">
                                <label
                                    class="mb-1 block text-[length:var(--font-xs)] font-bold text-[var(--muted)]">Observación</label>
                                <input v-model="sp.observacion" type="text"
                                    placeholder="Detalle u observaciones del servicio prestado para este paciente..."
                                    class="w-full rounded-xl border bg-[var(--card)] px-3 py-2.5 text-[length:var(--font-xs)] text-[var(--title)] outline-none focus:border-[var(--primary)]"
                                    style="border-color: var(--border)" />
                            </div>
                        </div>
                    </div>

                    <!-- Total Footer Card -->
                    <div class="flex justify-end pt-2">
                        <div class="rounded-2xl border bg-[var(--primary-soft)] px-6 py-3.5 text-right"
                            style="border-color: var(--border)">
                            <span
                                class="text-[length:var(--font-xs)] font-bold text-[var(--muted)] uppercase tracking-wider block">Total Servicios Prestados</span>
                            <span class="text-[length:var(--font-xl)] font-black text-[var(--primary)]">Bs. {{
                                totalServicios.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 border-t pt-4" style="border-color: var(--border)">
                <button type="button" @click="emit('cancel')"
                    class="rounded-2xl border bg-[var(--card)] px-8 py-4 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)]"
                    style="border-color: var(--border)">
                    Cancelar
                </button>

                <button type="submit" :disabled="form.processing"
                    class="rounded-2xl bg-[var(--text)] px-8 py-4 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:opacity-60">
                    {{ form.processing ? 'Guardando...' : editMode ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>
