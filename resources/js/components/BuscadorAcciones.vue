<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import type { Modulo, PageProps } from '@/types';

type AccionSistema = {
    modulo?: string;
    permiso?: string | string[];
    titulo: string;
    descripcion: string;
    ruta: string;
    icono: string;
    palabras: string[];
    grupo: 'principal' | 'gestion' | 'operacion';
    always?: boolean;
};
defineProps<{
    compact?: boolean;
}>();

const page = usePage<PageProps>();

const busqueda = ref('');
const abierto = ref(false);
const seleccionado = ref(0);

const showAccessMessage = ref(false);
const accessMessage = ref('No tienes acceso a esta acción.');

let accessMessageTimeout: number | null = null;

const authUser = computed(() => page.props.auth.user);
const modulos = computed<Modulo[]>(() => page.props.auth.modulos ?? []);
const permisos = computed<string[]>(() => page.props.auth.permisos ?? []);

const tieneRol = (rol: string) => {
    return authUser.value?.rol?.nombre === rol;
};

const normalizarTexto = (texto: string): string => {
    return texto
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim()
        .toLowerCase();
};

const nombresModulosPermitidos = computed<Set<string>>(() => {
    return new Set(
        modulos.value.map((modulo) => normalizarTexto(modulo.nombre)),
    );
});

const permisosNormalizados = computed<Set<string>>(() => {
    return new Set(
        permisos.value.map((permiso) => normalizarTexto(permiso)),
    );
});

const tieneModulo = (modulo?: string): boolean => {
    if (!modulo) {
        return false;
    }

    return nombresModulosPermitidos.value.has(normalizarTexto(modulo));
};

const tienePermiso = (permiso?: string | string[]): boolean => {
    if (!permiso) {
        return true;
    }

    const permisosRequeridos = Array.isArray(permiso)
        ? permiso
        : [permiso];

    return permisosRequeridos.some((item) => {
        return permisosNormalizados.value.has(normalizarTexto(item));
    });
};

const puedeAccederAccion = (accion: AccionSistema): boolean => {
    if (accion.always) {
        return true;
    }

    if (!accion.modulo) {
        return false;
    }

    return tieneModulo(accion.modulo) && tienePermiso(accion.permiso);
};

const mostrarMensajeAcceso = (mensaje = 'No tienes acceso a esta acción.') => {
    accessMessage.value = mensaje;
    showAccessMessage.value = true;

    if (accessMessageTimeout) {
        clearTimeout(accessMessageTimeout);
    }

    accessMessageTimeout = window.setTimeout(() => {
        showAccessMessage.value = false;
    }, 3000);
};

const routeExiste = (nombreRuta: string): boolean => {
    try {
        return typeof route === 'function' && route().has(nombreRuta);
    } catch {
        return false;
    }
};

const acciones: AccionSistema[] = [
    {
        titulo: 'Resumen',
        descripcion: 'Ir al panel principal del sistema',
        ruta: 'dashboard',
        icono: '▦',
        grupo: 'principal',
        palabras: ['dashboard', 'inicio', 'resumen', 'panel principal'],
        always: !(tieneRol('Paciente') || tieneRol('Secretaria') || tieneRol('Doctor')),
    },

    {
        modulo: 'Citas',
        titulo: 'Citas',
        descripcion: 'Ver la lista de citas registradas',
        ruta: 'citas.index',
        icono: '📅',
        grupo: 'principal',
        palabras: ['citas', 'cita', 'ver citas', 'lista citas', 'agenda'],
    },
    {
        modulo: 'Citas',
        titulo: 'Registrar cita',
        descripcion: 'Crear una nueva cita',
        ruta: 'citas.index',
        icono: '➕',
        grupo: 'operacion',
        palabras: ['registrar cita', 'crear cita', 'nueva cita', 'agendar cita', 'cita registrar'],
    },

    {
        modulo: 'Tratamientos',
        titulo: 'Tratamientos',
        descripcion: 'Ver tratamientos registrados',
        ruta: 'tratamiento.index',
        icono: '♧',
        grupo: 'principal',
        palabras: ['tratamientos', 'tratamiento', 'ver tratamientos', 'lista tratamientos'],
    },
    {
        modulo: 'Tratamientos',
        titulo: 'Registrar tratamiento',
        descripcion: 'Crear un nuevo tratamiento',
        ruta: 'tratamiento.index',
        icono: '➕',
        grupo: 'operacion',
        palabras: ['registrar tratamiento', 'crear tratamiento', 'nuevo tratamiento'],
    },

    {
        modulo: 'Analisis',
        titulo: 'Análisis',
        descripcion: 'Ver análisis disponibles',
        ruta: 'analisis.index',
        icono: '🔬',
        grupo: 'principal',
        palabras: ['analisis', 'análisis', 'laboratorio', 'ver analisis', 'lista analisis'],
    },
    {
        modulo: 'Analisis',
        titulo: 'Registrar análisis',
        descripcion: 'Crear un nuevo análisis',
        ruta: 'analisis.index',
        icono: '➕',
        grupo: 'operacion',
        palabras: ['registrar analisis', 'registrar análisis', 'crear analisis', 'nuevo analisis'],
    },

    {
        modulo: 'Modulos',
        titulo: 'Módulos',
        descripcion: 'Administrar módulos del sistema',
        ruta: 'modulo.index',
        icono: '▤',
        grupo: 'principal',
        palabras: ['modulos', 'módulos', 'modulo', 'módulo', 'administrar modulos'],
    },

    {
        modulo: 'Permisos',
        titulo: 'Permisos',
        descripcion: 'Administrar permisos del sistema',
        ruta: 'permiso.index',
        icono: '⚡',
        grupo: 'principal',
        palabras: ['permisos', 'permiso', 'accesos', 'administrar permisos'],
    },

    {
        modulo: 'Roles',
        titulo: 'Roles',
        descripcion: 'Administrar roles del sistema',
        ruta: 'rol.index',
        icono: '⚔',
        grupo: 'principal',
        palabras: ['roles', 'rol', 'perfiles', 'administrar roles'],
    },

    {
        modulo: 'Propietarios',
        titulo: authUser.value?.rol?.nombre === 'Propietario' ? 'Mi perfil propietario' : 'Propietarios',
        descripcion: authUser.value?.rol?.nombre === 'Propietario'
            ? 'Ver mi información como propietario'
            : 'Ver propietarios registrados',
        ruta: 'administrador.index',
        icono: '👤',
        grupo: 'principal',
        palabras: ['propietarios', 'propietario', 'administradores', 'mi perfil propietario'],
    },

    {
        modulo: 'Doctores',
        titulo: authUser.value?.rol?.nombre === 'Doctor' ? 'Mi perfil doctor' : 'Doctores',
        descripcion: authUser.value?.rol?.nombre === 'Doctor'
            ? 'Ver mi información como doctor'
            : 'Ver doctores registrados',
        ruta: 'doctor.index',
        icono: '🦷',
        grupo: 'principal',
        palabras: ['doctores', 'doctor', 'odontologos', 'odontólogos', 'mi perfil doctor'],
    },
    {
        modulo: 'Doctores',
        titulo: 'Registrar doctor',
        descripcion: 'Crear un nuevo doctor',
        ruta: 'doctor.index',
        icono: '➕',
        grupo: 'operacion',
        palabras: ['registrar doctor', 'crear doctor', 'nuevo doctor', 'agregar doctor'],
    },

    {
        modulo: 'Secretarias',
        titulo: authUser.value?.rol?.nombre === 'Secretaria' ? 'Mi perfil secretaria' : 'Secretarias',
        descripcion: authUser.value?.rol?.nombre === 'Secretaria'
            ? 'Ver mi información como secretaria'
            : 'Ver secretarias registradas',
        ruta: 'secretaria.index',
        icono: '👩',
        grupo: 'principal',
        palabras: ['secretarias', 'secretaria', 'mi perfil secretaria'],
    },
    {
        modulo: 'Secretarias',
        titulo: 'Registrar secretaria',
        descripcion: 'Crear una nueva secretaria',
        ruta: 'secretaria.index',
        icono: '➕',
        grupo: 'operacion',
        palabras: ['registrar secretaria', 'crear secretaria', 'nueva secretaria'],
    },

    {
        modulo: 'Pacientes',
        titulo: authUser.value?.rol?.nombre === 'Paciente' ? 'Mi perfil paciente' : 'Pacientes',
        descripcion: authUser.value?.rol?.nombre === 'Paciente'
            ? 'Ver mi información como paciente'
            : 'Ver pacientes registrados',
        ruta: 'paciente.index',
        icono: '👨',
        grupo: 'principal',
        palabras: ['pacientes', 'paciente', 'ver pacientes', 'mi perfil paciente'],
    },
    {
        modulo: 'Pacientes',
        titulo: 'Registrar paciente',
        descripcion: 'Crear un nuevo paciente',
        ruta: 'paciente.index',
        icono: '➕',
        grupo: 'operacion',
        palabras: ['registrar paciente', 'crear paciente', 'nuevo paciente', 'agregar paciente', 'paciente registrar'],
    },

    {
        modulo: 'Servicios',
        titulo: 'Servicios',
        descripcion: 'Ver servicios odontológicos',
        ruta: 'servicio.index',
        icono: '⚕',
        grupo: 'principal',
        palabras: ['servicios', 'servicio', 'ver servicios', 'lista servicios'],
    },
    {
        modulo: 'Servicios',
        titulo: 'Registrar servicio',
        descripcion: 'Crear un nuevo servicio',
        ruta: 'servicio.index',
        icono: '➕',
        grupo: 'operacion',
        palabras: ['registrar servicio', 'crear servicio', 'nuevo servicio', 'agregar servicio'],
    },

    {
        modulo: 'Especialidades',
        titulo: 'Especialidades',
        descripcion: 'Ver especialidades odontológicas',
        ruta: 'especialidad.index',
        icono: '🦷',
        grupo: 'principal',
        palabras: ['especialidades', 'especialidad', 'ver especialidades'],
    },
    {
        modulo: 'Especialidades',
        titulo: 'Registrar especialidad',
        descripcion: 'Crear una nueva especialidad',
        ruta: 'especialidad.create',
        icono: '➕',
        grupo: 'operacion',
        palabras: ['registrar especialidad', 'crear especialidad', 'nueva especialidad'],
    },

    {
        modulo: 'Turnos',
        titulo: 'Turnos',
        descripcion: 'Ver turnos disponibles',
        ruta: 'turnos.index',
        icono: '⏰',
        grupo: 'principal',
        palabras: ['turnos', 'turno', 'horarios', 'ver turnos'],
    },
    {
        modulo: 'Turnos',
        titulo: 'Registrar turno',
        descripcion: 'Crear un nuevo turno',
        ruta: 'turnos.create',
        icono: '➕',
        grupo: 'operacion',
        palabras: ['registrar turno', 'crear turno', 'nuevo turno', 'agregar turno'],
    },

    {
        modulo: 'Historial Clinico',
        titulo: 'Historiales clínicos',
        descripcion: 'Ver historiales clínicos de pacientes',
        ruta: 'historial_clinico.index',
        icono: '▤',
        grupo: 'gestion',
        palabras: ['historial', 'historial clinico', 'historial clínico', 'historiales'],
    },

    {
        modulo: 'Diagnosticos',
        titulo: 'Diagnósticos',
        descripcion: 'Ver diagnósticos registrados',
        ruta: 'diagnostico.index',
        icono: '📋',
        grupo: 'gestion',
        palabras: ['diagnosticos', 'diagnósticos', 'diagnostico', 'diagnóstico'],
    },

    {
        modulo: 'Boleta Pago',
        titulo: 'Boleta de Pago',
        descripcion: 'Ver boletas de pago',
        ruta: 'boleta_pago.index',
        icono: '💰',
        grupo: 'gestion',
        palabras: ['boleta', 'boleta pago', 'pagos', 'cobros', 'finanzas'],
    },

    {
        modulo: 'Reportes',
        titulo: 'Reportes',
        descripcion: 'Ver reportes y estadísticas del sistema',
        ruta: 'reportes.index',
        icono: '📊',
        grupo: 'gestion',
        palabras: ['reportes', 'reporte', 'estadisticas', 'estadísticas', 'graficos', 'gráficos'],
    },

    {
        modulo: 'Bitacora',
        titulo: 'Bitácora',
        descripcion: 'Ver acciones registradas en el sistema',
        ruta: 'bitacora.index',
        icono: '📋',
        grupo: 'gestion',
        palabras: ['bitacora', 'bitácora', 'auditoria', 'auditoría', 'logs'],
    },
];

const accionesFiltradas = computed<AccionSistema[]>(() => {
    const texto = normalizarTexto(busqueda.value);

    const accionesBase = acciones.filter((accion) => {
        if (!accion.always && !accion.modulo) {
            return false;
        }

        return true;
    });

    if (!texto) {
        return accionesBase.slice(0, 8);
    }

    return accionesBase
        .filter((accion) => {
            const titulo = normalizarTexto(accion.titulo);
            const descripcion = normalizarTexto(accion.descripcion);
            const modulo = normalizarTexto(accion.modulo ?? '');
            const palabras = accion.palabras.map((palabra) => normalizarTexto(palabra));

            return (
                titulo.includes(texto) ||
                descripcion.includes(texto) ||
                modulo.includes(texto) ||
                palabras.some((palabra) => palabra.includes(texto)) ||
                palabras.some((palabra) => texto.includes(palabra))
            );
        })
        .slice(0, 10);
});

const abrir = () => {
    abierto.value = true;
    seleccionado.value = 0;
};

const cerrar = () => {
    abierto.value = false;
    seleccionado.value = 0;
};

const bajar = () => {
    if (accionesFiltradas.value.length === 0) {
        return;
    }

    seleccionado.value =
        seleccionado.value >= accionesFiltradas.value.length - 1
            ? 0
            : seleccionado.value + 1;
};

const subir = () => {
    if (accionesFiltradas.value.length === 0) {
        return;
    }

    seleccionado.value =
        seleccionado.value <= 0
            ? accionesFiltradas.value.length - 1
            : seleccionado.value - 1;
};

const navegar = (accion: AccionSistema) => {
    if (!puedeAccederAccion(accion)) {
        const nombreModulo = accion.modulo ?? accion.titulo;
        mostrarMensajeAcceso(`No tienes acceso a ${nombreModulo}.`);
        return;
    }

    if (!routeExiste(accion.ruta)) {
        mostrarMensajeAcceso(`La ruta ${accion.ruta} no está disponible.`);
        return;
    }

    busqueda.value = '';
    cerrar();

    router.visit(route(accion.ruta));
};

const ejecutarSeleccionado = () => {
    const accion = accionesFiltradas.value[seleccionado.value];

    if (!accion) {
        return;
    }

    navegar(accion);
};

const abrirConAtajo = (event: KeyboardEvent) => {
    const isCtrlK = event.ctrlKey && event.key.toLowerCase() === 'k';
    const isCommandK = event.metaKey && event.key.toLowerCase() === 'k';

    if (!isCtrlK && !isCommandK) {
        return;
    }

    event.preventDefault();

    abrir();

    window.setTimeout(() => {
        const input = document.getElementById('buscador-acciones-input') as HTMLInputElement | null;
        input?.focus();
    }, 50);
};

onMounted(() => {
    window.addEventListener('keydown', abrirConAtajo);
});

onUnmounted(() => {
    window.removeEventListener('keydown', abrirConAtajo);

    if (accessMessageTimeout) {
        clearTimeout(accessMessageTimeout);
    }
});
</script>

<template>
    <div class="relative isolate w-full">
        <div class="group flex items-center gap-3 rounded-2xl  bg-[var(--card)] shadow-sm transition duration-200 hover:shadow-md focus-within:border-[var(--primary)] focus-within:ring-4 focus-within:ring-[var(--primary-soft)]"
            :class="compact ? 'px-3 py-2' : 'px-3.5 py-3'" style="border-color: var(--border)">


            <div class="min-w-0 flex-1 flex">
                <input id="buscador-acciones-input" v-model="busqueda" type="text"
                    placeholder="Buscar acción... Ej: registrar paciente"
                    class=" rounded-xl w-full bg-transparent text-[length:var(--font-sm)] font-semibold text-[var(--title)] outline-none placeholder:font-semibold placeholder:text-[var(--muted)]"
                    @focus="abrir" @keydown.down.prevent="bajar" @keydown.up.prevent="subir"
                    @keydown.enter.prevent="ejecutarSeleccionado" @keydown.esc.prevent="cerrar" />
            </div>

            <span
                class="hidden shrink-0 rounded-xl border bg-[var(--section-soft)] px-2.5 py-1.5 text-[length:var(--font-xs)] font-black text-[var(--muted)] shadow-sm sm:block"
                style="border-color: var(--border)">
                Ctrl K
            </span>
        </div>

        <!-- CAPA PARA CERRAR -->
        <div v-if="abierto" class="fixed inset-0 z-[9996]" @click="cerrar"></div>

        <!-- RESULTADOS -->
        <Transition enter-active-class="transition duration-200 ease-out"
            enter-from-class="-translate-y-2 opacity-0 scale-95" enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in" leave-from-class="translate-y-0 opacity-100 scale-100"
            leave-to-class="-translate-y-2 opacity-0 scale-95">
            <div v-if="abierto"
                class="absolute left-1/2 top-[calc(100%+0.65rem)] z-[9997] w-[min(calc(100vw-1rem),42rem)] -translate-x-1/2 overflow-hidden rounded-2xl border bg-[var(--card)] shadow-[0_24px_60px_rgba(15,23,42,0.18)]"
                style="border-color: var(--border)">
                <!-- CABECERA DEL DROPDOWN -->
                <div class="border-b bg-[var(--section-soft)] px-4 py-3" style="border-color: var(--border)">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p
                                class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                                Buscador interno
                            </p>

                            <p class="mt-0.5 text-[length:var(--font-xs)] font-medium text-[var(--muted)]">
                                Acciones disponibles según tus módulos y permisos
                            </p>
                        </div>

                        <button type="button"
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--card)] hover:text-red-500"
                            @click="cerrar">
                            ✕
                        </button>
                    </div>
                </div>

                <!-- LISTA DE RESULTADOS -->
                <div v-if="accionesFiltradas.length > 0" class="max-h-[340px] overflow-y-auto p-2">
                    <button v-for="(accion, index) in accionesFiltradas" :key="accion.ruta + accion.titulo"
                        type="button"
                        class="group/result flex w-full items-start gap-3 rounded-2xl px-3 py-2.5 text-left transition duration-150"
                        :class="[
                            index === seleccionado
                                ? 'bg-[var(--primary)] text-white shadow-md'
                                : 'text-[var(--title)] hover:bg-[var(--section-soft)]',
                            !puedeAccederAccion(accion)
                                ? 'opacity-70 grayscale'
                                : '',
                        ]" @mouseenter="seleccionado = index" @mousedown.prevent="navegar(accion)">
                        <!-- ICONO -->
                        <span
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-[length:var(--font-lg)] shadow-sm transition"
                            :class="index === seleccionado
                                ? 'bg-white/20 text-white'
                                : puedeAccederAccion(accion)
                                    ? 'bg-[var(--primary-soft)] text-[var(--primary)]'
                                    : 'bg-red-100 text-red-500'
                                ">
                            {{ puedeAccederAccion(accion) ? accion.icono : '🔒' }}
                        </span>

                        <!-- TEXTO -->
                        <span class="min-w-0 flex-1 py-0.5">
                            <span class="flex min-w-0 items-center gap-2">
                                <span class="block truncate text-[length:var(--font-sm)] font-black leading-5">
                                    {{ accion.titulo }}
                                </span>

                                <span v-if="!puedeAccederAccion(accion)"
                                    class="shrink-0 rounded-full px-2 py-0.5 text-[length:var(--font-xs)] font-black"
                                    :class="index === seleccionado
                                        ? 'bg-white/20 text-white'
                                        : 'bg-red-100 text-red-500'
                                        ">
                                    Sin acceso
                                </span>
                            </span>

                            <span class="mt-1 block truncate text-[length:var(--font-xs)] font-medium"
                                :class="index === seleccionado ? 'text-white/80' : 'text-[var(--muted)]'">
                                {{ accion.descripcion }}
                            </span>

                            <span class="mt-2 flex flex-wrap items-center gap-2">
                                <span v-if="accion.modulo"
                                    class="inline-flex rounded-full px-2.5 py-1 text-[length:var(--font-xs)] font-black"
                                    :class="index === seleccionado
                                        ? 'bg-white/20 text-white'
                                        : 'bg-[var(--section-soft)] text-[var(--primary)]'
                                        ">
                                    {{ accion.modulo }}
                                </span>

                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-[length:var(--font-xs)] font-black"
                                    :class="index === seleccionado
                                        ? 'bg-white/20 text-white'
                                        : 'bg-[var(--primary-soft)] text-[var(--primary)]'
                                        ">
                                    {{ accion.grupo }}
                                </span>
                            </span>
                        </span>

                        <!-- ENTER -->
                        <span
                            class="hidden shrink-0 rounded-xl px-2.5 py-1.5 text-[length:var(--font-xs)] font-black sm:block"
                            :class="index === seleccionado
                                ? 'bg-white/20 text-white'
                                : 'bg-[var(--section-soft)] text-[var(--muted)]'
                                ">
                            Enter
                        </span>
                    </button>
                </div>

                <!-- SIN RESULTADOS -->
                <div v-else class="p-6 text-center">
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-[var(--section-soft)] text-[length:var(--font-xl)] text-[var(--primary)]">
                        🔍
                    </div>

                    <p class="mt-4 text-[length:var(--font-base)] font-black text-[var(--title)]">
                        No se encontraron acciones
                    </p>

                    <p class="mx-auto mt-2 max-w-sm text-[length:var(--font-sm)] leading-6 text-[var(--muted)]">
                        Intenta buscar con palabras como paciente, servicios, registrar cita, reportes o boleta.
                    </p>
                </div>

                <!-- PIE -->
                <div class="border-t bg-[var(--section-soft)] px-4 py-2.5" style="border-color: var(--border)">
                    <div
                        class="flex flex-wrap items-center justify-between gap-2 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                        <span>
                            ↑ ↓ para navegar
                        </span>

                        <span>
                            Enter para abrir · Esc para cerrar
                        </span>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- MENSAJE DE ACCESO DENEGADO -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-200 ease-out"
                enter-from-class="translate-y-3 opacity-0 scale-95" enter-to-class="translate-y-0 opacity-100 scale-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="translate-y-0 opacity-100 scale-100"
                leave-to-class="translate-y-3 opacity-0 scale-95">
                <div v-if="showAccessMessage"
                    class="fixed bottom-6 right-6 z-[10001] w-[calc(100vw-2rem)] max-w-[360px] rounded-2xl border border-red-200 bg-white p-4 shadow-[0_18px_50px_rgba(239,68,68,0.22)]">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-red-100 text-[length:var(--font-lg)] text-red-600">
                            ⚠
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-[length:var(--font-sm)] font-black text-red-700">
                                Acceso denegado
                            </p>

                            <p class="mt-1 text-[length:var(--font-xs)] font-medium text-red-500">
                                {{ accessMessage }}
                            </p>
                        </div>

                        <button type="button"
                            class="rounded-xl p-1.5 text-red-400 transition hover:bg-red-50 hover:text-red-600"
                            @click="showAccessMessage = false">
                            ✕
                        </button>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>