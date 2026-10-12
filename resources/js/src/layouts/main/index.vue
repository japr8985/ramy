<template>
  <v-layout class="fill-height">
    <!-- Topbar: toma 'surface' automáticamente con bordes del tema -->
    <v-app-bar elevation="0" border="b" height="64">
      <v-app-bar-nav-icon @click="drawer = !drawer" />

      <div class="d-flex align-center ml-2">
        <v-icon size="20" color="primary" class="mr-2">mdi-layers-triple-outline</v-icon>
        <span class="text-subtitle-2 font-weight-bold tracking-wide">Ramy</span>
      </div>

      <v-spacer />

      <!-- Botón para alternar tema Claro/Oscuro -->
      <v-btn
        icon
        variant="text"
        size="small"
        class="mr-2"
        @click="toggleTheme"
      >
        <v-icon :icon="theme.global.current.value.dark ? 'mdi-weather-sunny' : 'mdi-weather-night'" />
      </v-btn>

      <!-- Menú Usuario -->
      <v-menu location="bottom end">
        <template #activator="{ props }">
          <v-btn v-bind="props" variant="text" rounded="lg" class="text-none">
            <v-avatar color="primary" size="30" class="mr-2 text-caption font-weight-bold">
              {{ userInitial }}
            </v-avatar>
            <span class="text-caption font-weight-medium d-none d-sm-inline">
              {{ authStore.user?.name }}
            </span>
          </v-btn>
        </template>
        <v-list border rounded="lg" elevation="4">
          <v-list-item prepend-icon="mdi-logout-variant" title="Salir" color="error" @click="handleLogout" />
        </v-list>
      </v-menu>
    </v-app-bar>

    <!-- Sidebar: usa 'surface' y borders de Vuetify -->
    <v-navigation-drawer v-model="drawer" border="e" width="260">
      <div class="pa-4 d-flex align-center">
        <v-avatar color="primary" variant="tonal" size="36" rounded="lg" class="mr-3">
          <v-icon size="20">mdi-shield-key-outline</v-icon>
        </v-avatar>
        <div>
          <div class="text-subtitle-2 font-weight-bold">WORKSPACE</div>
          <div class="text-caption text-medium-emphasis">Identity & Access</div>
        </div>
      </div>

      <v-divider />

      <v-list density="comfortable" nav class="px-3">
        <v-list-item
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          :prepend-icon="item.icon"
          :title="item.title"
          color="primary"
          rounded="lg"
        />
      </v-list>
    </v-navigation-drawer>

    <!-- Contenido principal: fondo 'background' automático de v-main -->
    <v-main>
      <v-container fluid class="pa-6">
        <router-view />
      </v-container>
      
    </v-main>
    
  </v-layout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useTheme } from 'vuetify';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const router = useRouter();
const theme = useTheme();
const authStore = useAuthStore();
const drawer = ref(true);

const navItems = [
  { title: 'Configuración', icon: 'mdi-cog-outline', to: '/settings' },
];

const userInitial = computed(() => {
  return authStore.user?.name ? authStore.user.name.charAt(0).toUpperCase() : 'U';
});

function toggleTheme() {
  theme.global.name.value = theme.global.current.value.dark ? 'mereyLight' : 'mereyDark';
}

async function handleLogout() {
  await authStore.logout();
  router.push({ name: 'login'});
  
}
</script>