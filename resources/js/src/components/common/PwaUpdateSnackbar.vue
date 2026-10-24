<script setup lang="ts">
import { ref } from 'vue';
import { usePwaUpdate } from '@/composables/usePwaUpdate';

const { needRefresh, refreshApp, dismissUpdate } = usePwaUpdate();
const loading = ref(false);

const handleUpdate = async () => {
  loading.value = true;
  await refreshApp();
};
</script>

<template>
  <v-snackbar
    v-model="needRefresh"
    :timeout="-1"
    location="bottom right"
    color="surface-light"
    elevation="6"
    rounded="lg"
    class="mb-4 mr-4"
  >
    <div class="d-flex align-center ga-3">
      <v-avatar color="primary" size="36" variant="tonal">
        <v-icon icon="mdi-cloud-download" color="primary"></v-icon>
      </v-avatar>

      <div>
        <div class="text-subtitle-2 font-weight-bold text-high-emphasis">
          Nueva versión disponible
        </div>
        <div class="text-caption text-medium-emphasis">
          Actualiza para obtener los últimos cambios de Ramy Store.
        </div>
      </div>
    </div>

    <template #actions>
      <!-- <v-btn
        variant="text"
        color="on-surface-variant"
        density="comfortable"
        @click="dismissUpdate"
      >
        Después
      </v-btn> -->

      <v-btn
        color="primary"
        variant="flat"
        density="comfortable"
        :loading="loading"
        @click="handleUpdate"
      >
        Actualizar
      </v-btn>
    </template>
  </v-snackbar>
</template>