<script setup lang="ts">
const services = [
    {
        title: 'Limpieza dental',
        text: 'Eliminación de placa y sarro para mantener una sonrisa saludable.',
        icon: '🦷',
    },
    {
        title: 'Blanqueamiento',
        text: 'Tratamiento estético para mejorar el color de tus dientes.',
        icon: '✨',
    },
    {
        title: 'Ortodoncia',
        text: 'Corrección de la posición dental para mejorar estética y mordida.',
        icon: '😁',
    },
    {
        title: 'Extracciones',
        text: 'Procedimientos seguros cuando una pieza dental necesita retirarse.',
        icon: '🩺',
    },
    {
        title: 'Curaciones',
        text: 'Restauración de dientes afectados por caries o desgaste.',
        icon: '🔧',
    },
    {
        title: 'Implantes dentales',
        text: 'Reposición de piezas dentales perdidas con una solución fija.',
        icon: '🦷',
    },
];
</script>

<template>
    <section id="servicios" class="bg-[var(--bg)] px-6 py-24 text-[length:var(--font-base)] transition-colors duration-300">
        <div class="mx-auto max-w-7xl">
            <div class="mb-14 text-center">
                <span class="font-bold uppercase tracking-[0.3em] text-[var(--primary)]">
                    Servicios
                </span>

                <h2 class="mt-4 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                    Tratamientos que ofrecemos
                </h2>

                <p class="mx-auto mt-4 max-w-2xl text-[length:var(--font-base)] text-[var(--muted)]">
                    Atención dental completa para cuidar, mejorar y mantener tu salud bucal.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <article
                    v-for="service in services"
                    :key="service.title"
                    class="group rounded-[2rem] border bg-[var(--card)] p-8 shadow-[0_20px_50px_rgba(0,169,157,0.10)] transition hover:-translate-y-2 hover:shadow-[0_30px_70px_rgba(0,169,157,0.18)]"
                    style="border-color: var(--border)"
                >
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--primary-soft)] text-[length:var(--font-3xl)]">
                        {{ service.icon }}
                    </div>

                    <h3 class="text-[length:var(--font-2xl)] font-black text-[var(--title)]">
                        {{ service.title }}
                    </h3>

                    <p class="mt-4 text-[length:var(--font-base)] leading-7 text-[var(--muted)]">
                        {{ service.text }}
                    </p>

                    <a
                        href="#cita"
                        class="mt-6 inline-block text-[length:var(--font-base)] font-bold text-[var(--primary)] transition group-hover:translate-x-1"
                    >
                        Agendar servicio →
                    </a>
                </article>
            </div>
        </div>
    </section>
</template>