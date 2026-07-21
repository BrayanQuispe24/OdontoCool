<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import type { LandingDoctor, LandingPageProps } from '@/types/landing';

const props = defineProps<LandingPageProps>();
const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';

const resolveImage = (value?: string | null, fallback = `${publicBase}assets/personal.webp`) => {
    if (!value) {
        return fallback;
    }

    if (/^https?:\/\//i.test(value)) {
        return value;
    }

    const normalized = value.replace(/^\/+/, '');

    if (normalized.startsWith('storage/')) {
        return `${publicBase}${normalized}`;
    }

    if (normalized.startsWith('assets/')) {
        return `${publicBase}${normalized}`;
    }

    return `${publicBase}${normalized}`;
};

const serviceIcon = (title: string) => {
    const normalized = title.toLowerCase();

    if (normalized.includes('limpieza')) return '🪥';
    if (normalized.includes('ortodoncia')) return '😁';
    if (normalized.includes('radiografía')) return '🩻';
    if (normalized.includes('blanqueamiento')) return '✨';
    if (normalized.includes('extracción')) return '🦷';
    if (normalized.includes('endodoncia')) return '🧪';
    if (normalized.includes('implante')) return '🔩';

    return '🦷';
};

const analysisIcon = (title: string) => {
    const normalized = title.toLowerCase();

    if (normalized.includes('radiografía') || normalized.includes('rayos')) return '🩻';
    if (normalized.includes('tomografía')) return '🧭';
    if (normalized.includes('fotografía')) return '📷';
    if (normalized.includes('hemograma')) return '🩸';
    if (normalized.includes('glucemia')) return '🧬';

    return '📋';
};

const infoIcon = (title: string) => {
    const normalized = title.toLowerCase();

    if (normalized.includes('horario')) return '⏰';
    if (normalized.includes('contacto')) return '☎';
    if (normalized.includes('ubicación')) return '📍';
    if (normalized.includes('cobertura')) return '🏥';

    return 'ℹ';
};

const doctorHighlight = (doctor: LandingDoctor) => {
    if (doctor.especialidades.length > 0) {
        return doctor.especialidades[0];
    }

    return 'Odontología integral';
};

const doctorSchedule = (doctor: LandingDoctor) => {
    if (doctor.turnos.length > 0) {
        return doctor.turnos.slice(0, 3).join(' · ');
    }

    return 'Horario sujeto a agenda';
};
</script>

<template>
    <Head :title="`${props.clinica.nombre} | OdontoCool`" />

    <main class="min-h-screen bg-[var(--bg)] text-[var(--text)] transition-colors duration-300">
        <Navbar />

        <section
            id="inicio"
            class="scroll-mt-32 pt-36"
            style="background: radial-gradient(circle at top left, var(--hero-from) 0%, var(--hero-mid) 35%, var(--hero-to) 100%)"
        >
            <div class="mx-auto grid max-w-7xl items-center gap-10 px-6 pb-16 lg:grid-cols-[1.05fr_0.95fr] lg:pt-4">
                <div class="space-y-8">
                    <span class="inline-flex rounded-full bg-[var(--card)] px-5 py-2 text-[length:var(--font-sm)] font-bold text-[var(--primary)] shadow">
                        OdontoCool · Clínica dental
                    </span>

                    <div class="space-y-5">
                        <h1 class="max-w-2xl text-[length:var(--font-3xl)] font-black leading-tight text-[var(--title)] md:text-[length:var(--font-4xl)]">
                            Atención dental moderna, segura y respaldada por datos reales
                        </h1>

                        <p class="max-w-2xl text-[length:var(--font-lg)] leading-8 text-[var(--muted)]">
                            {{ props.clinica.descripcion }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 md:grid-cols-3  ">
                        <article class="rounded-[1.75rem] border bg-[var(--card)] p-5 shadow-sm" style="border-color: var(--border)">
                            <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--muted)]">
                                Servicios
                            </p>

                            <h2 class="mt-3 text-[length:var(--font-3xl)] font-black text-[var(--primary)]">
                                {{ props.clinica.resumen.servicios }}
                            </h2>
                        </article>

                        <article class="rounded-[1.75rem] border bg-[var(--card)] p-5 shadow-sm" style="border-color: var(--border)">
                            <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--muted)]">
                                Análisis
                            </p>

                            <h2 class="mt-3 text-[length:var(--font-3xl)] font-black text-[var(--primary)]">
                                {{ props.clinica.resumen.analisis }}
                            </h2>
                        </article>

                        <article class="rounded-[1.75rem] border bg-[var(--card)] p-5 shadow-sm" style="border-color: var(--border)">
                            <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--muted)]">
                                Doctores
                            </p>

                            <h2 class="mt-3 text-[length:var(--font-3xl)] font-black text-[var(--primary)]">
                                {{ props.clinica.resumen.doctores }}
                            </h2>
                        </article>

                    </div>

                    <div class=" grid  text-center grid-cols-2 sm:text-end sm:flex sm:flex-wrap sm:gap-4">
                        <a
                            href="#servicios"
                            class="rounded-full bg-[var(--primary)] px-8 py-4 text-[length:var(--font-base)] font-bold text-white shadow-md transition hover:-translate-y-1 hover:bg-[var(--primary-hover)]"
                        >
                            Ver servicios
                        </a>

                        <a
                            href="#doctores"
                            class="rounded-full border bg-[var(--card)] px-8 py-4 text-[length:var(--font-base)] font-bold text-[var(--primary)] transition hover:-translate-y-1"
                            style="border-color: var(--border)"
                        >
                            Nuestros doctores
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-[var(--primary-soft)] blur-3xl"></div>

                    <div class="relative overflow-hidden rounded-[2.25rem] border bg-[var(--card)] p-4 shadow-[0_30px_80px_rgba(0,0,0,0.16)]" style="border-color: var(--border)">
                        <img
                            :src="`${publicBase}assets/portada.jpg`"
                            alt="OdontoCool"
                            class="h-[460px] w-full rounded-[1.75rem] object-cover object-center"
                        />

                        <div class="absolute bottom-6 left-6 right-6 rounded-[1.5rem] border bg-white/90 p-5 backdrop-blur-md" style="border-color: var(--border)">
                            <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.2em] text-[var(--primary)]">
                                Búsqueda pública
                            </p>

                            <p class="mt-2 text-[length:var(--font-base)] font-bold text-slate-900">
                                Encuentra servicios, doctores, análisis y datos de la clínica desde el encabezado.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="servicios" class="scroll-mt-32 bg-[var(--bg)] px-6 py-24">
            <div class="mx-auto max-w-7xl">
                <div class="mb-14 text-center">
                    <p class="font-bold uppercase tracking-[0.3em] text-[var(--primary)]">
                        Servicios disponibles
                    </p>

                    <h2 class="mt-4 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                        Tratamientos odontológicos que ofrecemos
                    </h2>

                    <p class="mx-auto mt-4 max-w-3xl text-[length:var(--font-base)] leading-7 text-[var(--muted)]">
                        Servicios reales cargados desde la base de datos para que el paciente consulte la oferta clínica sin información quemada.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="service in props.servicios"
                        :id="service.anchor"
                        :key="service.id"
                        class="group rounded-[2rem] border bg-[var(--card)] p-8 shadow-[0_20px_50px_rgba(0,169,157,0.10)] transition hover:-translate-y-2 hover:shadow-[0_30px_70px_rgba(0,169,157,0.18)]"
                        style="border-color: var(--border)"
                    >
                        <div class="mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-[length:var(--font-3xl)]">
                            {{ serviceIcon(service.title) }}
                        </div>

                        <span class="inline-flex rounded-full bg-[var(--section-soft)] px-3 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-[0.16em] text-[var(--primary)]">
                            {{ service.badge }}
                        </span>

                        <h3 class="mt-4 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                            {{ service.title }}
                        </h3>

                        <p class="mt-4 text-[length:var(--font-base)] leading-7 text-[var(--muted)]">
                            {{ service.description }}
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section id="analisis" class="scroll-mt-32 bg-[var(--section-soft)] px-6 py-24 transition-colors duration-300">
            <div class="mx-auto max-w-7xl">
                <div class="mb-14 text-center">
                    <p class="font-bold uppercase tracking-[0.3em] text-[var(--primary)]">
                        Análisis disponibles
                    </p>

                    <h2 class="mt-4 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                        Diagnóstico y estudios complementarios
                    </h2>

                    <p class="mx-auto mt-4 max-w-3xl text-[length:var(--font-base)] leading-7 text-[var(--muted)]">
                        Radiografías y análisis reales cargados desde el backend para una consulta pública rápida y confiable.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <article
                        v-for="item in props.analisis"
                        :id="item.anchor"
                        :key="item.id"
                        class="rounded-[2rem] border bg-[var(--card)] p-6 shadow-md transition hover:-translate-y-1"
                        style="border-color: var(--border)"
                    >
                        <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-[length:var(--font-2xl)]">
                            {{ analysisIcon(item.title) }}
                        </div>

                        <span class="inline-flex rounded-full bg-[var(--section-soft)] px-3 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-[0.16em] text-[var(--primary)]">
                            {{ item.badge }}
                        </span>

                        <h3 class="mt-4 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                            {{ item.title }}
                        </h3>

                        <p class="mt-3 text-[length:var(--font-sm)] leading-7 text-[var(--muted)]">
                            {{ item.description }}
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section id="doctores" class="scroll-mt-32 bg-[var(--bg)] px-6 py-24">
            <div class="mx-auto max-w-7xl">
                <div class="mb-14 text-center">
                    <p class="font-bold uppercase tracking-[0.3em] text-[var(--primary)]">
                        Nuestros doctores
                    </p>

                    <h2 class="mt-4 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                        Equipo profesional de la clínica
                    </h2>

                    <p class="mx-auto mt-4 max-w-3xl text-[length:var(--font-base)] leading-7 text-[var(--muted)]">
                        Perfil resumido de cada doctor con foto, contacto y especialidades preparadas desde la base de datos.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="doctor in props.doctores"
                        :id="doctor.anchor"
                        :key="doctor.codigo_doctor"
                        class="overflow-hidden rounded-[2rem] border bg-[var(--card)] shadow-[0_18px_45px_rgba(0,169,157,0.12)] transition hover:-translate-y-1"
                        style="border-color: var(--border)"
                    >
                        <img
                            :src="resolveImage(doctor.photo)"
                            :alt="doctor.title"
                            class="h-72 w-full object-cover object-center"
                        />

                        <div class="space-y-4 p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <span class="inline-flex rounded-full bg-[var(--primary-soft)] px-3 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-[0.16em] text-[var(--primary)]">
                                        {{ doctorHighlight(doctor) }}
                                    </span>

                                    <h3 class="mt-3 text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                                        {{ doctor.title }}
                                    </h3>
                                </div>
                            </div>

                            <p class="text-[length:var(--font-sm)] leading-7 text-[var(--muted)]">
                                {{ doctor.description }}
                            </p>

                            <div class="space-y-2 text-[length:var(--font-xs)] text-[var(--muted)]">
                                <p v-if="doctor.telefono">
                                    <strong class="text-[var(--title)]">Teléfono:</strong>
                                    {{ doctor.telefono }}
                                </p>

                                <p v-if="doctor.correo">
                                    <strong class="text-[var(--title)]">Correo:</strong>
                                    {{ doctor.correo }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="especialidad in doctor.especialidades"
                                    :key="especialidad"
                                    class="rounded-full bg-[var(--section-soft)] px-3 py-1 text-[length:var(--font-xs)] font-bold text-[var(--title)]"
                                >
                                    {{ especialidad }}
                                </span>
                            </div>

                            <div class="rounded-2xl bg-[var(--section-soft)] p-4 text-[length:var(--font-xs)] text-[var(--muted)]">
                                <p class="font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                                    Información adicional
                                </p>

                                <ul class="mt-2 space-y-1">
                                    <li v-for="item in doctor.informacion_adicional" :key="item">
                                        • {{ item }}
                                    </li>
                                </ul>

                                <p class="mt-3 font-bold text-[var(--title)]">
                                    Horarios: {{ doctorSchedule(doctor) }}
                                </p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="clinica" class="scroll-mt-32 bg-[var(--section-soft)] px-6 py-24 transition-colors duration-300">
            <div class="mx-auto max-w-7xl">
                <div class="mb-14 text-center">
                    <p class="font-bold uppercase tracking-[0.3em] text-[var(--primary)]">
                        Información de la clínica
                    </p>

                    <h2 class="mt-4 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                        Datos públicos consultables
                    </h2>

                    <p class="mx-auto mt-4 max-w-3xl text-[length:var(--font-base)] leading-7 text-[var(--muted)]">
                        Toda la información visible aquí proviene del backend y también puede recuperarse mediante el buscador público.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <article
                        v-for="card in props.clinica.cards"
                        :id="card.anchor"
                        :key="card.anchor"
                        class="rounded-[2rem] border bg-[var(--card)] p-6 shadow-md transition hover:-translate-y-1"
                        style="border-color: var(--border)"
                    >
                        <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-[length:var(--font-2xl)]">
                            {{ infoIcon(card.title) }}
                        </div>

                        <span class="inline-flex rounded-full bg-[var(--section-soft)] px-3 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-[0.16em] text-[var(--primary)]">
                            {{ card.badge }}
                        </span>

                        <h3 class="mt-4 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                            {{ card.title }}
                        </h3>

                        <p class="mt-3 text-[length:var(--font-sm)] leading-7 text-[var(--muted)]">
                            {{ card.description }}
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section class="px-6 py-20">
            <div class="mx-auto max-w-7xl rounded-[2.25rem] bg-[var(--navbar)] px-8 py-10 text-center shadow-[0_24px_70px_rgba(0,0,0,0.12)]">
                <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.32em] text-[var(--primary)]">
                    OdontoCool
                </p>

                <h2 class="mt-4 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                    {{ props.clinica.nombre }} está lista para atenderte
                </h2>

                <p class="mx-auto mt-4 max-w-3xl text-[length:var(--font-base)] leading-7 text-[var(--muted)]">
                    Usa el buscador del encabezado para encontrar servicios, análisis, doctores o información pública sobre la clínica.
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <a
                        href="#inicio"
                        class="rounded-full bg-[var(--primary)] px-8 py-4 text-[length:var(--font-base)] font-bold text-white transition hover:-translate-y-1 hover:bg-[var(--primary-hover)]"
                    >
                        Volver arriba
                    </a>

                    <a
                        href="#clinica"
                        class="rounded-full border bg-[var(--card)] px-8 py-4 text-[length:var(--font-base)] font-bold text-[var(--primary)] transition hover:-translate-y-1"
                        style="border-color: var(--border)"
                    >
                        Ver información pública
                    </a>
                </div>
            </div>
        </section>
    </main>
</template>
