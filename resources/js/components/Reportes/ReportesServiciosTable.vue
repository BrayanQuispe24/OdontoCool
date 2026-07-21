<script setup lang="ts">
type ServicioMasPrestado = { servicio: string; cantidad: number; total: number };

defineProps<{
    servicios: ServicioMasPrestado[];
}>();

const formatoMoney = (value: number) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(Number(value ?? 0));
};
</script>

<template>
    <div class="rounded-3xl border bg-[var(--card)] p-5 shadow-sm border-[var(--border)]">
        <h3 class="mb-4 text-[length:var(--font-base)] font-black text-[var(--title)]">
            Detalle de servicios más prestados
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left">
                <thead>
                    <tr class="border-b border-[var(--border)]">
                        <th class="px-4 py-3 text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                            Servicio
                        </th>
                        <th class="px-4 py-3 text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                            Cantidad
                        </th>
                        <th class="px-4 py-3 text-[length:var(--font-xs)] font-black text-[var(--muted)]">
                            Total generado
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="servicio in servicios" :key="servicio.servicio"
                        class="border-b border-[var(--border)]">
                        <td class="px-4 py-3 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                            {{ servicio.servicio }}
                        </td>
                        <td class="px-4 py-3 text-[length:var(--font-sm)] text-[var(--muted)]">
                            {{ servicio.cantidad }}
                        </td>
                        <td class="px-4 py-3 text-[length:var(--font-sm)] font-black text-[var(--primary)]">
                            {{ formatoMoney(servicio.total) }}
                        </td>
                    </tr>

                    <tr v-if="servicios.length === 0">
                        <td colspan="3"
                            class="px-4 py-6 text-center text-[length:var(--font-sm)] text-[var(--muted)]">
                            No hay servicios prestados en el rango seleccionado.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
