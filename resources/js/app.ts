import { createApp } from 'vue';
import App from './src/App.vue';
import vuetify from './src/plugins/vuetify';
import router from './src/router';
import { createPinia } from 'pinia';
import { initPwa } from '@/composables/usePwaUpdate';
// Iniciar el listener de Service Worker
initPwa();

const pinia = createPinia();
const app = createApp(App);

import GuestLayout from '@/layouts/guest/index.vue';
import MainLayout from '@/layouts/main/index.vue';

app.component('guest-layout', GuestLayout);
app.component('main-layout', MainLayout);

app.use(pinia);
app.use(router);
app.use(vuetify);
app.mount('#app');