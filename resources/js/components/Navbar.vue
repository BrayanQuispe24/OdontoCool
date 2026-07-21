<script setup lang="ts">
import { ref } from 'vue';
import { useTheme, type Theme } from '@/composable/useTheme';
import { useFontSize, type FontSize } from '@/composable/useFontSize';
import { Link } from '@inertiajs/vue3';
import PublicSearchBar from '@/components/PublicSearchBar.vue';

const { theme, applyTheme } = useTheme();
const { fontSize, applyFontSize, increaseFontSize, decreaseFontSize } = useFontSize();

const showSettings = ref(false);
const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';


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

const toggleSettings = () => {
    showSettings.value = !showSettings.value;
};

const closeSettings = () => {
    showSettings.value = false;
};

const toggleTheme = () => {
    if (theme.value === 'light') {
        applyTheme('dark');
        return;
    }

    if (theme.value === 'dark') {
        applyTheme('odontocool');
        return;
    }

    applyTheme('light');
};
</script>

<template>
    <header
        class="fixed left-0 top-0 z-50 w-full border-b bg-[var(--navbar)] shadow-[0_6px_30px_rgba(0,169,157,0.10)] backdrop-blur-xl transition-colors duration-300"
        style="border-color: var(--border)"
    >
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-3">
            <div class="flex items-center justify-between gap-4">
                <a href="#inicio" class="flex items-center gap-3">
                    <img
                        :src="`${publicBase}assets/logo.png`"
                        alt="OdontoCool"
                        class="h-14 w-14 object-contain md:h-16 md:w-16"
                    />

                    <div class="leading-tight">
                        <p class="text-[length:var(--font-lg)] font-black text-[var(--text)]">
                            OdontoCool
                        </p>

                        <p class="hidden sm:block text-[length:var(--font-xs)] font-semibold uppercase tracking-[0.22em] text-[var(--muted)]">
                            Sonrisa saludable, atención real
                        </p>
                    </div>
                </a>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="flex h-11 w-11 items-center justify-center rounded-full border bg-[var(--card)] text-[length:var(--font-lg)] text-[var(--primary)] shadow-md transition hover:-translate-y-0.5 hover:bg-[var(--primary-soft)]"
                        style="border-color: var(--border)"
                        title="Configuración de apariencia"
                        @click="toggleSettings"
                    >
                        ⚙
                    </button>

                    <Link
                        :href="route('login')" 
                        class=" hidden sm:block rounded-full bg-[var(--primary)] px-4 py-2 text-[length:var(--font-xs)] font-bold text-[var(--card)] shadow-md transition hover:-translate-y-1 md:px-6 md:py-3 md:text-[length:var(--font-sm)]"
                    >
                        Agendar cita
                    </Link>

                    <Link
                        :href="route('login')"
                        class=" text-center rounded-full bg-[var(--text)] px-4 py-2 text-[length:var(--font-xs)] font-semibold text-[var(--card)] shadow-md transition hover:-translate-y-1 md:px-6 md:py-3 md:text-[length:var(--font-sm)]"
                    >
                        Iniciar sesión
                    </Link>
                </div>
            </div>

            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:gap-6">
                <nav class="hidden items-center gap-8 lg:flex">
                    <a href="#inicio" class="nav-link">INICIO</a>
                    <a href="#servicios" class="nav-link">SERVICIOS</a>
                    <a href="#analisis" class="nav-link">ANÁLISIS</a>
                    <a href="#doctores" class="nav-link">DOCTORES</a>
                    <a href="#clinica" class="nav-link">CLÍNICA</a>
                </nav>

                <div class="w-full lg:flex-1">
                    <PublicSearchBar />
                </div>
            </div>
        </div>
    </header>

    <Teleport to="body">
        <div
            v-if="showSettings"
            class="fixed inset-0 z-[9998] bg-slate-900/20 backdrop-blur-[2px]"
            @click="closeSettings"
        ></div>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-3 opacity-0 scale-95"
            enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100 scale-100"
            leave-to-class="translate-y-3 opacity-0 scale-95"
        >
            <div
                v-if="showSettings"
                class="fixed left-1/2 top-24 z-[9999] max-h-[85vh] w-[calc(100vw-2rem)] max-w-[520px] -translate-x-1/2 overflow-y-auto rounded-3xl border bg-[var(--card)] shadow-[0_25px_80px_rgba(0,169,157,0.28)]"
                style="border-color: var(--border)"
                @click.stop
            >
                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b bg-[var(--section-soft)] px-5 py-4"
                    style="border-color: var(--border)"
                >
                    <div>
                        <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.25em] text-[var(--primary)]">
                            Apariencia
                        </p>

                        <h3 class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                            Configuración visual
                        </h3>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--card)] hover:text-red-500"
                        @click="closeSettings"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-6 p-5">
                    <div>
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--muted)]">
                                    Tema
                                </p>

                                <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                    Cambia los colores de la página.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="rounded-xl bg-[var(--primary)] px-4 py-2 text-[length:var(--font-xs)] font-black text-white shadow-md transition hover:bg-[var(--primary-hover)]"
                                @click="toggleTheme"
                            >
                                ◐
                            </button>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <button
                                v-for="item in themes"
                                :key="item.value"
                                type="button"
                                class="rounded-2xl border px-3 py-4 text-center transition hover:-translate-y-0.5"
                                :class="theme === item.value
                                    ? 'bg-[var(--primary)] text-white shadow-md'
                                    : 'bg-[var(--section-soft)] text-[var(--title)] hover:bg-[var(--primary-soft)]'"
                                style="border-color: var(--border)"
                                @click="applyTheme(item.value)"
                            >
                                <span class="block text-[length:var(--font-xl)]">
                                    {{ item.icon }}
                                </span>

                                <span class="mt-1 block text-[length:var(--font-xs)] font-black">
                                    {{ item.label }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="h-px bg-[var(--border)]"></div>

                    <div>
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--muted)]">
                                    Tamaño de letra
                                </p>

                                <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                    Ajusta la lectura de la página.
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="flex h-9 w-9 items-center justify-center rounded-xl border bg-[var(--section-soft)] text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--primary-soft)]"
                                    style="border-color: var(--border)"
                                    @click="decreaseFontSize"
                                >
                                    A-
                                </button>

                                <button
                                    type="button"
                                    class="flex h-9 w-9 items-center justify-center rounded-xl border bg-[var(--section-soft)] text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--primary-soft)]"
                                    style="border-color: var(--border)"
                                    @click="increaseFontSize"
                                >
                                    A+
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button
                                v-for="size in fontSizes"
                                :key="size.value"
                                type="button"
                                class="rounded-2xl border px-4 py-4 text-left transition hover:-translate-y-0.5"
                                :class="fontSize === size.value
                                    ? 'bg-[var(--primary)] text-white shadow-md'
                                    : 'bg-[var(--section-soft)] text-[var(--title)] hover:bg-[var(--primary-soft)]'"
                                style="border-color: var(--border)"
                                @click="applyFontSize(size.value)"
                            >
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

<style scoped>
.nav-link {
    position: relative;
    color: var(--primary);
    font-size: var(--font-sm);
    letter-spacing: 1.5px;
    font-weight: 700;
    transition: color 0.2s ease;
}

.nav-link::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -8px;
    width: 0;
    height: 2px;
    background: var(--primary);
    transition: width 0.2s ease;
}

.nav-link:hover::after {
    width: 100%;
}
</style>