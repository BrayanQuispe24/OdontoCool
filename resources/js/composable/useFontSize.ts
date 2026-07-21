import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types/auth';

export type FontSize = 'small' | 'normal' | 'large' | 'extra-large';

const fontSize = ref<FontSize>('normal');
const PUBLIC_DEFAULT_FONT_SIZE: FontSize = 'normal';

const STORAGE_KEY = 'app-font-size';

const validFontSizes: FontSize[] = ['small', 'normal', 'large', 'extra-large'];

function isValidFontSize(value: string | null | undefined): value is FontSize {
    return validFontSizes.includes(value as FontSize);
}

function setFontSize(value: FontSize) {
    fontSize.value = value;
    document.documentElement.setAttribute('data-font-size', value);
    localStorage.setItem(STORAGE_KEY, value);
}

function applyFontSize(value: FontSize) {
    setFontSize(value);

    const page = usePage<PageProps>();
    const user = page.props?.auth?.user;

    if (!user) return;

    router.patch(
        route('preferencias.update'),
        { font_size: value },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
}

function loadFontSize() {
    const page = usePage<PageProps>();

    const userFontSize = page.props?.auth?.user?.preferencias?.font_size;

    if (isValidFontSize(userFontSize)) {
        setFontSize(userFontSize);
        return;
    }

    const savedFontSize = localStorage.getItem(STORAGE_KEY);

    if (isValidFontSize(savedFontSize)) {
        setFontSize(savedFontSize);
        return;
    }

    setFontSize(PUBLIC_DEFAULT_FONT_SIZE);
}

function increaseFontSize() {
    if (fontSize.value === 'small') {
        applyFontSize('normal');
        return;
    }

    if (fontSize.value === 'normal') {
        applyFontSize('large');
        return;
    }

    if (fontSize.value === 'large') {
        applyFontSize('extra-large');
    }
}

function decreaseFontSize() {
    if (fontSize.value === 'extra-large') {
        applyFontSize('large');
        return;
    }

    if (fontSize.value === 'large') {
        applyFontSize('normal');
        return;
    }

    if (fontSize.value === 'normal') {
        applyFontSize('small');
    }
}
function resetPublicFontSize() {
    setFontSize(PUBLIC_DEFAULT_FONT_SIZE);
}

export function useFontSize() {
    return {
        fontSize,
        applyFontSize,
        loadFontSize,
        increaseFontSize,
        decreaseFontSize,
        resetPublicFontSize,
    };
}