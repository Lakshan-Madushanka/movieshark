import './bootstrap';
import '../css/app.css';
// import "primevue/resources/themes/lara-light-green/theme.css";
import "primevue/resources/themes/lara-dark-purple/theme.css";
import "primevue/resources/primevue.min.css";
import 'primeicons/primeicons.css'

import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from 'ziggy-js';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import BadgeDirective from 'primevue/badgedirective';
import Tooltip from 'primevue/tooltip';
import ConfirmationService from 'primevue/confirmationservice';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({el, App, props, plugin}) {
        return createApp({render: () => h(App, props)})
            .use(PrimeVue)
            .use(plugin)
            .use(ZiggyVue)
            .use(ToastService)
            .use(ConfirmationService)
            .directive('tooltip', Tooltip)
            .directive('badge', BadgeDirective)
            .mount(el);
    },
    progress: {
        color: '#FFFFFF',
        showSpinner: true,
    },
});
