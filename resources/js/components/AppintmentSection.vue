<script setup>
const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';
const schedules = [
    ['LUNES', '9:00 a 19:00'],
    ['MARTES', '9:00 a 19:00'],
    ['MIÉRCOLES', '9:00 a 19:00'],
    ['JUEVES', '9:00 a 19:00'],
    ['VIERNES', '9:00 a 19:00'],
    ['SÁBADO', '10:00 a 15:00'],
    ['DOMINGO', '10:00 a 15:00'],
];
</script>

<template>
    <section id="cita" class="bg-[var(--bg)] px-6 py-24 text-[length:var(--font-base)] transition-colors duration-300">
        <div
            class="mx-auto grid max-w-7xl overflow-hidden rounded-[2rem] bg-[var(--section-soft)] shadow-2xl shadow-teal-100 md:grid-cols-2">
            <div class="relative min-h-[560px] bg-cover bg-center" :style="{
                backgroundImage: `url(${publicBase}assets/personal.webp)`
            }">
                <div class="absolute inset-0 bg-[#009f93]/75"></div>

                <div class="relative z-10 p-10 text-white">
                    <h2 class="text-[length:var(--font-3xl)] font-black">Agenda una cita</h2>

                    <p class="mt-4 max-w-md text-[length:var(--font-base)] text-white/90">
                        Completa tus datos y nos pondremos en contacto contigo para confirmar tu atención.
                    </p>

                    <form class="mt-8 space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <input class="form-input" type="text" placeholder="Nombre" />
                            <input class="form-input" type="text" placeholder="Apellido" />
                        </div>

                        <input class="form-input" type="text" placeholder="Horario preferido" />
                        <input class="form-input" type="text" placeholder="Servicio" />
                        <textarea class="form-input h-28 resize-none" placeholder="Descripción"></textarea>

                        <button type="submit"
                            class="w-full rounded-full bg-[var(--primary)] px-8 py-4 text-[length:var(--font-base)] font-black text-white shadow-lg transition hover:-translate-y-1">
                            AGENDAR
                        </button>
                    </form>
                </div>
            </div>

            <div id="horarios" class="bg-[var(--section)] p-10 transition-colors duration-300">
                <span class="font-bold uppercase tracking-[0.3em] text-[var(--primary)]">
                    Horarios
                </span>

                <h2 class="mt-4 text-[length:var(--font-3xl)] font-black text-[var(--title)]">
                    Horarios de atención
                </h2>

                <div class="mt-8 space-y-4">
                    <div v-for="item in schedules" :key="item[0]"
                        class="flex items-center justify-between rounded-2xl bg-[var(--section-soft)] px-5 py-4 shadow-sm">
                        <span class="font-black tracking-wide text-[var(--title)]">
                            {{ item[0] }}
                        </span>

                        <span class="font-bold text-[var(--primary)]">
                            {{ item[1] }}
                        </span>
                    </div>
                </div>

                <div
                    class="mt-8 rounded-2xl bg-[var(--primary)] px-6 py-5 text-center text-[length:var(--font-xl)] font-bold text-white">
                    Pacientes sin cita son bienvenidos
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.form-input {
    width: 100%;
    border-radius: 999px;
    background: var(--input-bg);
    padding: 14px 20px;
    color: var(--input-text);
    font-size: var(--font-base);
    outline: none;
}

textarea.form-input {
    border-radius: 24px;
}
</style>