<!-- resources/js/src/views/pos/components/PosMobileDock.vue -->
<template>
  <div class="pos-mobile-dock px-3 py-2 border-t bg-surface d-flex align-center justify-space-between gap-2">
    <!-- Izquierda: Carrito y Totales Duales (Área táctil fluida) -->
    <div
      v-ripple
      class="totals-trigger d-flex align-center gap-2 cursor-pointer rounded-lg flex-grow-1 overflow-hidden pe-1"
      @click="emit('open-cart')"
    >
      <!-- Avatar con Badge del Carrito -->
      <v-badge
        :content="posStore.totalUnits"
        :model-value="posStore.totalUnits > 0"
        color="primary"
        location="top end"
        offset-x="2"
        offset-y="2"
        class="cart-badge font-mono font-weight-bold flex-shrink-0"
      >
        <v-avatar color="surface-variant" size="36">
          <v-icon size="18" color="primary">mdi-cart-outline</v-icon>
        </v-avatar>
      </v-badge>

      <!-- Bloque de Montos (con nowrap para evitar quiebres) -->
      <div class="amounts-block d-flex flex-column overflow-hidden">
        <!-- Bolívares referencial -->
        <div class="text-caption text-medium-emphasis font-mono text-truncate leading-none">
          Bs. {{ formattedVes }}
        </div>
        <!-- USD Principal destacado -->
        <div class="text-subtitle-1 font-weight-bold text-primary font-mono text-truncate leading-tight mt-1">
          ${{ formattedUsd }}
        </div>
      </div>
    </div>

    <!-- Derecha: Botón Primario de Cobro (Blindado contra encogimiento) -->
    <v-btn
      color="primary"
      variant="flat"
      size="default"
      class="dock-checkout-btn font-weight-bold flex-shrink-0 px-3"
      :disabled="posStore.cart.length === 0"
      @click="emit('open-cart')"
    >
      <v-icon start size="18" class="me-1">mdi-cash-register</v-icon>
      Cobrar
      <span v-if="posStore.totalUnits > 0" class="ms-1 font-mono">
        ({{ posStore.totalUnits }})
      </span>
    </v-btn>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePosStore } from '@/stores/pos';

const emit = defineEmits<{
  (e: 'open-cart'): void;
}>();

const posStore = usePosStore();

// Formato con separadores de miles para USD
const formattedUsd = computed(() => {
  return posStore.total.toLocaleString('es-VE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
});

// Formato con separadores de miles para VES
const formattedVes = computed(() => {
  return posStore.totalVes.toLocaleString('es-VE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
});
</script>

<style scoped>
.pos-mobile-dock {
  height: 64px;
  min-height: 64px;
  width: 100%;
  box-sizing: border-box;
  user-select: none;
  box-shadow: 0 -3px 12px rgba(0, 0, 0, 0.12);
  border-top-color: rgba(255, 255, 255, 0.08) !important;
}

.totals-trigger {
  min-width: 0; /* Permite que el texto se trunque limpiamente si la cifra es enorme */
  height: 100%;
}

.amounts-block {
  min-width: 0;
  white-space: nowrap; /* Regla clave: jamás romper 'Bs.' a otra línea */
}

.amounts-block > div {
  font-variant-numeric: tabular-nums;
}

.dock-checkout-btn {
  height: 44px !important;
  font-size: 0.88rem !important;
  letter-spacing: 0.02em;
}

.leading-none {
  line-height: 1;
}

.leading-tight {
  line-height: 1.15;
}

.font-mono {
  font-family: monospace;
}

.gap-2 {
  gap: 8px;
}

.cursor-pointer {
  cursor: pointer;
}
</style>