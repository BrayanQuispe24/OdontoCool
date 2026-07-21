<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useTheme, type Theme } from '@/composable/useTheme';
import { useFontSize, type FontSize } from '@/composable/useFontSize';
import { Modulo, PageProps } from '@/types';


defineProps({
    open: Boolean,
    collapsed: Boolean,
});

const emit = defineEmits(['close', 'toggle-collapse']);

const page = usePage<PageProps>();
const authUser = computed(() => page.props.auth.user);
const modulos = computed<Modulo[]>(() => page.props.auth.modulos ?? []);
const permisos = computed<string[]>(() => page.props.auth.permisos ?? []);
const tieneRol = (rol: string) => {
    return authUser.value?.rol?.nombre === rol;
};

// console.log('authUser:', authUser.value);
// console.log('modulos:', modulos.value);
// console.log('permisos:', permisos.value);

const {
    theme,
    autoThemeEnabled,
    suggestedThemeLabel,
    applyTheme,
    enableAutoTheme,
    disableAutoTheme,
    shouldAskForAutoTheme,
} = useTheme();
const { fontSize, applyFontSize, increaseFontSize, decreaseFontSize } = useFontSize();

const showSettings = ref(false);
const showAutoThemeQuestion = ref(false);
onMounted(() => {
    if (shouldAskForAutoTheme()) {
        showAutoThemeQuestion.value = true;
    }
});
//fotos
const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';

const assetUrl = (path: string | null | undefined): string | undefined => {
    if (!path) {
        return undefined;
    }

    if (
        path.startsWith('http://') ||
        path.startsWith('https://') ||
        path.startsWith('blob:')
    ) {
        return path;
    }

    const base = publicBase.replace(/\/+$/, '');

    // Si la ruta ya contiene "storage/", nos quedamos solo desde storage hacia adelante
    const storageIndex = path.indexOf('storage/');

    if (storageIndex !== -1) {
        const storagePath = path.substring(storageIndex);
        return `${base}/${storagePath}`;
    }

    const cleanPath = path.replace(/^\/+/, '');

    return `${base}/${cleanPath}`;
};


const fotoPerfil = computed(() => {
    return assetUrl(authUser.value?.url);
});

const nombreUsuario = computed<string>(() => {
    const nombre =
        authUser.value?.nombre_completo ??
        authUser.value?.name ??
        authUser.value?.nombre ??
        'Usuario';

    return typeof nombre === 'string' && nombre.trim() !== ''
        ? nombre
        : 'Usuario';
});

const inicialesUsuario = computed<string>(() => {
    return nombreUsuario.value
        .split(' ')
        .filter(Boolean)
        .map((parte: string) => parte.charAt(0))
        .slice(0, 2)
        .join('')
        .toUpperCase();
});

const themes: { label: string; value: Theme; icon: string }[] = [
    { label: 'Claro', value: 'light', icon: '☀' },
    { label: 'Oscuro', value: 'dark', icon: '🌙' },
    { label: 'OdontoCool', value: 'odontocool', icon: '🦷' },
    { label: 'Infantil', value: 'infantil', icon: '🧸' },
    { label: 'Juvenil', value: 'juvenil', icon: '⚡' },
    { label: 'Adulto', value: 'adulto', icon: '💼' },
];

const fontSizes: { label: string; value: FontSize; preview: string }[] = [
    { label: 'Pequeña', value: 'small', preview: 'A-' },
    { label: 'Normal', value: 'normal', preview: 'A' },
    { label: 'Grande', value: 'large', preview: 'A+' },
    { label: 'Muy grande', value: 'extra-large', preview: 'A++' },
];
const acceptAutoTheme = () => {
    enableAutoTheme();
    showAutoThemeQuestion.value = false;
};

const rejectAutoTheme = () => {
    disableAutoTheme();
    showAutoThemeQuestion.value = false;
};

type MenuItem = {
    modulo?: string;
    label: string;
    href: string;
    icon: string;
    grupo: 'principal' | 'gestion';
    always?: boolean;
};

const menuConfig: MenuItem[] = [
    {
        label: 'Resumen',
        href: 'dashboard',
        icon: '▦',
        grupo: 'principal',
        always:false 
    },

    {
        modulo: 'Citas',
        label: 'Citas',
        href: 'citas.index',
        icon: '📅',
        grupo: 'principal',
    },
    {
        modulo: 'Tratamientos',
        label: 'Tratamientos',
        href: 'tratamiento.index',
        icon: '♧',
        grupo: 'principal',
    },
    {
        modulo: 'Analisis',
        label: 'Análisis',
        href: 'analisis.index',
        icon: '🔬',
        grupo: 'principal',
    },
    {
        modulo: 'Modulos',
        label: 'Módulos',
        href: 'modulo.index',
        icon: '▤',
        grupo: 'principal',
    },
    {
        modulo: 'Permisos',
        label: 'Permisos',
        href: 'permiso.index',
        icon: '⚡',
        grupo: 'principal',
    },
    {
        modulo: 'Roles',
        label: 'Roles',
        href: 'rol.index',
        icon: '⚔',
        grupo: 'principal',
    },
    {
        modulo: 'Propietarios',
        label: `${authUser.value?.rol?.nombre === 'Propietario' ? 'Mi perfil' : 'Propietarios'}`,
        href: 'administrador.index',
        icon: '👤',
        grupo: 'principal',
    },
    {
        modulo: 'Doctores',
        label: `${authUser.value?.rol?.nombre === 'Doctor' ? 'Mi perfil' : 'Doctores'}`,
        href: 'doctor.index',
        icon: '🦷',
        grupo: 'principal',
    },
    {
        modulo: 'Secretarias',
        label: `${authUser.value?.rol?.nombre === 'Secretaria' ? 'Mi perfil' : 'Secretarias'}`,
        href: 'secretaria.index',
        icon: '👩',
        grupo: 'principal',
    },
    {
        modulo: 'Pacientes',
        label: `${authUser.value?.rol?.nombre === 'Paciente' ? 'Mi perfil' : 'Pacientes'}`,
        href: 'paciente.index',
        icon: '👨',
        grupo: 'principal',
    },
    {
        modulo: 'Servicios',
        label: 'Servicios',
        href: 'servicio.index',
        icon: '⚕',
        grupo: 'principal',
    },
    {
        modulo: 'Especialidades',
        label: 'Especialidades',
        href: 'especialidad.index',
        icon: '🦷',
        grupo: 'principal',
    },
    {
        modulo: 'Turnos',
        label: 'Turnos',
        href: 'turnos.index',
        icon: '⏰',
        grupo: 'principal',
    },
    {
        modulo: 'Historial Clinico',
        label: 'Historiales',
        href: 'historial_clinico.index',
        icon: '▤',
        grupo: 'gestion',
    },
    {
        modulo: 'Diagnosticos',
        label: 'Diagnósticos',
        href: 'diagnostico.index',
        icon: '📋',
        grupo: 'gestion',
    },
    {
        modulo: 'Boleta Pago',
        label: 'Boleta de Pago',
        href: 'boleta_pago.index',
        icon: '💰',
        grupo: 'gestion',
    },
    {
        modulo: 'Reportes',
        label: 'Reportes',
        href: 'reportes.index',
        icon: '📊',
        grupo: 'gestion',
    },
    {
        modulo: 'Bitacora',
        label: 'Bitácora',
        href: 'bitacora.index',
        icon: '📋',
        grupo: 'gestion',
    },
];
const normalizarTexto = (texto: string): string => {
    return texto
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim()
        .toLowerCase();
};

const nombresModulosPermitidos = computed<Set<string>>(() => {
    return new Set(
        modulos.value.map((modulo) => normalizarTexto(modulo.nombre))
    );
});

const puedeVerItem = (item: MenuItem): boolean => {
    if (item.always) {
        return true;
    }

    if (!item.modulo) {
        return false;
    }

    return nombresModulosPermitidos.value.has(normalizarTexto(item.modulo));
};

const menuPrincipalFiltrado = computed<MenuItem[]>(() => {
    return menuConfig.filter((item) => {
        return item.grupo === 'principal' && puedeVerItem(item);
    });
});

const menuGestionFiltrado = computed<MenuItem[]>(() => {
    return menuConfig.filter((item) => {
        return item.grupo === 'gestion' && puedeVerItem(item);
    });
});


const isActive = (href: string) => page.url === href;

const toggleTheme = () => {
    const currentIndex = themes.findIndex((item) => item.value === theme.value);

    const nextTheme =
        themes[currentIndex + 1]?.value ??
        themes[0].value;

    applyTheme(nextTheme);
};

const toggleSettings = () => {
    showSettings.value = !showSettings.value;
};

const closeSettings = () => {
    showSettings.value = false;
};
</script>
<template>
    <div v-if="open" class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden" @click="emit('close')"></div>

    <aside
        class="fixed left-0 top-0 z-50 flex h-screen flex-col border-r bg-[var(--card)] shadow-[10px_0_35px_rgba(0,169,157,0.08)] transition-all duration-300"
        style="border-color: var(--border)" :class="[
            collapsed ? 'lg:w-20' : 'lg:w-64',
            open
                ? 'w-64 translate-x-0'
                : 'w-64 -translate-x-full lg:translate-x-0',
        ]">
        <div class="flex h-[72px] items-center justify-between border-b px-4" style="border-color: var(--border)">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[var(--primary)] text-[length:var(--font-xl)] font-bold text-white shadow-lg shadow-teal-200">
                    🦷
                </div>

                <div v-if="!collapsed" class="overflow-hidden">
                    <h1 class="whitespace-nowrap text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        OdontoCool
                    </h1>

                    <p class="whitespace-nowrap text-[length:var(--font-xs)] text-[var(--primary)]">
                        Panel administrativo
                    </p>
                </div>
            </div>

            <button class="rounded-lg p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] lg:hidden"
                @click="emit('close')">
                ✕
            </button>
        </div>

        <nav class="flex-1 space-y-8 overflow-y-auto px-3 py-5">
            <div v-if="menuPrincipalFiltrado.length > 0">
                <p v-if="!collapsed" class="mb-2 px-3 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                    Principal
                </p>

                <div class="space-y-1">
                    <Link v-for="item in menuPrincipalFiltrado" :key="item.href" :href="route(item.href)"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-[length:var(--font-sm)] font-bold text-[var(--muted)] transition hover:bg-[var(--primary-soft)] hover:text-[var(--primary)]"
                        :class="[
                            isActive(item.href)
                                ? 'bg-[var(--section-soft)] font-black text-[var(--title)]'
                                : '',
                            collapsed ? 'justify-center' : '',
                        ]" @click="emit('close')">
                        <span class="w-5 text-center text-[length:var(--font-lg)]">
                            {{ item.icon }}
                        </span>

                        <span v-if="!collapsed">
                            {{ item.label }}
                        </span>
                    </Link>
                </div>
            </div>

            <div v-if="menuGestionFiltrado.length > 0">
                <p v-if="!collapsed" class="mb-2 px-3 text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                    Gestión
                </p>

                <div class="space-y-1">
                    <Link v-for="item in menuGestionFiltrado" :key="item.href" :href="route(item.href)"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-[length:var(--font-sm)] font-bold text-[var(--muted)] transition hover:bg-[var(--primary-soft)] hover:text-[var(--primary)]"
                        :class="[
                            isActive(item.href)
                                ? 'bg-[var(--section-soft)] font-black text-[var(--title)]'
                                : '',
                            collapsed ? 'justify-center' : '',
                        ]" @click="emit('close')">
                        <span class="w-5 text-center text-[length:var(--font-lg)]">
                            {{ item.icon }}
                        </span>

                        <span v-if="!collapsed">
                            {{ item.label }}
                        </span>
                    </Link>
                </div>
            </div>
        </nav>

        <div class="relative border-t transition-all duration-300" style="border-color: var(--border)"
            :class="collapsed ? 'p-2' : 'p-4'">
            <!-- MODO COLAPSADO -->
            <div v-if="collapsed" class="flex flex-col items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[var(--section-soft)] text-[length:var(--font-xs)] font-black text-[var(--primary)] shadow-sm">
                    <img v-if="fotoPerfil" :src="fotoPerfil" alt="Foto de perfil"
                        class="h-full w-full rounded-full object-cover" />

                    <span v-else>
                        {{ inicialesUsuario || 'US' }}
                    </span>
                </div>

                <button type="button"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border bg-[var(--section-soft)] text-[length:var(--font-lg)] text-[var(--primary)] shadow-sm transition hover:-translate-y-0.5 hover:bg-[var(--primary-soft)]"
                    style="border-color: var(--border)" title="Configuración de apariencia" @click="toggleSettings">
                    ⚙
                </button>
            </div>

            <!-- MODO NORMAL -->
            <div v-else class="flex items-center gap-3">
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[var(--section-soft)] text-[length:var(--font-xs)] font-black text-[var(--primary)] shadow-sm">
                    <img v-if="fotoPerfil" :src="fotoPerfil" alt="Foto de perfil"
                        class="h-full w-full rounded-full object-cover" />

                    <span v-else>
                        {{ inicialesUsuario || 'US' }}
                    </span>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-[length:var(--font-sm)] font-black text-[var(--title)]">
                        {{ nombreUsuario }}
                    </p>

                    <p class="truncate text-[length:var(--font-xs)] text-[var(--muted)]">
                        {{ authUser?.email ?? 'admin@clinica.com' }}
                    </p>
                </div>

                <button type="button"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border bg-[var(--section-soft)] text-[length:var(--font-lg)] text-[var(--primary)] shadow-sm transition hover:-translate-y-0.5 hover:bg-[var(--primary-soft)]"
                    style="border-color: var(--border)" title="Configuración de apariencia" @click="toggleSettings">
                    ⚙
                </button>
            </div>
        </div>
    </aside>

    <Teleport to="body">
        <Transition enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-3 opacity-0 scale-95" enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in" leave-from-class="translate-y-0 opacity-100 scale-100"
            leave-to-class="translate-y-3 opacity-0 scale-95">
            <div v-if="showAutoThemeQuestion"
                class="fixed bottom-6 right-6 z-[10000] w-[calc(100vw-2rem)] max-w-[420px] rounded-3xl border bg-[var(--card)] p-5 shadow-[0_25px_80px_rgba(0,169,157,0.25)]"
                style="border-color: var(--border)">
                <div class="mb-4">
                    <p
                        class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                        Tema automático
                    </p>

                    <h3 class="mt-1 text-[length:var(--font-base)] font-black text-[var(--title)]">
                        ¿Quieres cambiar el tema según la hora?
                    </h3>

                    <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                        Según la hora de tu sistema, ahora se recomienda usar el modo
                        <strong class="text-[var(--title)]">{{ suggestedThemeLabel }}</strong>.
                        Si activas esta opción, de día se usará el tema claro y de noche el tema oscuro.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <button type="button"
                        class="rounded-xl border px-4 py-2 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)]"
                        style="border-color: var(--border)" @click="rejectAutoTheme">
                        No, mantener manual
                    </button>

                    <button type="button"
                        class="rounded-xl bg-[var(--primary)] px-4 py-2 text-[length:var(--font-sm)] font-black text-white transition hover:bg-[var(--primary-hover)]"
                        @click="acceptAutoTheme">
                        Sí, activar
                    </button>
                </div>
            </div>
        </Transition>
        <div v-if="showSettings" class="fixed inset-0 z-[9998]" @click="closeSettings"></div>

        <Transition enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-3 opacity-0 scale-95" enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in" leave-from-class="translate-y-0 opacity-100 scale-100"
            leave-to-class="translate-y-3 opacity-0 scale-95">
            <div v-if="showSettings"
                class="fixed bottom-6 left-1/2 z-[9999] max-h-[85vh] w-[calc(100vw-2rem)] max-w-[520px] -translate-x-1/2 overflow-y-auto rounded-3xl border bg-[var(--card)] shadow-[0_25px_80px_rgba(0,169,157,0.28)]"
                style="border-color: var(--border)" @click.stop>
                <div class="sticky top-0 z-10 flex items-center justify-between border-b bg-[var(--section-soft)] px-4 py-3"
                    style="border-color: var(--border)">
                    <div>
                        <p
                            class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.2em] text-[var(--primary)]">
                            Apariencia
                        </p>

                        <h3 class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                            Configuración visual
                        </h3>
                    </div>

                    <button type="button"
                        class="rounded-lg p-2 text-[var(--muted)] transition hover:bg-[var(--card)] hover:text-red-500"
                        @click="closeSettings">
                        ✕
                    </button>
                </div>

                <div class="space-y-5 p-4">
                    <div>
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <p
                                    class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--muted)]">
                                    Tema
                                </p>

                                <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                    Cambia los colores del sistema.
                                </p>
                            </div>

                            <button type="button"
                                class="rounded-xl bg-[var(--primary)] px-3 py-2 text-[length:var(--font-xs)] font-black text-white transition hover:bg-[var(--primary-hover)]"
                                @click="toggleTheme">
                                ◐
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            <button v-for="item in themes" :key="item.value" type="button"
                                class="rounded-2xl border px-3 py-3 text-center transition hover:-translate-y-0.5"
                                :class="theme === item.value
                                    ? 'bg-[var(--primary)] text-white shadow-md'
                                    : 'bg-[var(--section-soft)] text-[var(--title)] hover:bg-[var(--primary-soft)]'
                                    " style="border-color: var(--border)" @click="applyTheme(item.value)">
                                <span class="block text-[length:var(--font-xl)]">
                                    {{ item.icon }}
                                </span>

                                <span class="mt-1 block text-[length:var(--font-xs)] font-black">
                                    {{ item.label }}
                                </span>
                            </button>
                        </div>
                        <div class="mt-3 rounded-2xl border bg-[var(--section-soft)] p-3"
                            style="border-color: var(--border)">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-[length:var(--font-xs)] font-black text-[var(--title)]">
                                        Tema automático
                                    </p>

                                    <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                        Día: claro · Noche: oscuro
                                    </p>
                                </div>

                                <button type="button"
                                    class="rounded-xl px-3 py-2 text-[length:var(--font-xs)] font-black transition"
                                    :class="autoThemeEnabled
                                        ? 'bg-[var(--primary)] text-white'
                                        : 'bg-[var(--card)] text-[var(--title)]'
                                        " @click="autoThemeEnabled ? disableAutoTheme() : enableAutoTheme()">
                                    {{ autoThemeEnabled ? 'Activado' : 'Desactivado' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-[var(--border)]"></div>

                    <div>
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <p
                                    class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--muted)]">
                                    Tamaño de letra
                                </p>

                                <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                    Ajusta la lectura del panel.
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                    class="flex h-9 w-9 items-center justify-center rounded-xl border bg-[var(--section-soft)] text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--primary-soft)]"
                                    style="border-color: var(--border)" @click="decreaseFontSize">
                                    A-
                                </button>

                                <button type="button"
                                    class="flex h-9 w-9 items-center justify-center rounded-xl border bg-[var(--section-soft)] text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--primary-soft)]"
                                    style="border-color: var(--border)" @click="increaseFontSize">
                                    A+
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button v-for="size in fontSizes" :key="size.value" type="button"
                                class="rounded-2xl border px-3 py-3 text-left transition hover:-translate-y-0.5" :class="fontSize === size.value
                                    ? 'bg-[var(--primary)] text-white shadow-md'
                                    : 'bg-[var(--section-soft)] text-[var(--title)] hover:bg-[var(--primary-soft)]'
                                    " style="border-color: var(--border)" @click="applyFontSize(size.value)">
                                <span class="block text-[length:var(--font-sm)] font-black">
                                    {{ size.preview }}
                                </span>

                                <span class="mt-1 block text-[length:var(--font-xs)] font-bold opacity-80">
                                    {{ size.label }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>