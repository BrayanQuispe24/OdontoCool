<script setup lang="ts">
import { computed, nextTick, ref, onMounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import type { Cita, EstadoCita, Doctor } from '@/types/Cita';
import type { Paciente } from '@/types/Paciente';
import type { Secretaria } from '@/types/Secretaria';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CitaForm from '@/components/Cita/CitaForm.vue';
import CitaCardList from '@/components/Cita/CitaCardList.vue';
import CitaEmptyState from '@/components/Cita/CitaEmptyState.vue';

// Calendario Components
import CalendarHeader from '@/components/Cita/Calendario/CalendarHeader.vue';
import CalendarSidebar from '@/components/Cita/Calendario/CalendarSidebar.vue';
import CalendarGrid from '@/components/Cita/Calendario/CalendarGrid.vue';
import CalendarModal from '@/components/Cita/Calendario/CalendarModal.vue';

const page = usePage<PageProps>();

const authUser = computed(() => page.props.auth.user);
const permisos = computed<string[]>(() => page.props.auth.permisos ?? []);

const props = defineProps<{
    estadoCitas: EstadoCita[];
    citas: Cita[];
    pacientes: Paciente[];
    doctores: Doctor[];
    secretarias?: Secretaria[];
}>();

// States
const viewMode = ref<'day' | 'week' | 'month'>('month');
const currentDate = ref(new Date());

const searchPaciente = ref('');
const estadoFiltro = ref('');
const doctorFiltro = ref('');

const showForm = ref(false);
const selectedCita = ref<Cita | null>(null);
const defaultDate = ref('');
const defaultHour = ref('');
const formSection = ref<HTMLElement | null>(null);

// Modal details state
const showModal = ref(false);
const modalCita = ref<Cita | null>(null);

const tienePermiso = (permiso: string) => {
    return permisos.value.includes(permiso);
};

const tieneRol = (rol: string) => {
    return authUser.value?.rol?.nombre === rol;
};

const esPaciente = computed(() => tieneRol('Paciente'));
const esDoctor = computed(() => tieneRol('Doctor'));

const doctorLogueado = computed(() => {
    if (!esDoctor.value) {
        return null;
    }
    return props.doctores.find((doctor) => {
        return doctor.persona_id === authUser.value?.persona_id;
    }) ?? null;
});

const doctorLogueadoCi = computed(() => {
    return doctorLogueado.value?.codigo_doctor ?? '';
});

const citasDeRol = computed(() => {
    if (esDoctor.value) {
        return props.citas.filter((cita) => cita.doctor_ci === doctorLogueadoCi.value);
    }
    return props.citas;
});

const soloLecturaPaciente = computed(() => {
    return esPaciente.value && selectedCita.value !== null;
});

const estadosDisponibles = computed(() => {
    return props.estadoCitas
        .filter((estado) => {
            const nombre = estado.nombre.toLowerCase();
            return nombre !== 'cancelada' && nombre !== 'cancelado';
        })
        .map((estado) => estado.nombre);
});

// Full filtering including Doctor filter
const citasFiltradas = computed(() => {
    const pacienteTerm = searchPaciente.value.toLowerCase();
    const estadoSeleccionado = estadoFiltro.value.toLowerCase();
    const docSeleccionado = doctorFiltro.value.toLowerCase();

    return citasDeRol.value.filter((cita) => {
        const paciente = `${cita.paciente?.persona?.nombre ?? ''} ${cita.paciente?.persona?.apellido ?? ''}`.toLowerCase();
        const estado = cita.ultimo_estado_asignado?.estado_cita?.nombre?.toLowerCase() ?? '';
        const docCi = cita.doctor_ci?.toLowerCase() ?? '';

        const coincidePaciente =
            pacienteTerm === '' || paciente.includes(pacienteTerm);

        const coincideEstado =
            estadoSeleccionado === '' || estado === estadoSeleccionado;

        const coincideDoctor =
            docSeleccionado === '' || docCi === docSeleccionado;

        return coincidePaciente && coincideEstado && coincideDoctor;
    });
});

const pacienteLogueado = computed(() => {
    if (!esPaciente.value) {
        return null;
    }

    return props.pacientes.find((paciente) => {
        return paciente.persona_id === authUser.value?.persona_id;
    }) ?? null;
});

const pacienteLogueadoCi = computed(() => {
    return pacienteLogueado.value?.codigo_paciente ?? '';
});

const puedeBuscarPaciente = computed(() => {
    return ['Secretaria', 'Propietario', 'Doctor'].includes(
        authUser.value?.rol?.nombre ?? ''
    );
});

// Scroll to form helper
const desplazarAlFormulario = async () => {
    await nextTick();
    formSection.value?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
};

const nuevaCita = async () => {
    selectedCita.value = null;
    defaultDate.value = '';
    defaultHour.value = '';
    showForm.value = true;
    await desplazarAlFormulario();
};

const editarCita = async (cita: Cita) => {
    selectedCita.value = cita;
    showForm.value = true;
    await desplazarAlFormulario();
};

const cerrarFormulario = () => {
    showForm.value = false;
    selectedCita.value = null;
    defaultDate.value = '';
    defaultHour.value = '';
};

const formularioGuardado = () => {
    cerrarFormulario();
};

const limpiarFiltros = () => {
    searchPaciente.value = '';
    estadoFiltro.value = '';
    doctorFiltro.value = '';
};

const eliminarCita = (citaId: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta cita?')) {
        router.delete(route('citas.destroy', citaId), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                modalCita.value = null;
            }
        });
    }
};

// Format current view range string dynamically
const formattedCurrentRange = computed(() => {
    const months = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    const date = currentDate.value;
    const year = date.getFullYear();
    
    if (viewMode.value === 'month') {
        return `${months[date.getMonth()]} de ${year}`;
    } else if (viewMode.value === 'week') {
        const day = date.getDay();
        const shift = day === 0 ? -6 : -(day - 1);
        const monday = new Date(date);
        monday.setDate(date.getDate() + shift);
        return `Semana del ${monday.getDate()} de ${months[monday.getMonth()]}, ${monday.getFullYear()}`;
    } else {
        return `${date.getDate()} de ${months[date.getMonth()]}, ${year}`;
    }
});

// Helper to format Date objects as 'YYYY-MM-DD'
const formatLocalDate = (d: Date) => {
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// Statistics selectors
const citasHoy = computed(() => {
    const todayStr = formatLocalDate(new Date());
    return citasDeRol.value.filter(c => c.fecha_cita === todayStr);
});

const citasHoyCount = computed(() => citasHoy.value.length);

const citasPendientesCount = computed(() => {
    return citasHoy.value.filter(c => {
        const name = c.ultimo_estado_asignado?.estado_cita?.nombre?.toLowerCase() ?? '';
        return name === 'pendiente';
    }).length;
});

const citasCanceladasCount = computed(() => {
    return citasHoy.value.filter(c => {
        const name = c.ultimo_estado_asignado?.estado_cita?.nombre?.toLowerCase() ?? '';
        return name === 'cancelada' || name === 'cancelado';
    }).length;
});

const citasDisponiblesCount = computed(() => {
    const busyMinutes = citasHoy.value
        .filter(c => {
            const name = c.ultimo_estado_asignado?.estado_cita?.nombre?.toLowerCase() ?? '';
            return name !== 'cancelada' && name !== 'cancelado';
        })
        .reduce((sum, c) => {
            if (!c.hora_inicio || !c.hora_fin) return sum + 60;
            const [h1, m1] = c.hora_inicio.split(':').map(Number);
            const [h2, m2] = c.hora_fin.split(':').map(Number);
            const diff = (h2 * 60 + m2) - (h1 * 60 + m1);
            return sum + (diff > 0 ? diff : 60);
        }, 0);
    const busyHours = busyMinutes / 60;
    return Math.max(0, 10 - busyHours);
});

const proximasCitas = computed(() => {
    const now = new Date();
    const todayStr = formatLocalDate(now);
    const currentHour = now.getHours() + now.getMinutes() / 60;
    
    return citasDeRol.value
        .filter(c => {
            if (!c.fecha_cita) return false;
            if (c.fecha_cita > todayStr) return true;
            if (c.fecha_cita === todayStr) {
                const startHour = c.hora_inicio ? c.hora_inicio.split(':').map(Number)[0] + c.hora_inicio.split(':').map(Number)[1] / 60 : 0;
                return startHour >= currentHour;
            }
            return false;
        })
        .sort((a, b) => {
            if (a.fecha_cita !== b.fecha_cita) {
                return a.fecha_cita.localeCompare(b.fecha_cita);
            }
            return (a.hora_inicio ?? '').localeCompare(b.hora_inicio ?? '');
        })
        .slice(0, 5);
});

// Navigation handlers
const handlePrev = () => {
    const d = new Date(currentDate.value);
    if (viewMode.value === 'month') {
        d.setMonth(d.getMonth() - 1);
    } else if (viewMode.value === 'week') {
        d.setDate(d.getDate() - 7);
    } else {
        d.setDate(d.getDate() - 1);
    }
    currentDate.value = d;
};

const handleNext = () => {
    const d = new Date(currentDate.value);
    if (viewMode.value === 'month') {
        d.setMonth(d.getMonth() + 1);
    } else if (viewMode.value === 'week') {
        d.setDate(d.getDate() + 7);
    } else {
        d.setDate(d.getDate() + 1);
    }
    currentDate.value = d;
};

const handleToday = () => {
    currentDate.value = new Date();
};

const handleSelectDate = (date: Date) => {
    currentDate.value = date;
    viewMode.value = 'day';
};

// Event Modal trigger
const handleEventClick = (cita: Cita) => {
    modalCita.value = cita;
    showModal.value = true;
};

const handleEventDblClick = (cita: Cita) => {
    showModal.value = false;
    modalCita.value = null;
    editarCita(cita);
};

// Click empty space trigger
const handleEmptySlotClick = (date: Date, hourStr: string) => {
    selectedCita.value = null;
    defaultDate.value = formatLocalDate(date);
    defaultHour.value = hourStr;
    showForm.value = true;
    desplazarAlFormulario();
};

// Drag & drop update call
const handleDragStart = (e: DragEvent, cita: Cita) => {
    if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('application/json', JSON.stringify(cita));
    }
};

const handleDropEvent = (cita: Cita, targetDate: Date, targetHourStr: string) => {
    const formattedDate = formatLocalDate(targetDate);
    router.put(route('citas.update', cita.id_cita), {
        fecha_cita: formattedDate,
        hora_inicio: targetHourStr,
        motivo: cita.motivo,
        observacion: cita.observacion,
        paciente_ci: cita.paciente_ci,
        codigo_historial: cita.codigo_historial,
        secretaria_ci: cita.secretaria_ci,
        doctor_ci: cita.doctor_ci,
        estado_cita_id: cita.ultimo_estado_asignado?.estado_cita?.id || '',
    }, {
        preserveScroll: true,
    });
};

// Resize update call
const handleResizeEvent = (cita: Cita, newDurationMinutes: number) => {
    const [h, m] = cita.hora_inicio!.split(':').map(Number);
    const totalMinutes = h * 60 + m + newDurationMinutes;
    const newH = Math.min(23, Math.floor(totalMinutes / 60));
    const newM = Math.min(59, totalMinutes % 60);
    const newHoraFin = `${String(newH).padStart(2, '0')}:${String(newM).padStart(2, '0')}`;

    router.put(route('citas.update', cita.id_cita), {
        fecha_cita: cita.fecha_cita,
        hora_inicio: cita.hora_inicio?.substring(0, 5),
        hora_fin: newHoraFin,
        motivo: cita.motivo,
        observacion: cita.observacion,
        paciente_ci: cita.paciente_ci,
        codigo_historial: cita.codigo_historial,
        secretaria_ci: cita.secretaria_ci,
        doctor_ci: cita.doctor_ci,
        estado_cita_id: cita.ultimo_estado_asignado?.estado_cita?.id || '',
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Calendario de Citas" />

    <AuthenticatedLayout>
        <template #title>
            Citas Médicas
        </template>

        <section ref="formSection"
            class="space-y-6 text-[length:var(--font-base)] text-[var(--text)] transition-colors duration-300">
            
            <!-- Calendar Main Layout (Desktop only >= lg) -->
            <div class="hidden lg:flex flex-col gap-5">
                <CalendarHeader
                    v-model:modelValueView="viewMode"
                    v-model:searchPaciente="searchPaciente"
                    v-model:estadoFiltro="estadoFiltro"
                    v-model:doctorFiltro="doctorFiltro"
                    :doctores="doctores"
                    :estados-disponibles="estadosDisponibles"
                    :formatted-current-range="formattedCurrentRange"
                    :tiene-permiso-crear="tienePermiso('citas.store')"
                    :es-doctor="esDoctor"
                    :doctor-logueado-ci="doctorLogueadoCi"
                    @prev="handlePrev"
                    @next="handleNext"
                    @today="handleToday"
                    @create="nuevaCita"
                />

                <div class="flex gap-6 items-stretch">
                    <!-- Sidebar layout -->
                    <CalendarSidebar
                        :current-date="currentDate"
                        :citas-hoy-count="citasHoyCount"
                        :citas-pendientes-count="citasPendientesCount"
                        :citas-canceladas-count="citasCanceladasCount"
                        :citas-disponibles-count="citasDisponiblesCount"
                        :proximas-citas="proximasCitas"
                        @select-date="handleSelectDate"
                    />

                    <!-- Calendar Grid -->
                    <CalendarGrid
                        :view="viewMode"
                        :current-date="currentDate"
                        :citas="citasFiltradas"
                        @click-event="handleEventClick"
                        @dblclick-event="handleEventDblClick"
                        @drag-start="handleDragStart"
                        @drop-event="handleDropEvent"
                        @resize-event="handleResizeEvent"
                        @click-empty-slot="handleEmptySlotClick"
                    />
                </div>
            </div>

            <!-- Mobile View (Agenda/List format < lg) -->
            <div class="block lg:hidden space-y-4">
                <div class="rounded-3xl border bg-[var(--card)] p-5 shadow-[0_15px_40px_rgba(0,169,157,0.06)]"
                    style="border-color: var(--border)">
                    <div class="flex flex-col gap-4">
                        <div>
                            <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.2em] text-[var(--primary)]">
                                Citas
                            </p>
                            <h1 class="text-xl font-black text-[var(--title)] mt-1">
                                Mis citas médicas
                            </h1>
                        </div>
                        <div class="flex flex-col gap-2">
                            <input v-model="searchPaciente" type="text" placeholder="Buscar paciente..."
                                class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-3 text-xs outline-none focus:bg-[var(--card)] focus:ring-2"
                                style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />
                            
                            <div class="grid grid-cols-2 gap-2">
                                <select v-model="estadoFiltro"
                                    class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-3 text-xs outline-none focus:bg-[var(--card)]"
                                    style="border-color: var(--border)">
                                    <option value="">Todos los estados</option>
                                    <option v-for="est in estadosDisponibles" :key="est" :value="est">
                                        {{ est }}
                                    </option>
                                </select>

                                <button v-if="tienePermiso('citas.store')" type="button" @click="nuevaCita"
                                    class="rounded-2xl bg-[var(--primary)] px-4 py-3 text-xs font-black text-white shadow-md">
                                    + Nueva Cita
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <CitaCardList
                    v-if="citasFiltradas.length"
                    :citas="citasFiltradas"
                    :tiene-permiso-update="tienePermiso('citas.update')"
                    :tiene-permiso-destroy="tienePermiso('citas.destroy')"
                    @edit="editarCita"
                    @delete="eliminarCita"
                />
                
                <CitaEmptyState v-else />
            </div>

            <!-- Form section (slide down panel) -->
            <CitaForm
                v-if="showForm"
                :cita="selectedCita"
                :pacientes="pacientes"
                :doctores="doctores"
                :secretarias="secretarias"
                :estado-citas="estadoCitas"
                :solo-lectura-paciente="soloLecturaPaciente"
                :es-paciente="esPaciente"
                :paciente-logueado-ci="pacienteLogueadoCi"
                :es-doctor="esDoctor"
                :doctor-logueado-ci="doctorLogueadoCi"
                :default-date="defaultDate"
                :default-hour="defaultHour"
                @saved="formularioGuardado"
                @cancel="cerrarFormulario"
            />

            <!-- Event Details Modal -->
            <CalendarModal
                :cita="modalCita"
                :show="showModal"
                @close="showModal = false"
                @edit="handleEventDblClick"
                @delete="eliminarCita"
            />

        </section>
    </AuthenticatedLayout>
</template>