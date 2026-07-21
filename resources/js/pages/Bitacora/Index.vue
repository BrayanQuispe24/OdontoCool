<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Bitacora } from '@/types/Bitacora';

const props = defineProps<{
    bitacoras: Bitacora[];
}>();

const search = ref('');
const moduloFiltro = ref('');
const fechaFiltro = ref('');

const modulosDisponibles = computed(() => {
    const mods = props.bitacoras.map((b) => b.modulo);
    return [...new Set(mods)].filter(Boolean);
});

const getIniciales = (nombre?: string, apellido?: string) => {
    const n = nombre?.charAt(0) ?? '';
    const a = apellido?.charAt(0) ?? '';
    return `${n}${a}`.toUpperCase() || 'SYS';
};

const getBrowserAndOS = (userAgent?: string | null) => {
    if (!userAgent) return 'Desconocido';
    let os = 'OS';
    let browser = 'Navegador';

    if (userAgent.includes('Windows')) os = 'Windows';
    else if (userAgent.includes('Macintosh') || userAgent.includes('Mac OS')) os = 'macOS';
    else if (userAgent.includes('Linux')) os = 'Linux';
    else if (userAgent.includes('Android')) os = 'Android';
    else if (userAgent.includes('iPhone') || userAgent.includes('iPad')) os = 'iOS';

    if (userAgent.includes('Edg')) browser = 'Edge';
    else if (userAgent.includes('Chrome')) browser = 'Chrome';
    else if (userAgent.includes('Firefox')) browser = 'Firefox';
    else if (userAgent.includes('Safari')) browser = 'Safari';

    return `${browser} - ${os}`;
};

const bitacorasFiltradas = computed(() => {
    return props.bitacoras.filter((log) => {
        const query = search.value.toLowerCase();
        
        // Search term matches
        const personaNombre = log.persona
            ? `${log.persona.nombre} ${log.persona.apellido}`.toLowerCase()
            : 'sistema';
        const personaCi = log.persona_id ? log.persona_id.toLowerCase() : '';
        const actionMatches = log.accion.toLowerCase().includes(query);
        const moduleMatches = log.modulo.toLowerCase().includes(query);
        
        const matchesQuery = 
            personaNombre.includes(query) ||
            personaCi.includes(query) ||
            actionMatches ||
            moduleMatches ||
            (log.ip_address?.toLowerCase() || '').includes(query);

        // Modulo filter matches
        const matchesModulo = !moduloFiltro.value || log.modulo === moduloFiltro.value;

        // Fecha filter matches
        const matchesFecha = !fechaFiltro.value || log.fecha.toString().startsWith(fechaFiltro.value);

        return matchesQuery && matchesModulo && matchesFecha;
    });
});


</script>

<template>
    <Head title="Bitácora de Actividades" />

    <AuthenticatedLayout>
        <template #title> Bitácora de Actividades </template>

        <div class="space-y-6 p-4 text-[var(--text)] transition-colors duration-300 sm:p-6 lg:p-8">
            <!-- Header Panel -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-[length:var(--font-lg)] font-black text-[var(--title)]">
                        Historial de Auditoría
                    </h3>
                    <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                        Monitorea las acciones de los usuarios y cambios realizados en el sistema.
                    </p>
                </div>
            </div>

            <!-- Search and Filter Panel -->
            <div
                class="grid gap-4 rounded-3xl border bg-[var(--card)] p-5 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 md:grid-cols-3"
                style="border-color: var(--border)"
            >
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[var(--primary)]">
                        ⌕
                    </span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por usuario, CI, acción, IP..."
                        class="w-full rounded-2xl border bg-[var(--section-soft)] py-3.5 pl-10 pr-5 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition placeholder:text-[var(--muted)] focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                    />
                </div>

                <div>
                    <select
                        v-model="moduloFiltro"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-3.5 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                    >
                        <option value="">Todos los Módulos</option>
                        <option v-for="mod in modulosDisponibles" :key="mod" :value="mod">
                            {{ mod }}
                        </option>
                    </select>
                </div>

                <div>
                    <input
                        v-model="fechaFiltro"
                        type="date"
                        class="w-full rounded-2xl border bg-[var(--section-soft)] px-5 py-3.5 text-[length:var(--font-sm)] text-[var(--title)] outline-none transition focus:bg-[var(--card)] focus:ring-4"
                        style="border-color: var(--border); --tw-ring-color: var(--primary-soft)"
                    />
                </div>
            </div>

            <!-- List Table / Desktop Table & Mobile Cards -->
            <div v-if="bitacorasFiltradas.length" class="space-y-6">
                <!-- Tabla para desktop -->
                <div
                    class="hidden overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300 lg:block"
                    style="border-color: var(--border)"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-[length:var(--font-sm)]">
                            <thead class="bg-[var(--section-soft)] text-[length:var(--font-xs)] uppercase tracking-wider text-[var(--muted)]">
                                <tr>
                                    <th class="px-6 py-4 font-black">Usuario</th>
                                    <th class="px-6 py-4 font-black">Módulo</th>
                                    <th class="px-6 py-4 font-black">Acción</th>
                                    <th class="px-6 py-4 font-black">Fecha / Hora</th>
                                    <th class="px-6 py-4 font-black">Dispositivo / IP</th>
                                    <th class="px-6 py-4 text-right font-black">Detalle</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y" style="border-color: var(--border)">
                                <tr
                                    v-for="log in bitacorasFiltradas"
                                    :key="log.id"
                                    class="transition hover:bg-[var(--section-soft)]"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-sm)] font-black text-white shrink-0"
                                            >
                                                {{ getIniciales(log.persona?.nombre, log.persona?.apellido) }}
                                            </div>

                                            <div>
                                                <div class="font-black text-[var(--title)]">
                                                    {{ log.persona ? `${log.persona.nombre} ${log.persona.apellido}` : 'Sistema / Admin' }}
                                                </div>
                                                <div class="text-[length:var(--font-xs)] font-bold text-[var(--muted)]">
                                                    {{ log.persona_id ? `CI: ${log.persona_id}` : 'Acción Automatizada' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="inline-block rounded-xl bg-[var(--primary-soft)] px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase text-[var(--primary)]">
                                            {{ log.modulo }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 font-semibold text-[var(--title)]">
                                        {{ log.accion }}
                                    </td>

                                    <td class="px-6 py-4 text-[var(--title)]">
                                        <div>{{ log.fecha.toString().substring(0, 10) }}</div>
                                        <div class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                            {{ log.hora }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-[var(--title)]">
                                            {{ getBrowserAndOS(log.user_agent) }}
                                        </div>
                                        <div class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                            IP: {{ log.ip_address || 'N/A' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                         <Link
                                             :href="route('bitacora.show', log.id)"
                                             class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--card)] hover:text-[var(--primary)] text-lg inline-block"
                                             title="Ver Detalle de Payload"
                                         >
                                             👁
                                         </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tarjetas para móvil/tablet -->
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:hidden">
                    <article
                        v-for="log in bitacorasFiltradas"
                        :key="log.id"
                        class="group relative overflow-hidden rounded-3xl border bg-[var(--card)] p-6 shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,169,157,0.16)]"
                        style="border-color: var(--border)"
                    >
                        <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-[var(--primary-soft)]"></div>

                        <div class="relative z-10 flex h-full flex-col justify-between">
                            <div>
                                <div class="mb-4 flex items-center gap-3">
                                    <div
                                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--primary)] text-[length:var(--font-sm)] font-black text-white shrink-0"
                                    >
                                        {{ getIniciales(log.persona?.nombre, log.persona?.apellido) }}
                                    </div>
                                    <div>
                                        <h2 class="text-[length:var(--font-sm)] font-black text-[var(--title)]">
                                            {{ log.persona ? `${log.persona.nombre} ${log.persona.apellido}` : 'Sistema / Admin' }}
                                        </h2>
                                        <p class="text-[length:var(--font-xs)] text-[var(--muted)]">
                                            CI: {{ log.persona_id || 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="inline-block rounded-lg bg-[var(--primary-soft)] px-2.5 py-1 text-[length:var(--font-xs)] font-black uppercase text-[var(--primary)]">
                                        {{ log.modulo }}
                                    </span>
                                    <span class="text-[length:var(--font-xs)] text-[var(--muted)] py-1">
                                        📅 {{ log.fecha.toString().substring(0, 10) }} {{ log.hora }}
                                    </span>
                                </div>

                                <p class="mt-4 text-[length:var(--font-sm)] font-bold text-[var(--title)]">
                                    {{ log.accion }}
                                </p>

                                <div class="mt-4 rounded-2xl bg-[var(--section-soft)] p-4 text-[length:var(--font-xs)] text-[var(--muted)] space-y-1">
                                    <p><span class="font-bold">IP:</span> {{ log.ip_address || 'N/A' }}</p>
                                    <p><span class="font-bold">Dispositivo:</span> {{ getBrowserAndOS(log.user_agent) }}</p>
                                </div>
                            </div>

                             <div class="mt-6 flex justify-end">
                                 <Link
                                     :href="route('bitacora.show', log.id)"
                                     class="flex items-center gap-1.5 rounded-xl bg-[var(--primary-soft)] px-4 py-2 text-[length:var(--font-xs)] font-black text-[var(--primary)] transition hover:opacity-90"
                                 >
                                     👁 Ver Detalle
                                 </Link>
                             </div>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="flex flex-col items-center justify-center rounded-3xl border bg-[var(--card)] p-12 text-center shadow-[0_15px_40px_rgba(0,169,157,0.08)] transition-colors duration-300"
                style="border-color: var(--border)"
            >
                <span class="text-[length:var(--font-3xl)]">📋</span>
                <h3 class="mt-4 text-[length:var(--font-xl)] font-black text-[var(--title)]">
                    No se encontraron registros
                </h3>
                <p class="mt-2 max-w-sm text-[length:var(--font-sm)] text-[var(--muted)]">
                    No hay eventos de auditoría que coincidan con los filtros de búsqueda actuales.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
