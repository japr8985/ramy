<!-- resources/js/src/views/pos/components/PosCartContent.vue -->
<template>
  <div class="pos-cart-content d-flex flex-column h-100 overflow-hidden bg-surface">
    <!-- ======================================================== -->
    <!-- 1. CABECERA: TÍTULO Y ASIGNACIÓN DE CLIENTE              -->
    <!-- ======================================================== -->
    <div class="cart-header px-4 pt-3 pb-2 flex-shrink-0">
      <!-- Fila de título y acciones -->
      <div class="d-flex justify-space-between align-center mb-2">
        <div class="d-flex align-center gap-2">
          <span class="text-h6 font-weight-bold">Ticket de Venta</span>
          <v-chip
            v-if="posStore.totalUnits > 0"
            size="x-small"
            color="primary"
            variant="tonal"
            class="font-mono font-weight-bold px-2"
          >
            {{ posStore.totalUnits }} und
          </v-chip>
        </div>

        <div class="d-flex align-center gap-1">
          <v-btn
            v-if="posStore.cart.length > 0"
            icon="mdi-trash-can-outline"
            variant="text"
            color="error"
            size="small"
            density="comfortable"
            title="Vaciar ticket"
            @click="posStore.clearCart()"
          />
          <v-btn
            v-if="isMobileSheet"
            icon="mdi-close"
            variant="text"
            size="small"
            density="comfortable"
            @click="emit('close')"
          />
        </div>
      </div>

      <!-- Barra de Cliente tipo cápsula -->
      <div
        v-ripple
        class="customer-pill d-flex align-center justify-space-between px-3 py-2 rounded-xl cursor-pointer"
        @click="isCustomerDialogOpen = true"
      >
        <div class="d-flex align-center gap-2 overflow-hidden pe-2">
          <v-avatar
            :color="posStore.selectedCustomerId ? 'primary' : 'surface-variant'"
            size="26"
            class="flex-shrink-0"
          >
            <v-icon size="15" :color="posStore.selectedCustomerId ? 'white' : 'primary'">
              mdi-account
            </v-icon>
          </v-avatar>
          <div class="overflow-hidden">
            <span class="text-body-2 font-weight-medium text-truncate d-block">
              {{ currentCustomerName }}
            </span>
          </div>
        </div>

        <span class="text-caption text-primary font-weight-bold flex-shrink-0">
          {{ posStore.selectedCustomerId ? 'Cambiar' : '+ Asignar' }}
        </span>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- 2. LISTA DE RENGLONES CON ESTRUCTURA ERGONÓMICA DE 2 FILAS-->
    <!-- ======================================================== -->
    <div class="cart-items-body flex-grow-1 overflow-y-auto px-3 py-1">
      <!-- Estado vacío -->
      <div
        v-if="posStore.cart.length === 0"
        class="h-100 d-flex flex-column align-center justify-center text-center py-8 text-medium-emphasis"
      >
        <v-avatar color="surface-variant" size="48" class="mb-2 opacity-60">
          <v-icon size="24">mdi-cart-outline</v-icon>
        </v-avatar>
        <span class="text-body-2 font-weight-medium">No hay artículos en la orden</span>
        <span class="text-caption text-disabled mt-1">Selecciona productos para comenzar.</span>
      </div>

      <!-- Lista con distribución vertical interna -->
      <div v-else class="d-flex flex-column gap-2 py-1">
        <template v-for="(item, index) in posStore.cart" :key="item.product.id">
          <div class="cart-product-row px-2 py-2 rounded-lg">
            <!-- Sub-fila 1: Nombre completo + Botón Eliminar -->
            <div class="d-flex align-start justify-space-between gap-2 mb-2">
              <span class="text-body-2 font-weight-bold text-high-emphasis product-title leading-snug">
                {{ item.product.name }}
              </span>

              <v-btn
                icon="mdi-close"
                size="x-small"
                variant="text"
                density="comfortable"
                color="medium-emphasis"
                class="delete-btn flex-shrink-0"
                @click="posStore.removeFromCart(item.product.id)"
              />
            </div>

            <!-- Sub-fila 2: Precio unitario | Stepper | Subtotal -->
            <div class="d-flex align-center justify-space-between gap-2">
              <!-- Precio unitario referencial -->
              <div class="text-caption text-medium-emphasis font-mono overflow-hidden text-truncate pe-1">
                ${{ formatNumber(item.price) }}
              </div>

              <div class="d-flex align-center gap-2 flex-shrink-0">
                <!-- Stepper táctil -->
                <div class="stepper-capsule d-flex align-center rounded-pill px-1">
                  <v-btn
                    icon="mdi-minus"
                    size="x-small"
                    variant="text"
                    density="comfortable"
                    class="stepper-btn"
                    @click="posStore.updateItemQuantity(item.product.id, item.quantity - 1)"
                  />
                  <span class="font-mono font-weight-bold text-caption px-1 stepper-count text-center">
                    {{ item.quantity }}
                  </span>
                  <v-btn
                    icon="mdi-plus"
                    size="x-small"
                    variant="text"
                    density="comfortable"
                    class="stepper-btn"
                    :disabled="item.quantity >= Number(item.product.quantity)"
                    @click="posStore.updateItemQuantity(item.product.id, item.quantity + 1)"
                  />
                </div>

                <!-- Subtotal de renglón -->
                <div class="subtotal-block text-right font-mono">
                  <span class="text-body-2 font-weight-bold text-high-emphasis">
                    ${{ formatNumber(item.price * item.quantity) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Separador sutil -->
          <v-divider v-if="index < posStore.cart.length - 1" class="border-opacity-20" />
        </template>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- 3. PIE DE PAGO Y CIERRE (CON SEPARADORES DE MILES)       -->
    <!-- ======================================================== -->
    <div class="cart-footer px-4 pt-3 pb-4 bg-surface border-t flex-shrink-0">
      <!-- Selector de Método de Pago -->
      <v-btn-toggle
        v-model="posStore.paymentMethod"
        mandatory
        color="primary"
        density="comfortable"
        rounded="xl"
        class="w-100 payment-toggle mb-3"
      >
        <v-btn value="cash" class="flex-grow-1 text-caption font-weight-bold">Efectivo</v-btn>
        <v-btn value="card" class="flex-grow-1 text-caption font-weight-bold">Tarjeta</v-btn>
        <v-btn value="credit" class="flex-grow-1 text-caption font-weight-bold">Crédito</v-btn>
      </v-btn-toggle>

      <!-- Fila de Descuento -->
      <div class="d-flex align-center justify-space-between gap-3 mb-2">
        <span class="text-caption text-medium-emphasis">Aplicar descuento</span>
        <div style="width: 120px;">
          <v-text-field
            v-model.number="posStore.totalDiscount"
            placeholder="0.00"
            type="number"
            min="0"
            :max="posStore.subtotal"
            step="0.5"
            variant="outlined"
            density="compact"
            hide-details
            prefix="$"
            class="discount-input"
            @update:model-value="posStore.validateDiscount"
          />
        </div>
      </div>

      <!-- Abono Inicial (solo si es crédito) -->
      <div
        v-if="posStore.paymentMethod === 'credit'"
        class="mb-3 pa-3 rounded-xl bg-surface-variant d-flex flex-column gap-2"
      >
        <div class="d-flex justify-space-between align-center text-caption">
          <span class="font-weight-bold">Abono Inicial</span>
          <span class="font-mono text-medium-emphasis">Resta: ${{ formatNumber(posStore.remainingBalance) }}</span>
        </div>
        <v-text-field
          v-model.number="posStore.initialPayment"
          placeholder="0.00"
          type="number"
          min="0"
          :max="posStore.total"
          step="0.5"
          variant="outlined"
          density="compact"
          hide-details
          prefix="$"
          @update:model-value="posStore.validateInitialPayment"
        />
        <span v-if="!posStore.selectedCustomerId" class="text-caption text-error font-weight-bold">
          * Selecciona un cliente arriba para autorizar crédito.
        </span>
      </div>

      <v-divider class="my-2 opacity-20" />

      <!-- Resumen Numérico -->
      <div class="d-flex justify-space-between align-center text-caption text-medium-emphasis mb-1">
        <span>Subtotal:</span>
        <span class="font-mono">${{ formatNumber(posStore.subtotal) }}</span>
      </div>

      <div
        v-if="posStore.totalDiscount > 0"
        class="d-flex justify-space-between align-center text-caption text-success mb-1"
      >
        <span>Descuento:</span>
        <span class="font-mono">-${{ formatNumber(posStore.totalDiscount) }}</span>
      </div>

      <!-- Total Principal Dual -->
      <div class="d-flex justify-space-between align-end mt-2 mb-3">
        <div>
          <span class="text-caption text-disabled text-uppercase tracking-wider font-weight-bold d-block">
            TOTAL A COBRAR
          </span>
          <span class="text-caption text-medium-emphasis font-mono">
            Bs. {{ formattedTotalVes }}
          </span>
        </div>
        <div class="text-right">
          <span class="text-h5 font-weight-bold text-primary font-mono leading-none">
            ${{ formatNumber(posStore.total) }}
          </span>
        </div>
      </div>

      <!-- Botón de Cobro Primario -->
      <v-btn
        color="primary"
        size="large"
        rounded="xl"
        block
        prepend-icon="mdi-cash-check"
        :loading="posStore.isProcessing"
        :disabled="!posStore.canCheckout"
        class="checkout-btn font-weight-bold elevation-2"
        @click="handleCheckout"
      >
        {{ posStore.paymentMethod === 'credit' ? 'Registrar Crédito' : 'Cobrar Orden' }}
      </v-btn>
    </div>

    <!-- Diálogo interno de selección de cliente -->
    <PosCustomerDialog
      v-model="isCustomerDialogOpen"
      :customers="customers"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePosStore } from '@/stores/pos';
import type { Customer } from '@/types/parties';
import PosCustomerDialog from './PosCustomerDialog.vue';

const props = withDefaults(
  defineProps<{
    customers: Customer[];
    isMobileSheet?: boolean;
  }>(),
  {
    isMobileSheet: false,
  }
);

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'sale-completed'): void;
}>();

const posStore = usePosStore();
const isCustomerDialogOpen = ref(false);

const activeCustomer = computed(() => {
  if (!posStore.selectedCustomerId) return null;
  return props.customers.find(c => c.id === posStore.selectedCustomerId) ?? null;
});

const currentCustomerName = computed(() => {
  return activeCustomer.value?.name ?? 'Consumidor Final';
});

// Función de formateo con separador de miles
function formatNumber(val: number): string {
  return (val || 0).toLocaleString('es-VE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

const formattedTotalVes = computed(() => formatNumber(posStore.totalVes));

async function handleCheckout() {
  const sale = await posStore.submitSale();
  if (sale) {
    emit('sale-completed');
  }
}
</script>

<style scoped>
.pos-cart-content {
  min-height: 0;
}

.customer-pill {
  background-color: rgba(255, 255, 255, 0.04);
  transition: background-color 0.15s ease;
}
.customer-pill:active {
  background-color: rgba(var(--v-theme-primary), 0.12);
}

.cart-product-row {
  background-color: rgba(255, 255, 255, 0.02);
  transition: background-color 0.15s ease;
}

.product-title {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  word-break: break-word;
}

.stepper-capsule {
  background-color: rgba(255, 255, 255, 0.06);
}

.stepper-btn {
  width: 26px !important;
  height: 26px !important;
}

.stepper-count {
  min-width: 22px;
}

.subtotal-block {
  min-width: 70px;
}

.delete-btn {
  width: 24px !important;
  height: 24px !important;
}

.payment-toggle {
  height: 42px !important;
  background-color: rgba(255, 255, 255, 0.04);
}

.discount-input :deep(.v-field__input) {
  text-align: right;
  font-family: monospace;
}

.checkout-btn {
  height: 50px !important;
  letter-spacing: 0.02em;
}

.cart-footer {
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  padding-bottom: max(16px, env(safe-area-inset-bottom, 0px)) !important;
}

.leading-snug { line-height: 1.25; }
.leading-none { line-height: 1; }
.font-mono { font-family: monospace; }
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.cursor-pointer { cursor: pointer; }
.tracking-wider { letter-spacing: 0.05em; }
</style>