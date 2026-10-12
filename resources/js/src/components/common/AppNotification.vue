<template>
  <v-snackbar
    v-model="notificationStore.isVisible"
    :color="colorByType"
    :timeout="notificationStore.timeout"
    :location="snackbarLocation"
    variant="flat"
    elevation="8"
    class="app-snackbar"
    rounded="lg"
    max-width="460"
  >
    <div class="d-flex align-center notification-content">
      <v-icon :icon="iconByType" class="mr-3 flex-shrink-0" size="22" />
      <span class="text-body-2 font-weight-medium text-white text-break">
        {{ notificationStore.message }}
      </span>
    </div>

    <template #actions>
      <v-btn
        icon="mdi-close"
        variant="text"
        size="small"
        density="compact"
        color="white"
        @click="notificationStore.close"
      />
    </template>
  </v-snackbar>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useDisplay } from 'vuetify';
import { useNotificationStore } from '@/stores/notification';

const { mobile } = useDisplay();
const notificationStore = useNotificationStore();

// En móvil se centra arriba con margen natural; en desktop va a la esquina superior derecha
const snackbarLocation = computed(() => (mobile.value ? 'top' : 'top right'));

const colorByType = computed(() => {
  switch (notificationStore.type) {
    case 'success':
      return 'teal-darken-1';
    case 'error':
      return 'deep-orange-darken-2';
    case 'warning':
      return 'amber-darken-3';
    case 'info':
    default:
      return 'primary';
  }
});

const iconByType = computed(() => {
  switch (notificationStore.type) {
    case 'success':
      return 'mdi-check-circle-outline';
    case 'error':
      return 'mdi-alert-circle-outline';
    case 'warning':
      return 'mdi-alert-outline';
    case 'info':
    default:
      return 'mdi-information-outline';
  }
});
</script>

<style scoped>
.app-snackbar :deep(.v-snackbar__wrapper) {
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
  backdrop-filter: blur(8px);
  /* Asegura un margen de resguardo de 16px por lado en dispositivos pequeños */
  max-width: calc(100vw - 32px) !important;
}

.notification-content {
  min-width: 0;
  word-break: break-word;
}
</style>