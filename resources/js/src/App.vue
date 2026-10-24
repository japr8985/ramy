<template>
  <v-app>
      <component :is="layout">
        <v-main>
          <v-container fluid class="fill-height align-start">
            <router-view />
          </v-container>
        </v-main>
      </component>
    <app-notification />
    <!-- Notificación de actualización (solo visible tras autenticarse en MainLayout) -->
  <PwaUpdateSnackbar />
  </v-app>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AppNotification from './components/common/AppNotification.vue';
import PwaUpdateSnackbar from './components/common/PwaUpdateSnackbar.vue';

const route = useRoute();

const layout = computed(() => {
  const l = route.meta.layout ?? 'guest'
  return `${l}-layout`
})
</script>