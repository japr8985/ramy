<template>
  <v-app-bar density="comfortable" elevation="0" class="border-b px-2">
    <!-- El botón hamburguesa se muestra en pantallas grandes para colapsar/expandir el drawer -->
    <v-app-bar-nav-icon
      v-if="!uiStore.isHandheld"
      @click="uiStore.toggleNavDrawer()"
    />

    <v-app-bar-title class="app-bar-title text-truncate">
      <span class="text-subtitle-1 font-weight-bold">{{ currentRouteTitle }}</span>
    </v-app-bar-title>

    <v-spacer />

    <!-- 3. Indicador de Tasa: ultra compacto en móvil -->
    <v-chip
      size="small"
      color="primary"
      variant="tonal"
      class="font-mono font-weight-bold me-2"
    >
      <v-icon start size="14">mdi-currency-usd</v-icon>
      <span v-if="!mobile" class="me-1">BCV:</span>
      {{ Number(exchangeRate).toFixed(2) }}
    </v-chip>

    <!-- 4. Menú de Usuario / Perfil -->
    <v-menu location="bottom end">
      <template #activator="{ props }">
        <v-btn
          v-bind="props"
          variant="text"
          density="comfortable"
          class="pa-0 rounded-circle"
          min-width="36"
          width="36"
          height="36"
        >
          <v-avatar color="primary" variant="tonal" size="32">
            <span class="text-caption font-weight-bold">{{ userInitials }}</span>
          </v-avatar>
        </v-btn>
      </template>

      <v-list density="compact" rounded="lg" min-width="180">
        <v-list-item
          :title="userName"
          subtitle="Cajero / Operador"
          prepend-icon="mdi-account-outline"
        />
        <v-divider class="my-1" />
        <v-list-item
          title="Cerrar Sesión"
          prepend-icon="mdi-logout"
          color="error"
          @click="handleLogout"
        />
      </v-list>
    </v-menu>
  </v-app-bar>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useDisplay } from 'vuetify';
import { useUiStore } from '@/stores/ui';

const { mobile } = useDisplay();
const uiStore = useUiStore();
const route = useRoute();

// Título reactivo según la ruta activa
const currentRouteTitle = computed(() => {
  return (route.meta?.title as string) || 'POS';
});

// Datos de sesión y tasa (reemplazar con stores respectivos)
const exchangeRate = 1.0;
const userName = 'Jorge';
const userInitials = 'J';

function handleLogout() {
  // Lógica de logout
}
</script>

<style scoped>
/* Evita que títulos largos empujen los elementos de la derecha fuera de la pantalla */
.app-bar-title {
  min-width: 0 !important;
  flex: 1 1 auto;
}

:deep(.v-toolbar-title__placeholder) {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.font-mono {
  font-family: monospace;
}
</style>