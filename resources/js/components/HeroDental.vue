<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';
</script>

<template>
    <section id="inicio"
        class="relative overflow-hidden pt-28 text-[length:var(--font-base)] transition-colors duration-300"
        style="background: radial-gradient(circle at top left, var(--hero-from) 0%, var(--hero-mid) 35%, var(--hero-to) 100%)">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-6 py-12 md:grid-cols-[1fr_1.2fr]">
            <div class="z-10">
                <span
                    class="mb-4 inline-flex rounded-full bg-[var(--card)] px-5 py-2 text-[length:var(--font-sm)] font-bold text-[var(--primary)] shadow">
                    Cuidamos tu sonrisa
                </span>

                <h1
                    class="max-w-xl text-[length:var(--font-3xl)] font-black leading-tight text-[var(--text)] md:text-[length:var(--font-3xl)]">
                    Atención dental moderna, cómoda y segura
                </h1>

                <p class="mt-6 max-w-lg text-[length:var(--font-lg)] leading-8 text-[var(--muted)]">
                    Agenda tu consulta y recibe atención profesional para cuidar tu salud bucal con confianza.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <Link :href="route('login')"
                        class="rounded-full border bg-[var(--card)] px-8 py-4 text-[length:var(--font-base)] font-bold text-[var(--primary)] transition hover:-translate-y-1">
                    Iniciar sesión
                    </Link>

                    <a href="#servicios"
                        class="rounded-full border bg-[var(--card)] px-8 py-4 text-[length:var(--font-base)] font-bold text-[var(--primary)] transition hover:-translate-y-1"
                        style="border-color: var(--border)">
                        Ver servicios
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -right-10 -top-8 h-64 w-64 rounded-full bg-[var(--primary-soft)] blur-3xl"></div>

                <img :src="`${publicBase}assets/portada.jpg`" alt="Clínica dental"
                    class="relative h-[470px] w-full rounded-[2rem] object-cover object-center shadow-2xl" />
            </div>
        </div>

        <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-4">
            <a href="#" class="social-btn bg-[#25D366]">☎</a>
            <a href="#" class="social-btn bg-[#1877F2]">f</a>
        </div>
    </section>
</template>

<style scoped>
.social-btn {
    display: flex;
    height: 54px;
    width: 54px;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    color: white;
    font-size: var(--font-3xl);
    font-weight: 900;
    box-shadow: 0 12px 30px rgb(15 118 110 / 0.25);
    transition: transform 0.2s ease;
}

.social-btn:hover {
    transform: translateY(-4px);
}
</style>