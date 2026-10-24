import { ref } from 'vue';
import { registerSW } from 'virtual:pwa-register';

const needRefresh = ref(false);
const updateServiceWorker = ref<((reloadPage?: boolean) => Promise<void>) | null>(null);

// Inicializamos el registro del Service Worker
export function initPwa() {
  const update = registerSW({
    immediate: true,
    onNeedRefresh() {
      needRefresh.value = true;
    },
    onOfflineReady() {
      console.log('Ramy Store está lista para funcionar offline');
    },
  });

  updateServiceWorker.value = update;
}

export function usePwaUpdate() {
  const refreshApp = async () => {
    if (updateServiceWorker.value) {
      await updateServiceWorker.value(true);
    }
  };

  const dismissUpdate = () => {
    needRefresh.value = false;
  };

  return {
    needRefresh,
    refreshApp,
    dismissUpdate,
  };
}