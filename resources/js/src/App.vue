<template>
  <v-app>
    <v-main>
      <component :is="layout">
        <router-view />
      </component>
    </v-main>
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