<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const stats = [
    { title: 'Citas hoy', value: '24', detail: '+12% vs mes anterior', icon: '▣', color: 'text-orange-500' },
    { title: 'Pacientes activos', value: '1.284', detail: '+4% vs mes anterior', icon: '☷', color: 'text-teal-500' },
    { title: 'Ingresos del mes', value: '$28.4k', detail: '+18% vs mes anterior', icon: '$', color: 'text-sky-700' },
    { title: 'Tratamientos activos', value: '57', detail: '+6% vs mes anterior', icon: '⌁', color: 'text-amber-500' },
];

const agenda = [
    { time: '09:00', name: 'María González', service: 'Limpieza dental', status: 'Confirmada' },
    { time: '10:30', name: 'Carlos Pérez', service: 'Endodoncia', status: 'En curso' },
    { time: '11:15', name: 'Lucía Fernández', service: 'Revisión', status: 'Confirmada' },
    { time: '12:00', name: 'Javier Ruiz', service: 'Ortodoncia', status: 'Pendiente' },
    { time: '13:30', name: 'Ana Torres', service: 'Blanqueamiento', status: 'Confirmada' },
];

const pacientes = [
    { initials: 'MG', name: 'María González', last: 'Hace 2 días', next: '15 Jul' },
    { initials: 'CP', name: 'Carlos Pérez', last: 'Hoy', next: '22 Jul' },
    { initials: 'LF', name: 'Lucía Fernández', last: 'Ayer', next: '29 Jul' },
    { initials: 'JR', name: 'Javier Ruiz', last: 'Hace 1 sem', next: '5 Ago' },
];

const statusClass = (status) => {
    if (status === 'En curso') return 'bg-slate-900 text-white';
    if (status === 'Pendiente') return 'bg-slate-100 text-slate-900';
    return 'bg-white text-slate-900 border border-slate-200';
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #title>
            Resumen
        </template>

        <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <article
                v-for="stat in stats"
                :key="stat.title"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ stat.title }}</p>
                        <h3 class="mt-4 text-3xl font-black text-slate-950">{{ stat.value }}</h3>
                        <p class="mt-1 text-xs text-slate-500">{{ stat.detail }}</p>
                    </div>

                    <span class="text-xl" :class="stat.color">{{ stat.icon }}</span>
                </div>
            </article>
        </section>

        <section class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-[1fr_0.48fr]">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-black text-slate-950">Agenda de hoy</h3>

                <div class="mt-6 space-y-3">
                    <div
                        v-for="item in agenda"
                        :key="item.time"
                        class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-4"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-500">
                                <span>◷</span>
                                {{ item.time }}
                            </div>

                            <div>
                                <p class="font-black text-slate-950">{{ item.name }}</p>
                                <p class="text-sm text-slate-500">{{ item.service }}</p>
                            </div>
                        </div>

                        <span
                            class="rounded-xl px-3 py-1 text-xs font-black"
                            :class="statusClass(item.status)"
                        >
                            {{ item.status }}
                        </span>
                    </div>
                </div>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-black text-slate-950">Pacientes recientes</h3>

                <div class="mt-6 space-y-4">
                    <div
                        v-for="paciente in pacientes"
                        :key="paciente.name"
                        class="flex items-center justify-between gap-4"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-xs font-black">
                                {{ paciente.initials }}
                            </div>

                            <div>
                                <p class="font-black text-slate-950">{{ paciente.name }}</p>
                                <p class="text-xs text-slate-500">Última visita: {{ paciente.last }}</p>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-xs text-slate-500">Próxima</p>
                            <p class="text-sm font-black">{{ paciente.next }}</p>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    </AuthenticatedLayout>
</template>