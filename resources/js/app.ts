import { createApp } from 'vue';
import App from './src/App.vue';
import vuetify from './src/plugins/vuetify';
import { registerSW } from 'virtual:pwa-register';

// Registra y actualiza automáticamente el Service Worker
registerSW({ immediate: true });

const app = createApp(App);
app.use(vuetify);
app.mount('#app');