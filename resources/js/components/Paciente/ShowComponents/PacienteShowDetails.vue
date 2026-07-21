<script setup lang="ts">
import type { PacienteShow } from '@/types/Paciente';

const props = defineProps<{
    form: any;
    passwordForm: any;
    editando: boolean;
    paciente: PacienteShow;
    usuario: any;
    iniciales: string;
    previewFoto: string | null;
    tieneRol: (rol: string) => boolean;
    assetUrl: (path: string | null | undefined) => string | undefined;
}>();

const form = props.form;
const passwordForm = props.passwordForm;

const emit = defineEmits<{
    'change-photo': [event: Event];
    'submit-password': [];
}>();
</script>

<template>
    <div class="grid gap-5 md:grid-cols-2">
        <!-- DATOS PERSONALES -->
        <div class="rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300 border-[var(--border)]">
            <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                Datos personales
            </p>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Carnet
                    </label>

                    <input v-model="form.carnet_identidad" type="text" :disabled="!editando"
                        class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition disabled:opacity-80 border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]" />

                    <p v-if="form.errors.carnet_identidad"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.carnet_identidad }}
                    </p>
                </div>

                <div>
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Teléfono
                    </label>

                    <input v-model="form.telefono" type="text" :disabled="!editando"
                        class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition disabled:opacity-80 border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]" />

                    <p v-if="form.errors.telefono"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.telefono }}
                    </p>
                </div>

                <div>
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Nombre
                    </label>

                    <input v-model="form.nombre" type="text" :disabled="!editando"
                        class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition disabled:opacity-80 border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]" />

                    <p v-if="form.errors.nombre"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.nombre }}
                    </p>
                </div>

                <div>
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Apellido
                    </label>

                    <input v-model="form.apellido" type="text" :disabled="!editando"
                        class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition disabled:opacity-80 border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]" />

                    <p v-if="form.errors.apellido"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.apellido }}
                    </p>
                </div>

                <div>
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Fecha nacimiento
                    </label>

                    <input v-model="form.fecha_nacimiento" type="date" :disabled="!editando"
                        class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition disabled:opacity-80 border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]" />

                    <p v-if="form.errors.fecha_nacimiento"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.fecha_nacimiento }}
                    </p>
                </div>

                <div>
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Género
                    </label>

                    <select v-model="form.genero" :disabled="!editando"
                        class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition disabled:opacity-80 border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]">
                        <option value="">Sin género</option>
                        <option value="masculino">Masculino</option>
                        <option value="femenino">Femenino</option>
                        <option value="otro">Otro</option>
                    </select>

                    <p v-if="form.errors.genero"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.genero }}
                    </p>
                </div>

                <div class="sm:col-span-2">
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Dirección
                    </label>

                    <textarea v-model="form.direccion" rows="3" :disabled="!editando"
                        class="mt-2 w-full resize-none rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold leading-7 text-[var(--title)] outline-none transition disabled:opacity-80 border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]"></textarea>

                    <p v-if="form.errors.direccion"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.direccion }}
                    </p>
                </div>
            </div>
        </div>

        <!-- DATOS USUARIO -->
        <div class="space-y-6">
            <div class="rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300 border-[var(--border)]">
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Datos de usuario
                </p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                            Email
                        </label>

                        <input v-model="form.email" type="email"
                            :disabled="!editando || !tieneRol('Paciente') || tieneRol('Administrador')"
                            class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition disabled:opacity-80 border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]" />

                        <p v-if="form.errors.email"
                            class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                            Rol
                        </p>

                        <p class="mt-2 text-[length:var(--font-lg)] font-black text-[var(--title)]">
                            {{ usuario?.rol?.nombre ?? 'Sin rol' }}
                        </p>
                    </div>

                    <div>
                        <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                            Estado
                        </label>

                        <select v-if="editando" v-model="form.estado"
                            :disabled="!tieneRol('Administrador')"
                            class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition border-[var(--border)]">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>

                        <span v-else
                            class="mt-3 inline-flex rounded-xl px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-wider"
                            :class="form.estado === 'activo'
                                ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20'
                                : 'bg-slate-100 dark:bg-slate-800 text-slate-500'">
                            {{ form.estado ?? 'Sin estado' }}
                        </span>

                        <p v-if="form.errors.estado"
                            class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                            {{ form.errors.estado }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                            URL de foto actual
                        </label>

                        <input v-if="editando" v-model="form.url" type="text" placeholder="Sin URL" readonly
                            class="mt-2 w-full cursor-not-allowed rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition border-[var(--border)]" />

                        <a v-else-if="form.url" :href="assetUrl(form.url)" target="_blank" rel="noreferrer"
                            class="mt-2 block break-all rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--primary)] transition hover:underline border-[var(--border)]">
                            {{ assetUrl(form.url) }}
                        </a>

                        <p v-else
                            class="mt-2 rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--muted)] border-[var(--border)]">
                            Sin URL registrada
                        </p>
                        <p v-if="form.errors.url"
                            class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                            {{ form.errors.url }}
                        </p>
                    </div>

                    <!-- FOTO SUBIDA -->
                    <div v-if="editando"
                        class="sm:col-span-2 rounded-3xl border border-dashed p-5 text-center transition border-[var(--border)]">
                        <div
                            class="mx-auto flex h-20 w-20 items-center justify-center overflow-hidden rounded-3xl bg-[var(--bg)] text-[length:var(--font-2xl)] font-black text-[var(--primary)] border-[var(--border)]">
                            <img v-if="previewFoto" :src="assetUrl(previewFoto)" alt="Foto de perfil"
                                class="h-full w-full object-cover" />

                            <span v-else>
                                {{ iniciales || 'P' }}
                            </span>
                        </div>

                        <p class="mt-4 text-[length:var(--font-sm)] font-black text-[var(--title)]">
                            Subir nueva foto de perfil
                        </p>

                        <label
                            class="mt-4 inline-flex cursor-pointer rounded-2xl bg-[var(--text)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90">
                            Seleccionar imagen
                            <input type="file" accept="image/*" class="hidden" @change="emit('change-photo', $event)"
                                :disabled="!editando || !tieneRol('Paciente') || tieneRol('Administrador')" />
                        </label>

                        <p v-if="form.errors.foto_perfil"
                            class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                            {{ form.errors.foto_perfil }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- CAMBIAR PASSWORD -->
            <div v-if="editando && (tieneRol('Paciente') || tieneRol('Administrador'))"
                class="rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300 border-[var(--border)]">
                <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                    Cambiar contraseña
                </p>

                <p class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]">
                    Esta acción cambiará la contraseña de acceso de esta cuenta.
                </p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                            Nueva contraseña
                        </label>

                        <input v-model="passwordForm.password" type="password" placeholder="Mínimo 8 caracteres"
                            class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition border-[var(--border)]" />

                        <p v-if="passwordForm.errors.password"
                            class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                            {{ passwordForm.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                            Confirmar contraseña
                        </label>

                        <input v-model="passwordForm.password_confirmation" type="password"
                            placeholder="Repite la contraseña"
                            class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition border-[var(--border)]" />

                        <p v-if="passwordForm.errors.password_confirmation"
                            class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                            {{ passwordForm.errors.password_confirmation }}
                        </p>
                    </div>

                    <div class="sm:col-span-2 flex justify-end">
                        <button type="button" @click="emit('submit-password')" :disabled="passwordForm.processing"
                            class="rounded-2xl bg-[var(--text)] px-5 py-3 text-[length:var(--font-sm)] font-black text-[var(--card)] transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60">
                            {{ passwordForm.processing ? 'Actualizando...' : 'Cambiar contraseña' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATOS PACIENTE -->
        <div class="rounded-3xl border bg-[var(--card)] p-6 shadow transition-colors duration-300 md:col-span-2 border-[var(--border)]">
            <p class="text-[length:var(--font-sm)] font-bold uppercase tracking-[0.25em] text-[var(--primary)]">
                Datos de paciente
            </p>

            <div class="mt-5 grid gap-5 sm:grid-cols-3">
                <div>
                    <p class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Código paciente
                    </p>

                    <p class="mt-2 text-[length:var(--font-lg)] font-black text-[var(--title)]">
                        {{ paciente.codigo_paciente }}
                    </p>
                </div>

                <div>
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Contacto de emergencia
                    </label>

                    <input v-model="form.contacto_emergencia" type="text" :disabled="!editando"
                        placeholder="Sin contacto"
                        class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition disabled:opacity-80 border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]" />

                    <p v-if="form.errors.contacto_emergencia"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.contacto_emergencia }}
                    </p>
                </div>

                <div>
                    <label class="text-[length:var(--font-sm)] font-bold text-[var(--muted)]">
                        Teléfono de emergencia
                    </label>

                    <input v-model="form.telefono_emergencia" type="text" :disabled="!editando"
                        placeholder="Sin teléfono"
                        class="mt-2 w-full rounded-2xl border bg-[var(--bg)] px-4 py-3 text-[length:var(--font-base)] font-bold text-[var(--title)] outline-none transition disabled:opacity-80 border-[var(--border)] focus:ring-4 focus:ring-[var(--primary-soft)]" />

                    <p v-if="form.errors.telefono_emergencia"
                        class="mt-2 text-[length:var(--font-sm)] font-bold text-red-500">
                        {{ form.errors.telefono_emergencia }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
