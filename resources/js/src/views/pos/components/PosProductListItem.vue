<!-- resources/js/src/views/pos/components/PosProductListItem.vue -->
<template>
  <div
    v-ripple="!isOutOfStock"
    class="pos-product-row px-3 d-flex align-center justify-space-between border-b"
    :class="{
      'is-disabled': isOutOfStock,
      'bg-surface-light': isOutOfStock,
    }"
    @click="handleClick"
  >
    <!-- Columna Izquierda: Nombre, SKU y Stock -->
    <div class="product-info flex-grow-1 overflow-hidden pe-2">
      <!-- Título de producto (blanco de alto contraste) -->
      <div class="product-name font-weight-bold text-truncate">
        {{ product.name }}
      </div>

      <!-- Metadatos: SKU + Badge de Existencia -->
      <div class="d-flex align-center gap-2 mt-1">
        <span class="text-caption font-mono text-medium-emphasis sku-text text-truncate">
          {{ product.sku }}
        </span>

        <span
          class="stock-indicator px-1 rounded font-mono font-weight-medium"
          :class="stockStatusClasses"
        >
          {{ isOutOfStock ? 'Sin stock' : `${product.quantity} disp` }}
        </span>
      </div>
    </div>

    <!-- Columna Derecha: Precios y Botón de Acción -->
    <div class="d-flex align-center gap-2 flex-shrink-0">
      <div class="price-container text-right">
        <!-- Precio en Bs referencial -->
        <div class="price-ves text-caption text-medium-emphasis font-mono">
          Bs. {{ priceInVes }}
        </div>
        <!-- Precio en USD destacado -->
        <div class="price-usd font-weight-bold text-primary font-mono">
          ${{ Number(product.selling_price).toFixed(2) }}
        </div>
      </div>

      <!-- Botón táctil + -->
      <v-btn
        icon="mdi-plus"
        size="small"
        color="primary"
        variant="tonal"
        density="comfortable"
        :disabled="isOutOfStock"
        class="row-add-btn"
        @click.stop="handleClick"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePosStore } from '@/stores/pos';
import type { Product } from '@/types/catalog';

const props = defineProps<{
  product: Product;
}>();

const emit = defineEmits<{
  (e: 'add', product: Product): void;
}>();

const posStore = usePosStore();

const isOutOfStock = computed(() => Number(props.product.quantity) <= 0);

const isLowStock = computed(() => {
  const stock = Number(props.product.quantity);
  const min = Number(props.product.min_stock ?? 0);
  return stock > 0 && stock <= min;
});

const stockStatusClasses = computed(() => {
  if (isOutOfStock.value) {
    return 'bg-error-tonal text-error';
  }
  if (isLowStock.value) {
    return 'bg-warning-tonal text-warning';
  }
  return 'bg-surface-variant text-medium-emphasis';
});

const priceInVes = computed(() => {
  const ves = Number(props.product.selling_price) * posStore.exchangeRate;
  return ves.toLocaleString('es-VE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
});

function handleClick() {
  if (isOutOfStock.value) return;
  emit('add', props.product);
}
</script>

<style scoped>
.pos-product-row {
  height: 64px; /* Altura ergonómica para 2 líneas holgadas */
  cursor: pointer;
  user-select: none;
  background-color: rgb(var(--v-theme-surface));
  border-bottom-color: rgba(255, 255, 255, 0.08) !important;
  transition: background-color 0.12s ease;
}

.pos-product-row:active:not(.is-disabled) {
  background-color: rgba(var(--v-theme-primary), 0.1) !important;
}

.is-disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Tipografía de producto */
.product-name {
  font-size: 0.88rem;
  line-height: 1.25;
  color: rgba(255, 255, 255, 0.95);
  letter-spacing: 0.01em;
}

.sku-text {
  font-size: 0.72rem !important;
  max-width: 100px;
}

/* Indicador de stock compacto */
.stock-indicator {
  font-size: 0.68rem;
  line-height: 1.4;
  white-space: nowrap;
}

.bg-error-tonal {
  background-color: rgba(var(--v-theme-error), 0.15);
}

.bg-warning-tonal {
  background-color: rgba(var(--v-theme-warning), 0.15);
}

/* Contenedor de precios numéricos tabulares */
.price-container {
  font-variant-numeric: tabular-nums;
  min-width: 85px;
}

.price-ves {
  font-size: 0.7rem !important;
  line-height: 1.1;
  color: rgba(255, 255, 255, 0.6) !important;
}

.price-usd {
  font-size: 0.92rem;
  line-height: 1.2;
  margin-top: 2px;
}

.row-add-btn {
  width: 32px !important;
  height: 32px !important;
}

.gap-2 { gap: 8px; }
.font-mono { font-family: monospace; }
</style>