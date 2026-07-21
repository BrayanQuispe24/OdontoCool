<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import type { LandingSearchResult } from '@/types/landing';

const query = ref('');
const results = ref<LandingSearchResult[]>([]);
const isOpen = ref(false);
const isLoading = ref(false);
const hasLoadedDefaults = ref(false);
const errorMessage = ref('');
const rootRef = ref<HTMLElement | null>(null);

let debounceTimer: number | undefined;
let requestToken = 0;

const fetchResults = async (term = '', shouldOpen = false) => {
    const currentToken = ++requestToken;
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const response = await fetch(`${route('buscar.negocio')}?q=${encodeURIComponent(term)}`, {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('No se pudieron cargar los resultados.');
        }

        const payload = await response.json() as { results?: LandingSearchResult[] };

        if (currentToken !== requestToken) {
            return;
        }

        results.value = payload.results ?? [];
        hasLoadedDefaults.value = true;
        isOpen.value = shouldOpen;
    } catch (error) {
        if (currentToken !== requestToken) {
            return;
        }

        results.value = [];
        errorMessage.value = error instanceof Error ? error.message : 'No se pudieron cargar los resultados.';
        isOpen.value = shouldOpen;
    } finally {
        if (currentToken === requestToken) {
            isLoading.value = false;
        }
    }
};

const openDropdown = () => {
    isOpen.value = true;

    if (!hasLoadedDefaults.value && results.value.length === 0) {
        void fetchResults(query.value.trim(), true);
    }
};

const selectResult = (result: LandingSearchResult) => {
    isOpen.value = false;
    query.value = result.title;

    const target = document.getElementById(result.anchor);

    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

const handleDocumentClick = (event: MouseEvent) => {
    if (!rootRef.value) {
        return;
    }

    if (!rootRef.value.contains(event.target as Node)) {
        isOpen.value = false;
    }
};

watch(query, (value) => {
    window.clearTimeout(debounceTimer);

    const trimmed = value.trim();

    if (trimmed === '') {
        void fetchResults('');
        return;
    }

    debounceTimer = window.setTimeout(() => {
        void fetchResults(trimmed, true);
    }, 250);
});

onMounted(() => {
    document.addEventListener('click', handleDocumentClick);
    void fetchResults('');
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
    window.clearTimeout(debounceTimer);
});
</script>

<template>
    <div ref="rootRef" class="relative w-full">
        <label class="sr-only" for="public-search">
            Buscar servicios, doctores, análisis...
        </label>

        <div class="relative">
            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[var(--muted)]">
                🔎
            </span>

            <input
                id="public-search"
                v-model="query"
                type="search"
                autocomplete="off"
                placeholder="Buscar servicios, doctores, análisis..."
                class="w-full rounded-full border bg-[var(--card)] py-3 pl-11 pr-4 text-[length:var(--font-sm)] font-semibold text-[var(--text)] outline-none transition focus:border-[var(--primary)] focus:ring-4 focus:ring-[var(--primary-soft)]"
                style="border-color: var(--border)"
                @focus="openDropdown"
            />
        </div>

        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-120 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-2 opacity-0"
        >
            <div
                v-if="isOpen"
                class="absolute left-0 top-[calc(100%+0.75rem)] z-50 w-full overflow-hidden rounded-3xl border bg-[var(--card)] shadow-[0_30px_90px_rgba(0,0,0,0.18)]"
                style="border-color: var(--border)"
            >
                <div class="flex items-center justify-between gap-3 border-b px-4 py-3" style="border-color: var(--border)">
                    <p class="text-[length:var(--font-xs)] font-black uppercase tracking-[0.22em] text-[var(--muted)]">
                        Resultados públicos
                    </p>

                    <span class="text-[length:var(--font-xs)] font-bold text-[var(--primary)]">
                        {{ query.trim() ? 'Filtrados' : 'Sugeridos' }}
                    </span>
                </div>

                <div v-if="isLoading" class="px-4 py-6 text-[length:var(--font-sm)] text-[var(--muted)]">
                    Buscando coincidencias...
                </div>

                <div v-else-if="errorMessage" class="px-4 py-6 text-[length:var(--font-sm)] text-red-500">
                    {{ errorMessage }}
                </div>

                <div v-else-if="results.length === 0" class="px-4 py-6 text-[length:var(--font-sm)] text-[var(--muted)]">
                    No encontramos coincidencias para esta búsqueda.
                </div>

                <div v-else class="max-h-[420px] overflow-y-auto p-2">
                    <button
                        v-for="result in results"
                        :key="`${result.type}-${result.anchor}-${result.title}`"
                        type="button"
                        class="flex w-full items-start gap-3 rounded-2xl px-4 py-3 text-left transition hover:bg-[var(--section-soft)]"
                        @click="selectResult(result)"
                    >
                        <span class="mt-0.5 inline-flex rounded-full bg-[var(--primary-soft)] px-3 py-1 text-[length:var(--font-xs)] font-black uppercase tracking-[0.18em] text-[var(--primary)]">
                            {{ result.type }}
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="block text-[length:var(--font-sm)] font-black text-[var(--title)]">
                                {{ result.title }}
                            </span>

                            <span class="mt-1 block text-[length:var(--font-xs)] leading-5 text-[var(--muted)]">
                                {{ result.description }}
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>
