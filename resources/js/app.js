import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { useTheme } from './composable/useTheme';
import { useFontSize } from './composable/useFontSize';
import { createApp, h } from 'vue';



const appName = import.meta.env.VITE_APP_NAME || 'OdontoCool';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),

    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob('./pages/**/*.vue'),
        ),

    setup({ el, App, props, plugin }) {
        const { loadTheme } = useTheme();
        const { loadFontSize } = useFontSize();
        loadTheme();
        loadFontSize();

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },

    progress: {
        color: '#18dccf',
    },
});