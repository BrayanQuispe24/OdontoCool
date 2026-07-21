<script setup lang="ts">
const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';
const cards = [
    {
        title: 'Nuestro personal',
        image: `${publicBase}assets/personal.webp`,
        text: 'Contamos con profesionales capacitados para brindarte una atención cercana, segura y de calidad.',
    },
    {
        title: 'Nuestra ubicación',
        image: `${publicBase}assets/nuestraUbicacion.webp`,
        text: 'Nos encontramos en una zona accesible para que puedas llegar fácilmente a tu consulta dental.',
    },
    {
        title: 'Nuestra misión',
        image: `${publicBase}assets/NuestaMision.jpeg`,
        text: 'Buscamos cuidar la salud bucal de cada paciente con compromiso, tecnología y confianza.',
    },
];
</script>

<template>
    <section id="nosotros" class="bg-[var(--section)] px-6 py-24 transition-colors duration-300">
        <div class="mx-auto max-w-7xl">
            <div class="mb-14 text-center">
                <span class="font-bold uppercase tracking-[0.3em] text-[var(--primary)]">
                    Sobre nosotros
                </span>

                <h2 class="mt-4 text-4xl font-black text-[var(--title)]">
                    Una clínica pensada para tu comodidad
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <article
                    v-for="card in cards"
                    :key="card.title"
                    class="group overflow-hidden rounded-[2rem] bg-[var(--section-soft)] p-5 shadow-md shadow-teal-100/60 transition hover:-translate-y-2 hover:shadow-2xl"
                >
                    <img
                        :src="card.image"
                        :alt="card.title"
                        class="h-56 w-full rounded-[1.5rem] object-cover transition duration-300 group-hover:scale-[1.03]"
                    />

                    <div class="p-4">
                        <h3 class="mt-2 text-2xl font-black text-[var(--title)]">
                            {{ card.title }}
                        </h3>

                        <p class="mt-4 text-base leading-7 text-[var(--muted)]">
                            {{ card.text }}
                        </p>

                        <button class="mt-6 rounded-full bg-[var(--primary)] px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-1">
                            + Más información
                        </button>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>