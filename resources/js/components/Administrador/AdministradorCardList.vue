<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import type { Propietario } from '@/types/Propietario';

defineProps<{
    propietarios: Propietario[];
}>();

const emit = defineEmits<{
    edit: [propietario: Propietario];
}>();

const eliminarPropietario = (codigo: string) => {
    if (!confirm('¿Estás seguro de eliminar este propietario?')) {
        return;
    }

    router.delete(route('administrador.destroy', codigo), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="grid grid-cols-1 gap-5 md:hidden">
        <article
            v-for="propietario in propietarios"
            :key="propietario.codigo_propietario"
            class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)]"
            style="border-color: var(--border)"
        >
            <div
                class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-[var(--primary-soft)]"
            />

            <div class="relative z-10">
                <div
                    class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-2xl)] font-black text-white shadow-lg"
                >
                    {{
                        propietario.persona?.nombre
                            ?.charAt(0)
                            .toUpperCase() ?? 'P'
                    }}
                </div>

                <h2
                    class="text-[length:var(--font-xl)] font-black text-[var(--title)]"
                >
                    {{ propietario.persona?.nombre ?? 'Sin nombre' }}
                    {{ propietario.persona?.apellido ?? '' }}
                </h2>

                <p
                    class="mt-2 text-[length:var(--font-sm)] text-[var(--muted)]"
                >
                    {{
                        propietario.persona?.usuarios?.[0]?.email ??
                        'Sin email'
                    }}
                </p>

                <div
                    class="mt-5 space-y-2 text-[length:var(--font-sm)] text-[var(--muted)]"
                >
                    <p>
                        <span class="font-bold text-[var(--title)]">
                            CI:
                        </span>

                        {{
                            propietario.persona?.carnet_identidad ??
                            propietario.persona_id
                        }}
                    </p>

                    <p>
                        <span class="font-bold text-[var(--title)]">
                            Fecha inicio:
                        </span>

                        {{ propietario.fecha_inicio ?? 'Sin fecha' }}
                    </p>

                    <p>
                        <span class="font-bold text-[var(--title)]">
                            Participación:
                        </span>

                        {{ propietario.porcentaje_participacion ?? 0 }}%
                    </p>

                    <p v-if="propietario.persona?.telefono">
                        <span class="font-bold text-[var(--title)]">
                            Teléfono:
                        </span>

                        {{ propietario.persona.telefono }}
                    </p>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <span
                        class="rounded-full px-4 py-2 text-[length:var(--font-sm)] font-black capitalize"
                        :class="
                            propietario.persona?.usuarios?.[0]?.estado ===
                            'inactivo'
                                ? 'bg-red-50 text-red-500'
                                : 'bg-[var(--primary-soft)] text-[var(--primary)]'
                        "
                    >
                        {{
                            propietario.persona?.usuarios?.[0]?.estado ??
                            'activo'
                        }}
                    </span>

                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            title="Editar propietario"
                            class="text-[length:var(--font-xl)] font-black text-[var(--primary)] transition hover:text-[var(--primary-hover)]"
                            @click="emit('edit', propietario)"
                        >
                            ✎
                        </button>

                        <button
                            type="button"
                            title="Eliminar propietario"
                            class="text-[length:var(--font-xl)] font-black text-red-500 transition hover:text-red-700"
                            @click="
                                eliminarPropietario(
                                    propietario.codigo_propietario,
                                )
                            "
                        >
                            🗑
                        </button>

                        <Link
                            :href="
                                route(
                                    'administrador.show',
                                    propietario.codigo_propietario,
                                )
                            "
                            title="Ver propietario"
                            class="text-[length:var(--font-xl)] font-black text-[var(--primary)] transition hover:text-[var(--primary-hover)]"
                        >
                            ⚙
                        </Link>
                    </div>
                </div>
            </div>
        </article>
    </div>
</template>