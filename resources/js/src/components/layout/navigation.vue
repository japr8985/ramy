<template>
  <v-navigation-drawer
    v-model="uiStore.isNavDrawerOpen"
    :temporary="uiStore.isHandheld"
    :permanent="!uiStore.isHandheld"
    width="260"
    class="border-e"
  >
    <!-- Cabecera del Drawer -->
    <div class="pa-4 d-flex align-center border-b">
      <v-avatar color="primary" variant="tonal" size="38" rounded="lg" class="me-3">
        <v-icon size="22">mdi-storefront-outline</v-icon>
      </v-avatar>

      <div class="overflow-hidden">
        <div class="text-subtitle-2 font-weight-bold text-truncate leading-tight">SISTEMA POS</div>
        <div class="text-caption text-medium-emphasis text-truncate">Caja & Inventario</div>
      </div>

      <v-spacer />

      <!-- Botón de cerrar (exclusivo para smartphones) -->
      <v-btn
        v-if="mobile"
        icon="mdi-close"
        variant="text"
        density="comfortable"
        size="small"
        @click="uiStore.closeNavDrawer()"
      />
    </div>

    <!-- Lista de Navegación -->
    <v-list density="comfortable" nav class="px-2 py-3">
      <template v-for="(section, sIndex) in navigationSections" :key="section.header">
        <!-- Subencabezado de grupo funcional -->
        <v-list-subheader class="text-uppercase text-caption font-weight-bold tracking-wider text-medium-emphasis px-3 mt-1">
          {{ section.header }}
        </v-list-subheader>

        <!-- Enlaces a rutas -->
        <v-list-item
          v-for="item in section.items"
          :key="item.to"
          :to="item.to"
          :prepend-icon="item.icon"
          :title="item.title"
          rounded="lg"
          color="primary"
          class="nav-drawer-item mb-1"
          @click="handleNavigate"
        />

        <v-divider v-if="sIndex < navigationSections.length - 1" class="my-2" />
      </template>
    </v-list>
  </v-navigation-drawer>
</template>

<script setup lang="ts">
import { useDisplay } from 'vuetify';
import { useUiStore } from '@/stores/ui';
import { navigationSections } from '@/navigation/navItems';

const { mobile } = useDisplay();
const uiStore = useUiStore();

function handleNavigate() {
  // En smartphones cerramos el drawer al seleccionar la ruta para despejar la vista
  if (mobile.value) {
    uiStore.closeNavDrawer();
  }
}
</script>

<style scoped>
.leading-tight {
  line-height: 1.2;
}

.tracking-wider {
  letter-spacing: 0.06em;
}

/* Área táctil cómoda para el dedo (mínimo 44px) */
.nav-drawer-item {
  min-height: 44px;
}
</style>