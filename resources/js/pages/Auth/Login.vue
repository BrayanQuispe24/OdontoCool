<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="Iniciar Sesión" />

    <main class="min-h-screen px-6 py-10 text-[var(--text)] transition-colors duration-300"
        style="background: radial-gradient(circle at top left, var(--hero-from) 0%, var(--hero-mid) 35%, var(--hero-to) 100%)">
        <section class="mx-auto flex min-h-[calc(100vh-80px)] max-w-6xl items-center justify-center">
            <div
                class="grid w-full overflow-hidden rounded-[2.5rem] bg-[var(--card)] shadow-[0_35px_100px_rgba(15,118,110,0.25)] transition-colors duration-300 md:grid-cols-[1.08fr_0.92fr]">
                <div class="relative hidden min-h-[650px] overflow-hidden bg-[var(--primary)] p-10 text-white md:block">
                    <img :src="`${publicBase}assets/portada.jpg`" alt="Clínica dental"
                        class="absolute inset-0 h-full w-full object-cover" />

                    <div class="absolute inset-0 bg-gradient-to-br from-[#006f68]/90 via-[#00a99d]/70 to-[#18dccf]/45">
                    </div>

                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <Link href="/" class="inline-flex items-center gap-3">
                            <div class="rounded-2xl bg-white/95 p-3 shadow-lg">
                                <img :src="`${publicBase}assets/logo.png`" alt="OdontoCool" class="h-14 w-14 object-contain" />
                            </div>

                            <div>
                                <p class="text-xl font-black tracking-wide">
                                    OdontoCool
                                </p>
                            </div>
                        </Link>

                        <div>
                            <span
                                class="mb-5 inline-flex rounded-full bg-white/20 px-5 py-2 text-sm font-bold backdrop-blur">
                                Sonrisas saludables, atención confiable
                            </span>

                            <h1 class="max-w-xl text-5xl font-black leading-tight">
                                Bienvenido de nuevo a tu clínica dental
                            </h1>

                            <p class="mt-6 max-w-lg text-lg leading-8 text-white/90">
                                Accede para gestionar citas, servicios, horarios y la atención de tus pacientes desde un
                                solo lugar.
                            </p>

                            <div class="mt-10 grid max-w-md grid-cols-3 gap-4">
                                <div class="rounded-2xl bg-white/20 p-4 text-center backdrop-blur">
                                    <p class="text-2xl font-black">24/7</p>
                                    <p class="text-xs font-bold uppercase tracking-wider text-white/80">
                                        Sistema
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white/20 p-4 text-center backdrop-blur">
                                    <p class="text-2xl font-black">+500</p>
                                    <p class="text-xs font-bold uppercase tracking-wider text-white/80">
                                        Pacientes
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white/20 p-4 text-center backdrop-blur">
                                    <p class="text-2xl font-black">100%</p>
                                    <p class="text-xs font-bold uppercase tracking-wider text-white/80">
                                        Control
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p class="text-sm text-white/75">
                            © {{ new Date().getFullYear() }} OdontoCool. Todos los derechos reservados.
                        </p>
                    </div>
                </div>

                <div
                    class="flex min-h-[650px] items-center justify-center bg-[var(--card)] p-6 transition-colors duration-300 sm:p-10">
                    <div class="w-full max-w-md">
                        <div class="mb-8 text-center md:text-left">
                            <span
                                class="hidden rounded-full bg-[var(--primary-soft)] px-5 py-2 text-sm font-bold text-[var(--primary)] sm:inline-flex">
                                Acceso al sistema
                            </span>

                            <h2 class="mt-5 text-center text-4xl font-black text-[var(--title)] md:text-left">
                                Iniciar sesión
                            </h2>

                            <p class="mt-3 text-sm text-[var(--muted)]">
                                Ingresa tus credenciales para continuar
                            </p>
                        </div>

                        <div v-if="status"
                            class="mb-5 rounded-2xl bg-emerald-50 p-4 text-sm font-bold text-emerald-700">
                            {{ status }}
                        </div>

                        <form class="space-y-5" @submit.prevent="submit">
                            <div>
                                <label for="email" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                    Correo electrónico
                                </label>

                                <div class="relative">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-4 flex items-center font-bold text-[var(--primary)]">
                                        @
                                    </span>

                                    <input id="email" v-model="form.email" type="email" required autofocus
                                        autocomplete="username" placeholder="tu@correo.com"
                                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />
                                </div>

                                <p v-if="form.errors.email" class="mt-2 text-sm font-medium text-red-500">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <div>
                                <label for="password" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                    Contraseña
                                </label>

                                <div class="relative">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                        🔒
                                    </span>

                                    <input id="password" v-model="form.password" type="password" required
                                        autocomplete="current-password" placeholder="••••••••"
                                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)" />
                                </div>

                                <p v-if="form.errors.password" class="mt-2 text-sm font-medium text-red-500">
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <button type="submit" :disabled="form.processing"
                                class="flex w-full items-center justify-center rounded-2xl bg-[var(--primary)] px-8 py-4 font-black text-white shadow-[0_18px_35px_rgba(0,169,157,0.35)] transition hover:-translate-y-1 hover:bg-[var(--primary-hover)] disabled:cursor-not-allowed disabled:opacity-60">
                                {{ form.processing ? 'Iniciando sesión...' : 'Iniciar sesión' }}
                            </button>
                        </form>

                        <div class="mt-8 text-center">
                            <Link :href="route('home')"
                                class="text-sm font-bold text-[var(--primary)] hover:text-[var(--primary-hover)]">
                                Volver al inicio
                            </Link>

                            <Link :href="route('register')"
                                class="ml-4 text-sm font-bold text-[var(--primary)] hover:text-[var(--primary-hover)]">
                                Crear cuenta
                            </Link>

                            <p class="mt-5 text-xs text-[var(--muted)] md:hidden">
                                © {{ new Date().getFullYear() }} OdontoCool. Todos los derechos reservados.
                            </p>
                        </div>
                        <div class="mt-5 text-center">
                            <span class="text-sm font-bold text-[var(--primary)] hover:text-[var(--primary-hover)]">
                                Para recuperar tu contraseña, contacta al administrador del sistema.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>