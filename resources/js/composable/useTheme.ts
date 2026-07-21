import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types/auth';

export type Theme =
    | 'light'
    | 'dark'
    | 'odontocool'
    | 'infantil'
    | 'juvenil'
    | 'adulto';

const theme = ref<Theme>('light');

const PUBLIC_DEFAULT_THEME: Theme = 'odontocool';

const STORAGE_KEY = 'app-theme';
const AUTO_THEME_KEY = 'app-auto-theme';
const AUTO_THEME_ASKED_KEY = 'app-auto-theme-asked';

const validThemes: Theme[] = [
    'light',
    'dark',
    'odontocool',
    'infantil',
    'juvenil',
    'adulto',
];

const autoThemeEnabled = ref(false);
let autoThemeInterval: number | null = null;

function isValidTheme(value: string | null | undefined): value is Theme {
    return validThemes.includes(value as Theme);
}

function getSystemThemeByHour(): Theme {
    const hour = new Date().getHours();

    // Noche: 19:00 a 05:59
    if (hour >= 19 || hour < 6) {
        return 'dark';
    }

    // Día: 06:00 a 18:59
    return 'light';
}

function setTheme(value: Theme) {
    theme.value = value;
    document.documentElement.setAttribute('data-theme', value);
    localStorage.setItem(STORAGE_KEY, value);
}

function saveThemeInDatabase(value: Theme) {
    const page = usePage<PageProps>();
    const user = page.props?.auth?.user;

    if (!user) return;

    router.patch(
        route('preferencias.update'),
        { theme: value },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
}

function applyTheme(value: Theme) {
    setTheme(value);
    saveThemeInDatabase(value);
}

function applyAutomaticTheme() {
    const systemTheme = getSystemThemeByHour();

    if (theme.value === systemTheme) {
        return;
    }

    applyTheme(systemTheme);
}

function enableAutoTheme() {
    localStorage.setItem(getAutoThemeKey(), 'true');
    localStorage.setItem(getAutoThemeAskedKey(), 'true');

    autoThemeEnabled.value = true;

    applyAutomaticTheme();
    startAutoThemeWatcher();
}

function disableAutoTheme() {
    localStorage.setItem(getAutoThemeKey(), 'false');
    localStorage.setItem(getAutoThemeAskedKey(), 'true');

    autoThemeEnabled.value = false;
}
function shouldAskForAutoTheme() {
    const alreadyAsked = localStorage.getItem(getAutoThemeAskedKey());

    return alreadyAsked !== 'true';
}

function loadTheme() {
    const page = usePage<PageProps>();

    const userTheme = page.props?.auth?.user?.preferencias?.theme;

    autoThemeEnabled.value = localStorage.getItem(getAutoThemeKey()) === 'true';

    if (isValidTheme(userTheme)) {
        setTheme(userTheme);
    } else {
        const savedTheme = localStorage.getItem(STORAGE_KEY);

        if (isValidTheme(savedTheme)) {
            setTheme(savedTheme);
        } else {
            setTheme('light');
        }
    }

    if (autoThemeEnabled.value) {
        applyAutomaticTheme();
        startAutoThemeWatcher();
    }
}

function resetPublicTheme() {
    setTheme(PUBLIC_DEFAULT_THEME);
}

const suggestedTheme = computed<Theme>(() => {
    return getSystemThemeByHour();
});

const suggestedThemeLabel = computed<string>(() => {
    return suggestedTheme.value === 'dark' ? 'oscuro' : 'claro';
});

function startAutoThemeWatcher() {
    if (autoThemeInterval !== null) {
        return;
    }

    autoThemeInterval = window.setInterval(() => {
        if (!autoThemeEnabled.value) {
            return;
        }

        applyAutomaticTheme();
    }, 10 * 60 * 1000);
}

function getCurrentUserId(): string | null {
    const page = usePage<PageProps>();
    const user = page.props?.auth?.user;

    if (!user?.id) {
        return null;
    }

    return String(user.id);
}

function getAutoThemeKey(): string {
    const userId = getCurrentUserId();

    if (!userId) {
        return 'app-auto-theme-public';
    }

    return `app-auto-theme-user-${userId}`;
}

function getAutoThemeAskedKey(): string {
    const userId = getCurrentUserId();

    if (!userId) {
        return 'app-auto-theme-asked-public';
    }

    return `app-auto-theme-asked-user-${userId}`;
}

export function useTheme() {
    return {
        theme,
        autoThemeEnabled,
        suggestedTheme,
        suggestedThemeLabel,

        applyTheme,
        loadTheme,
        resetPublicTheme,

        enableAutoTheme,
        disableAutoTheme,
        shouldAskForAutoTheme,
        applyAutomaticTheme,
    };
}