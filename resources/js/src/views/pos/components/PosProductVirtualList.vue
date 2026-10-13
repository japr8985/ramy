<template>
  <div class="pos-virtual-list-container h-100 overflow-hidden">
    <!-- Estado de carga -->
    <div v-if="isLoading" class="d-flex flex-column gap-2 pa-3">
      <v-skeleton-loader v-for="n in 7" :key="n" type="list-item-two-line" height="52" />
    </div>

    <!-- Catálogo vacío -->
    <div
      v-else-if="products.length === 0"
      class="h-100 d-flex flex-column align-center justify-center text-center pa-6 text-medium-emphasis"
    >
      <v-icon size="44" color="medium-emphasis" class="mb-2">mdi-package-variant-closed-remove</v-icon>
      <span class="text-subtitle-2 font-weight-bold">No hay productos</span>
      <span class="text-caption">Ajuste el término de búsqueda o la categoría.</span>
    </div>

    <!-- Virtual Scroller activo (item-height fijado en 52px) -->
    <v-virtual-scroll
      v-else
      :items="products"
      :item-height="52"
      class="h-50"
    >
      <template #default="{ item }">
        <PosProductListItem :product="item" @add="posStore.addToCart(item)" />
      </template>
    </v-virtual-scroll>
  </div>
</template>

<script setup lang="ts">
import { usePosStore } from '@/stores/pos';
import type { Product } from '@/types/catalog';
import PosProductListItem from './PosProductListItem.vue';

defineProps<{
  products: Product[];
  isLoading: boolean;
}>();

const posStore = usePosStore();
</script>

<style scoped>
.pos-virtual-list-container {
  min-height: 0;
  display: flex;
  flex-direction: column;
}
.gap-2 { gap: 8px; }
</style>