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
  </v-app>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AppNotification from './components/common/AppNotification.vue';

const route = useRoute();

const layout = computed(() => {
  const l = route.meta.layout ?? 'guest'
  return `${l}-layout`
})
</script>