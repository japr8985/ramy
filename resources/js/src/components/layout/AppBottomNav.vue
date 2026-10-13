<!-- resources/js/src/components/layout/AppBottomNav.vue -->
<template>
  <v-bottom-navigation
    app
    grow
    color="primary"
    elevation="4"
    class="border-t mobile-nav-bar"
  >
    <!-- 4 Accesos Core -->
    <v-btn
      v-for="tab in mobileBottomTabs"
      :key="tab.to"
      :to="tab.to"
      class="nav-tab-btn"
    >
      <v-icon size="20" class="mb-1">{{ tab.icon }}</v-icon>
      <span class="nav-tab-label">{{ tab.title }}</span>
    </v-btn>

    <!-- Botón 'Más' -->
    <v-btn
      :active="uiStore.isNavDrawerOpen"
      class="nav-tab-btn"
      @click="uiStore.toggleNavDrawer()"
    >
      <v-icon size="20" class="mb-1">mdi-menu</v-icon>
      <span class="nav-tab-label">Más</span>
    </v-btn>
  </v-bottom-navigation>
</template>

<script setup lang="ts">
import { useUiStore } from '@/stores/ui';
import { mobileBottomTabs } from '@/navigation/navItems';

const uiStore = useUiStore();
</script>

<style scoped>
.mobile-nav-bar {
  /* Evita que el contenedor desborde el ancho de la pantalla */
  max-width: 100vw !important;
  overflow: hidden !important;
}

/* Forzar que cada botón ocupe exactamente 1/5 (20%) del ancho disponible */
:deep(.v-bottom-navigation__content) {
  display: flex !important;
  width: 100% !important;
  justify-content: space-between !important;
}

:deep(.nav-tab-btn) {
  min-width: 0 !important;
  flex: 1 1 0 !important;
  padding: 4px 1px !important;
  height: 100% !important;
}

/* Iconos y textos compactos para encajar en 64px por pestaña */
.nav-tab-label {
  font-size: 0.68rem !important;
  line-height: 1.1 !important;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}
</style>