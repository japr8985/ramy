import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useDisplay } from 'vuetify';

export const useUiStore = defineStore('ui', () => {
  const { smAndDown, width } = useDisplay();

  // Es teléfono o tablet vertical (< 960px). 
  // En 1024px esto será FALSE, activando automáticamente el layout de escritorio.
  const isHandheld = computed(() => smAndDown.value);

  // Estados de navegación
  const isNavDrawerOpen = ref(false);
  const showBottomNav = ref(true);

  function toggleNavDrawer() {
    isNavDrawerOpen.value = !isNavDrawerOpen.value;
  }

  function openNavDrawer() {
    isNavDrawerOpen.value = true;
  }

  function closeNavDrawer() {
    isNavDrawerOpen.value = false;
  }

  return {
    isHandheld,
    isNavDrawerOpen,
    showBottomNav,
    toggleNavDrawer,
    openNavDrawer,
    closeNavDrawer,
  };
});