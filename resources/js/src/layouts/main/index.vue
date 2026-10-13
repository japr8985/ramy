<script setup lang="ts">
import AppBottomNav from '@/components/layout/AppBottomNav.vue';
import AppToolbar from '@/components/layout/AppToolbar.vue';
import Navigation from '@/components/layout/navigation.vue';
import { useUiStore } from '@/stores/ui';
import { useDisplay } from 'vuetify';
const { mobile } = useDisplay();
const uiStore = useUiStore();
</script>

<template>
  <!-- Drawer Lateral -->
  <navigation />
  <!-- Barra Superior -->
  <app-toolbar />

  <!-- Área de Trabajo -->
  <slot />
  <!-- Barra Inferior -->
  <AppBottomNav v-if="uiStore.isHandheld && uiStore.showBottomNav" />
</template>
