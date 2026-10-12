import { createApp } from 'vue';
import App from './src/App.vue';
import vuetify from './src/plugins/vuetify';
import router from './src/router';
import { registerSW } from 'virtual:pwa-register';

registerSW({ immediate: true });

const pinia = createPinia();
const app = createApp(App);

import GuestLayout from '@/layouts/guest/index.vue';
import MainLayout from '@/layouts/main/index.vue';

import { createPinia } from 'pinia';

app.component('guest-layout', GuestLayout);
app.component('main-layout', MainLayout)

app.use(pinia);
app.use(router);

app.use(vuetify);
app.mount('#app');