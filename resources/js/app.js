import './bootstrap';
import '../css/app.css';
import './styles.css';  
import '@mdi/font/css/materialdesignicons.css' // Ensure you are using css-loader
import 'vuetify/dist/vuetify.min.css'


import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import VueTheMask from 'vue-the-mask';

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import ChartCard from './Components/ChartCard.vue';

const appName = 'Nome da Sua Aplicação';
const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi', // This is already the default value - only for display purposes
      },
      theme: {
        defaultTheme: 'light'
      },
      display: {
        mobileBreakpoint: 'sm',
        thresholds: {
          xs: 0,
          sm: 340,
          md: 540,
          lg: 800,
          xl: 1280,
        },
      },
      
  });

  
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
       

        app.use(plugin)
            .use(vuetify)
            .use(ZiggyVue)
            .use(VueTheMask)
            


        // Registrando o componente ChartCard globalmente
        app.component('ChartCard', ChartCard);

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
