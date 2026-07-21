<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Propietario } from '@/types/Propietario';

defineProps<{
    propietarios: Propietario[];
}>();

const emit = defineEmits<{
    edit: [propietario: Propietario];
}>();
</script>

<template>
    <div
        class="hidden overflow-hidden rounded-3xl border bg-[var(--card)] md:block"
        style="border-color: var(--border)"
    >
        <table class="w-full text-left text-sm">
            <thead class="bg-[var(--primary-soft)] text-[var(--title)]">
                <tr>
                    <th class="px-6 py-4">Nombre</th>
                    <th class="px-6 py-4">CI</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Fecha inicio</th>
                    <th class="px-6 py-4">Participación</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="propietario in propietarios"
                    :key="propietario.codigo_propietario"
                    class="border-t"
                    style="border-color: var(--border)"
                >
                    <td class="px-6 py-4 font-bold">
                        {{ propietario.persona?.nombre }}
                        {{ propietario.persona?.apellido }}
                    </td>

                    <td class="px-6 py-4">
                        {{ propietario.persona?.carnet_identidad }}
                    </td>

                    <td class="px-6 py-4">
                        {{
                            propietario.persona?.usuarios?.[0]?.email
                                ?? 'Sin email'
                        }}
                    </td>

                    <td class="px-6 py-4">
                        {{ propietario.fecha_inicio ?? 'Sin fecha' }}
                    </td>

                    <td class="px-6 py-4">
                        {{ propietario.porcentaje_participacion ?? 0 }}%
                    </td>

                    <td class="px-6 py-4">
                        {{
                            propietario.persona?.usuarios?.[0]?.estado
                                ?? 'Sin estado'
                        }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-3">
                            <button
                                type="button"
                                class="rounded-xl bg-[var(--primary-soft)] px-4 py-2"
                                @click="emit('edit', propietario)"
                            >
                                Editar
                            </button>

                            <Link
                                :href="route(
                                    'administrador.show',
                                    propietario.codigo_propietario
                                )"
                                class="rounded-xl bg-gray-100 px-4 py-2"
                            >
                                Ver
                            </Link>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>