<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AdminSidebar from '@/components/SideBar.vue';
import { onMounted } from 'vue';
import { useTheme } from '@/composable/useTheme';
import { useFontSize } from '@/composable/useFontSize';
import ContadorPagina from '@/components/ContadorPagina.vue';
import BuscadorAcciones from '@/components/BuscadorAcciones.vue';

const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const { loadTheme } = useTheme();
const { loadFontSize } = useFontSize();
const showActionSearch = ref(
    localStorage.getItem('show-action-search') !== 'false'
);

const toggleActionSearch = () => {
    showActionSearch.value = !showActionSearch.value;

    localStorage.setItem(
        'show-action-search',
        showActionSearch.value ? 'true' : 'false'
    );
};

onMounted(() => {
    loadTheme();
    loadFontSize();
});

const showNotification = ref(false);
let timeoutId: ReturnType<typeof setTimeout> | null = null;

const page = usePage<{
    flash: {
        success?: string;
        error?: string;
        warning?: string;
        flash_id?: string;
    };
}>();

const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);
const flashWarning = computed(() => page.props.flash?.warning);
const flashId = computed(() => page.props.flash?.flash_id);
const { resetPublicTheme } = useTheme();
const { resetPublicFontSize } = useFontSize();


watch(
    flashId,
    () => {
        if (flashSuccess.value || flashError.value || flashWarning.value) {
            showNotification.value = true;

            if (timeoutId) {
                clearTimeout(timeoutId);
            }

            timeoutId = setTimeout(() => {
                showNotification.value = false;
            }, 5000);
        }
    },
    { immediate: true }
);
const resetPublicPreferences = () => {
    resetPublicTheme();
    resetPublicFontSize();
};
</script>

<template>
    <div class="min-h-screen bg-[var(--bg)] text-[var(--text)] transition-colors duration-300">
        <AdminSidebar :open="sidebarOpen" :collapsed="sidebarCollapsed" @close="sidebarOpen = false"
            @toggle-collapse="sidebarCollapsed = !sidebarCollapsed" />

        <div class="transition-all duration-300" :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'">
            <header
                class="sticky top-0 z-30 border-b bg-[var(--navbar)] px-4 shadow-[0_8px_30px_rgba(0,169,157,0.08)] backdrop-blur transition-colors duration-300 sm:px-6"
                style="border-color: var(--border)">
                <!-- FILA PRINCIPAL -->
                <div class="flex min-h-[72px] items-center justify-between gap-3">
                    <!-- IZQUIERDA: BOTÓN + TÍTULO -->
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <button
                            class="rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] lg:hidden"
                            @click="sidebarOpen = true">
                            ☰
                        </button>

                        <button
                            class="hidden rounded-xl p-2 text-[var(--muted)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)] lg:inline-flex"
                            @click="sidebarCollapsed = !sidebarCollapsed">
                            ☰
                        </button>

                        <h2
                            class="min-w-0 truncate text-[length:var(--font-xl)] font-black leading-tight text-[var(--title)]">
                            <slot name="title">Resumen</slot>
                        </h2>
                    </div>

                    <!-- CENTRO: BUSCADOR SOLO EN ESCRITORIO -->
                    <div v-if="showActionSearch" class="hidden min-w-0 flex-[1.4] justify-center lg:flex">
                        <div class="w-full max-w-[620px] xl:max-w-[760px]">
                            <BuscadorAcciones compact />
                        </div>
                    </div>

                    <!-- DERECHA: ACCIONES -->
                    <div class="flex shrink-0 items-center justify-end gap-2 sm:gap-3">
                        <!-- BOTÓN MOSTRAR / OCULTAR BUSCADOR -->
                        <button type="button"
                            class="rounded-xl border bg-[var(--card)] px-3 py-2 text-[length:var(--font-sm)] font-black text-[var(--title)] transition hover:bg-[var(--section-soft)] hover:text-[var(--primary)]"
                            style="border-color: var(--border)"
                            :title="showActionSearch ? 'Ocultar buscador' : 'Mostrar buscador'"
                            @click="toggleActionSearch">
                            <span v-if="showActionSearch">
                                🔍
                            </span>

                            <span v-else>
                                🔎
                            </span>
                        </button>

                        <Link :href="route('logout')" method="post" as="button"
                            class="rounded-xl bg-[var(--text)] px-4 py-2 text-[length:var(--font-sm)] font-bold text-[var(--card)] transition hover:opacity-90"
                            @before="resetPublicPreferences">
                            Salir
                        </Link>

                        <ContadorPagina />
                    </div>
                </div>

                <!-- BUSCADOR EN MÓVIL / TABLET -->
                <div v-if="showActionSearch" class="pb-4 lg:hidden">
                    <BuscadorAcciones compact />
                </div>
            </header>

            <Transition name="toast">
                <div v-if="showNotification && flashSuccess"
                    class="fixed right-6 top-24 z-50 flex w-[calc(100%-2rem)] max-w-md items-start gap-3 overflow-hidden rounded-2xl bg-green-600 p-4 text-white shadow-xl">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-xl font-black">
                        ✓
                    </div>

                    <div class="flex-1">
                        <h3 class="text-base font-black">
                            Operación exitosa
                        </h3>

                        <p class="mt-1 text-sm font-semibold text-green-50">
                            {{ flashSuccess }}
                        </p>
                    </div>

                    <button type="button"
                        class="rounded-lg px-2 text-xl font-black text-white/80 hover:bg-white/20 hover:text-white"
                        @click="showNotification = false">
                        ×
                    </button>
                </div>
            </Transition>

            <Transition name="toast">
                <div v-if="showNotification && flashError"
                    class="fixed right-6 top-24 z-50 flex w-[calc(100%-2rem)] max-w-md items-start gap-3 overflow-hidden rounded-2xl bg-red-600 p-4 text-white shadow-xl">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-xl font-black">
                        !
                    </div>

                    <div class="flex-1">
                        <h3 class="text-base font-black">
                            Ocurrió un error
                        </h3>

                        <p class="mt-1 text-sm font-semibold text-red-50">
                            {{ flashError }}
                        </p>
                    </div>

                    <button type="button"
                        class="rounded-lg px-2 text-xl font-black text-white/80 hover:bg-white/20 hover:text-white"
                        @click="showNotification = false">
                        ×
                    </button>
                </div>
            </Transition>

            <Transition name="toast">
                <div v-if="showNotification && flashWarning"
                    class="fixed right-6 top-24 z-50 flex w-[calc(100%-2rem)] max-w-md items-start gap-3 overflow-hidden rounded-2xl bg-amber-500 p-4 text-white shadow-xl">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-xl font-black">
                        ⚠
                    </div>

                    <div class="flex-1">
                        <h3 class="text-base font-black">
                            Atención
                        </h3>

                        <p class="mt-1 text-sm font-semibold text-amber-50">
                            {{ flashWarning }}
                        </p>
                    </div>

                    <button type="button"
                        class="rounded-lg px-2 text-xl font-black text-white/80 hover:bg-white/20 hover:text-white"
                        @click="showNotification = false">
                        ×
                    </button>
                </div>
            </Transition>

            <main class="p-4 sm:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-12px) translateX(24px);
}
</style>