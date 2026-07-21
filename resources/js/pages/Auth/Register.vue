<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const publicBase = import.meta.env.VITE_PUBLIC_BASE || '/';

const form = useForm({
    email: '',
    url: '',
    password: '',
    password_confirmation: '',
    carnet_identidad: '',
    nombre: '',
    apellido: '',
    fecha_nacimiento: '',
    direccion: '',
    genero: '',
    telefono: '',
    contacto_emergencia: '',
    telefono_emergencia: '',
    rol_id: 2, // el que corresponda a paciente
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Registro" />

    <main
        class="min-h-screen px-6 py-10 text-[var(--text)] transition-colors duration-300"
        style="background: radial-gradient(circle at top left, var(--hero-from) 0%, var(--hero-mid) 35%, var(--hero-to) 100%)"
    >
        <section class="mx-auto flex min-h-[calc(100vh-80px)] max-w-6xl items-center justify-center">
            <div
                class="grid w-full overflow-hidden rounded-[2.5rem] bg-[var(--card)] shadow-[0_35px_100px_rgba(15,118,110,0.25)] transition-colors duration-300 md:grid-cols-[1.08fr_0.92fr]"
            >
                <div class="relative hidden min-h-[720px] overflow-hidden bg-[var(--primary)] p-10 text-white md:block">
                    <img
                        :src="`${publicBase}assets/portada.jpg`"
                        alt="Clínica dental"
                        class="absolute inset-0 h-full w-full object-cover"
                    />

                    <div class="absolute inset-0 bg-gradient-to-br from-[#006f68]/90 via-[#00a99d]/70 to-[#18dccf]/45"></div>

                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <Link :href="route('home')" class="inline-flex items-center gap-3">
                            <div class="rounded-2xl bg-white/95 p-3 shadow-lg">
                                <img
                                    :src="`${publicBase}assets/logo.png`"
                                    alt="OdontoCool"
                                    class="h-14 w-14 object-contain"
                                />
                            </div>

                            <div>
                                <p class="text-xl font-black tracking-wide">
                                    OdontoCool
                                </p>
                            </div>
                        </Link>

                        <div>
                            <span class="mb-5 inline-flex rounded-full bg-white/20 px-5 py-2 text-sm font-bold backdrop-blur">
                                Crea tu cuenta en segundos
                            </span>

                            <h1 class="max-w-xl text-5xl font-black leading-tight">
                                Únete a OdontoCool y agenda tu atención dental
                            </h1>

                            <p class="mt-6 max-w-lg text-lg leading-8 text-white/90">
                                Regístrate para acceder al sistema, gestionar tus citas y recibir una atención más
                                organizada.
                            </p>

                            <div class="mt-10 grid max-w-md grid-cols-3 gap-4">
                                <div class="rounded-2xl bg-white/20 p-4 text-center backdrop-blur">
                                    <p class="text-2xl font-black">Fácil</p>
                                    <p class="text-xs font-bold uppercase tracking-wider text-white/80">
                                        Registro
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white/20 p-4 text-center backdrop-blur">
                                    <p class="text-2xl font-black">Seguro</p>
                                    <p class="text-xs font-bold uppercase tracking-wider text-white/80">
                                        Acceso
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white/20 p-4 text-center backdrop-blur">
                                    <p class="text-2xl font-black">Rápido</p>
                                    <p class="text-xs font-bold uppercase tracking-wider text-white/80">
                                        Citas
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p class="text-sm text-white/75">
                            © {{ new Date().getFullYear() }} OdontoCool. Todos los derechos reservados.
                        </p>
                    </div>
                </div>

                <div class="flex min-h-[720px] items-center justify-center bg-[var(--card)] p-6 transition-colors duration-300 sm:min-w-[620px] sm:p-10">
                    <div class="w-full max-w-2xl">
                        <div class="mb-8 text-center md:text-left">
                            <Link :href="route('home')" class="mb-6 inline-flex items-center gap-3 md:hidden">
                                <img
                                    :src="`${publicBase}assets/logo.png`"
                                    alt="OdontoCool"
                                    class="hidden h-16 w-16 object-contain sm:inline-flex"
                                />

                                <div class="hidden sm:inline-flex">
                                    <p class="text-xl font-black text-[var(--text)]">
                                        Odonto
                                    </p>

                                    <p class="text-sm font-bold tracking-widest text-[var(--primary)]">
                                        Cool
                                    </p>
                                </div>
                            </Link>

                            <span class="hidden rounded-full bg-[var(--primary-soft)] px-5 py-2 text-sm font-bold text-[var(--primary)] sm:inline-flex">
                                Registrarse
                            </span>

                            <h2 class="mt-5 text-center text-4xl font-black text-[var(--title)] md:text-left">
                                Registrarse
                            </h2>

                            <p class="mt-3 text-sm text-[var(--muted)]">
                                Completa tus datos para crear tu cuenta
                            </p>
                        </div>

                        <form class="w-full space-y-5" @submit.prevent="submit">
                            <div class="grid w-full grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-4">
                                <div>
                                    <label for="nombre" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        Nombre
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            👤
                                        </span>

                                        <input
                                            id="nombre"
                                            v-model="form.nombre"
                                            type="text"
                                            required
                                            autofocus
                                            autocomplete="name"
                                            placeholder="Tu nombre"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.nombre" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.nombre }}
                                    </p>
                                </div>

                                <div>
                                    <label for="apellido" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        Apellido
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            👤
                                        </span>

                                        <input
                                            id="apellido"
                                            v-model="form.apellido"
                                            type="text"
                                            required
                                            autofocus
                                            autocomplete="name"
                                            placeholder="Tu apellido"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.apellido" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.apellido }}
                                    </p>
                                </div>

                                <div>
                                    <label for="carnet_identidad" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        Carnet de Identidad
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            👤
                                        </span>

                                        <input
                                            id="carnet_identidad"
                                            v-model="form.carnet_identidad"
                                            type="text"
                                            required
                                            autofocus
                                            autocomplete="name"
                                            placeholder="Tu carnet de identidad"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.carnet_identidad" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.carnet_identidad }}
                                    </p>
                                </div>

                                <div>
                                    <label for="fecha_nacimiento" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        fecha de nacimiento
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            👤
                                        </span>

                                        <input
                                            id="fecha_nacimiento"
                                            v-model="form.fecha_nacimiento"
                                            type="date"
                                            required
                                            autofocus
                                            autocomplete="name"
                                            placeholder="Tu nombre"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.fecha_nacimiento" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.fecha_nacimiento }}
                                    </p>
                                </div>

                                <div>
                                    <label for="direccion" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        Dirección
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            👤
                                        </span>

                                        <input
                                            id="direccion"
                                            v-model="form.direccion"
                                            type="text"
                                            required
                                            autofocus
                                            autocomplete="address"
                                            placeholder="Tu dirección"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.direccion" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.direccion }}
                                    </p>
                                </div>

                                <div>
                                    <label for="genero" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        Género
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            👤
                                        </span>

                                        <input
                                            id="genero"
                                            v-model="form.genero"
                                            type="text"
                                            required
                                            autofocus
                                            autocomplete="gender"
                                            placeholder="Tu género"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.genero" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.genero }}
                                    </p>
                                </div>

                                <div>
                                    <label for="telefono" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        Telefono
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            👤
                                        </span>

                                        <input
                                            id="telefono"
                                            v-model="form.telefono"
                                            type="text"
                                            required
                                            autofocus
                                            autocomplete="name"
                                            placeholder="Tu telefono"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.telefono" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.telefono }}
                                    </p>
                                </div>

                                <div>
                                    <label for="contacto_emergencia" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        Contacto de emergencia
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            👤
                                        </span>

                                        <input
                                            id="contacto_emergencia"
                                            v-model="form.contacto_emergencia"
                                            type="text"
                                            required
                                            autofocus
                                            autocomplete="name"
                                            placeholder="Nombre del contacto"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.contacto_emergencia" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.contacto_emergencia }}
                                    </p>
                                </div>

                                <div>
                                    <label for="telefono_emergencia" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        Teléfono de emergencia
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            👤
                                        </span>

                                        <input
                                            id="telefono_emergencia"
                                            v-model="form.telefono_emergencia"
                                            type="text"
                                            required
                                            autofocus
                                            autocomplete="name"
                                            placeholder="Teléfono del contacto"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.telefono_emergencia" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.telefono_emergencia }}
                                    </p>
                                </div>

                                <div>
                                    <label for="email" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        Correo electrónico
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center font-bold text-[var(--primary)]">
                                            @
                                        </span>

                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            required
                                            autocomplete="username"
                                            placeholder="tu@correo.com"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
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
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            🔒
                                        </span>

                                        <input
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            required
                                            autocomplete="new-password"
                                            placeholder="••••••••"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.password" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.password }}
                                    </p>
                                </div>

                                <div>
                                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-[var(--title)]">
                                        Confirmar contraseña
                                    </label>

                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                                            ✅
                                        </span>

                                        <input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Repite tu contraseña"
                                            class="w-full rounded-2xl border bg-[var(--section-soft)] px-12 py-4 text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                                            style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                                        />
                                    </div>

                                    <p v-if="form.errors.password_confirmation" class="mt-2 text-sm font-medium text-red-500">
                                        {{ form.errors.password_confirmation }}
                                    </p>
                                </div>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex w-full items-center justify-center rounded-2xl bg-[var(--primary)] px-8 py-4 font-black text-white shadow-[0_18px_35px_rgba(0,169,157,0.35)] transition hover:-translate-y-1 hover:bg-[var(--primary-hover)] disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {{ form.processing ? 'Registrando...' : 'Registrarse' }}
                            </button>
                        </form>

                        <div class="mt-8 text-center">
                            <Link
                                :href="route('login')"
                                class="text-sm font-bold text-[var(--primary)] hover:text-[var(--primary-hover)]"
                            >
                                ¿Ya tienes cuenta? Inicia sesión
                            </Link>

                            <div class="mt-4">
                                <Link
                                    :href="route('home')"
                                    class="text-sm font-bold text-[var(--muted)] hover:text-[var(--title)]"
                                >
                                    Volver al inicio
                                </Link>
                            </div>

                            <p class="mt-5 text-xs text-[var(--muted)] md:hidden">
                                © {{ new Date().getFullYear() }} OdontoCool. Todos los derechos reservados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>