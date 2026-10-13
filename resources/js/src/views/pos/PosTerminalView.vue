<template>
  <div class="pos-terminal-view h-100">
    <v-row class="fill-height ma-0">
      <!-- Columna Izquierda: Catálogo y Búsqueda Rápida -->
      <v-col cols="12" lg="7" xl="8" class="d-flex flex-column pa-3 pa-sm-4">
        <!-- Barra de herramientas: Búsqueda y Tasa -->
        <div class="d-flex flex-column flex-sm-row align-center justify-space-between gap-3 mb-4">
          <div class="d-flex align-center gap-2 w-100" style="max-width: 460px;">
            <v-text-field
              ref="barcodeInputRef"
              v-model="searchQuery"
              placeholder="Escanear SKU o buscar por nombre..."
              prepend-inner-icon="mdi-barcode-scan"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              class="w-100"
              @keydown.enter.prevent="onBarcodeScanEnter"
              @update:model-value="onSearchInput"
              @click:clear="onSearchClear"
            />
          </div>

          <v-chip color="primary" variant="tonal" size="small" class="font-mono font-weight-bold">
            <v-icon start size="16">mdi-currency-usd</v-icon>
            Tasa: {{ exchangeRate.toFixed(2) }} VES
          </v-chip>
        </div>

        <!-- Filtro Rápido de Categorías en Chips -->
        <div class="d-flex align-center gap-2 overflow-x-auto pb-2 mb-3">
          <v-chip
            :color="selectedCategory === null ? 'primary' : 'surface-variant'"
            :variant="selectedCategory === null ? 'flat' : 'tonal'"
            size="small"
            class="cursor-pointer"
            @click="filterByCategory(null)"
          >
            Todos
          </v-chip>
          <v-chip
            v-for="category in categories"
            :key="category.id"
            :color="selectedCategory === category.id ? 'primary' : 'surface-variant'"
            :variant="selectedCategory === category.id ? 'flat' : 'tonal'"
            size="small"
            class="cursor-pointer"
            @click="filterByCategory(category.id)"
          >
            {{ category.name }}
          </v-chip>
        </div>

        <!-- Grid de Tarjetas de Productos -->
        <div v-if="isLoadingProducts" class="flex-grow-1 overflow-y-auto">
          <v-row dense>
            <v-col v-for="n in 8" :key="n" cols="12" sm="6" md="4" xl="3">
              <v-skeleton-loader type="card" height="130" class="border rounded-lg" />
            </v-col>
          </v-row>
        </div>

        <div v-else-if="products.length === 0" class="flex-grow-1 d-flex flex-column align-center justify-center pa-8 text-center border rounded-lg bg-surface-light">
          <v-icon size="48" color="medium-emphasis" class="mb-2">mdi-package-variant-closed-remove</v-icon>
          <span class="text-subtitle-2 font-weight-bold">No hay productos disponibles</span>
          <span class="text-caption text-medium-emphasis">Verifique el filtro aplicado o el stock en almacén.</span>
        </div>

        <div v-else class="flex-grow-1 overflow-y-auto pr-1">
          <v-row dense>
            <v-col
              v-for="product in products"
              :key="product.id"
              cols="12"
              sm="6"
              md="4"
              xl="3"
            >
              <v-card
                elevation="0"
                border
                rounded="lg"
                class="pa-3 product-card h-100 d-flex flex-column justify-space-between cursor-pointer"
                :class="{ 'opacity-50': Number(product.quantity) <= 0 }"
                @click="addToCart(product)"
              >
                <div>
                  <div class="d-flex justify-space-between align-start gap-1">
                    <span class="text-caption font-mono text-medium-emphasis">{{ product.sku }}</span>
                    <v-chip
                      size="x-small"
                      :color="Number(product.quantity) <= Number(product.min_stock) ? 'error' : 'surface-variant'"
                      variant="tonal"
                    >
                      Stock: {{ product.quantity }}
                    </v-chip>
                  </div>
                  <div class="text-body-2 font-weight-bold text-truncate mt-1" :title="product.name">
                    {{ product.name }}
                  </div>
                </div>

                <div class="mt-3 pt-2 border-t d-flex justify-space-between align-end">
                  <div>
                    <div class="text-caption text-medium-emphasis">
                      Bs. {{ (Number(product.selling_price) * exchangeRate).toFixed(2) }}
                    </div>
                    <div class="text-subtitle-2 font-weight-bold text-primary">
                      ${{ Number(product.selling_price).toFixed(2) }}
                    </div>
                  </div>
                  <v-btn
                    icon="mdi-plus"
                    size="x-small"
                    color="primary"
                    variant="tonal"
                    :disabled="Number(product.quantity) <= 0"
                  />
                </div>
              </v-card>
            </v-col>
          </v-row>
        </div>
      </v-col>

      <!-- Columna Derecha: Cesta de Compra y Cobro -->
      <v-col cols="12" lg="5" xl="4" class="pa-3 pa-sm-4 border-s bg-surface">
        <v-card elevation="0" border rounded="lg" class="h-100 d-flex flex-column">
          <!-- Cabecera de la Cesta y Selector de Cliente -->
          <div class="pa-4 border-b">
            <div class="d-flex justify-space-between align-center mb-3">
              <div class="d-flex align-center gap-2">
                <v-icon color="primary">mdi-cart-outline</v-icon>
                <span class="text-subtitle-1 font-weight-bold">Orden en Curso</span>
              </div>
              <v-btn
                v-if="cart.length > 0"
                variant="text"
                color="error"
                size="small"
                prepend-icon="mdi-trash-can-outline"
                @click="clearCart"
              >
                Vaciar
              </v-btn>
            </div>

            <v-autocomplete
              v-model="selectedCustomerId"
              :items="customers"
              item-value="id"
              :item-title="(c: any) => `${c.name} (${c.rif_ci})`"
              placeholder="Asignar Cliente (Opcional)"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              prepend-inner-icon="mdi-account-outline"
            />
          </div>

          <!-- Renglones de la Cesta -->
          <div class="flex-grow-1 overflow-y-auto pa-3">
            <div v-if="cart.length === 0" class="h-100 d-flex flex-column align-center justify-center text-center pa-6">
              <v-icon size="40" color="medium-emphasis" class="mb-2">mdi-cart-arrow-down</v-icon>
              <span class="text-body-2 font-weight-medium">La cesta está vacía</span>
              <span class="text-caption text-medium-emphasis">Haga clic sobre un producto o escanee un código de barras.</span>
            </div>

            <div v-else class="d-flex flex-column gap-2">
              <div
                v-for="item in cart"
                :key="item.product.id"
                class="pa-3 border rounded-lg d-flex align-center justify-space-between gap-2 bg-surface-light"
              >
                <div class="flex-grow-1 overflow-hidden">
                  <div class="text-body-2 font-weight-medium text-truncate">{{ item.product.name }}</div>
                  <div class="text-caption text-medium-emphasis">
                    ${{ item.price.toFixed(2) }} c/u &bull; Bs. {{ (item.price * exchangeRate).toFixed(2) }}
                  </div>
                </div>

                <!-- Control de Cantidad -->
                <div class="d-flex align-center gap-1">
                  <v-btn
                    icon="mdi-minus"
                    size="x-small"
                    variant="tonal"
                    @click="updateItemQuantity(item.product.id, item.quantity - 1)"
                  />
                  <span class="font-weight-bold px-2 text-body-2">{{ item.quantity }}</span>
                  <v-btn
                    icon="mdi-plus"
                    size="x-small"
                    variant="tonal"
                    :disabled="item.quantity >= Number(item.product.quantity)"
                    @click="updateItemQuantity(item.product.id, item.quantity + 1)"
                  />
                </div>

                <!-- Subtotal y Quitar -->
                <div class="text-right" style="min-width: 70px;">
                  <div class="text-body-2 font-weight-bold">
                    ${{ (item.price * item.quantity).toFixed(2) }}
                  </div>
                </div>

                <v-btn
                  icon="mdi-close"
                  size="x-small"
                  variant="text"
                  color="medium-emphasis"
                  @click="removeFromCart(item.product.id)"
                />
              </div>
            </div>
          </div>

          <!-- Métodos de Pago y Descuento -->
          <div class="pa-4 border-t bg-surface-light">
            <!-- Descuento y Notas -->
            <v-row dense class="mb-2">
              <v-col cols="6">
                <v-text-field
                  v-model.number="totalDiscount"
                  label="Descuento ($)"
                  type="number"
                  min="0"
                  :max="subtotal"
                  step="0.1"
                  variant="outlined"
                  density="compact"
                  hide-details
                  prefix="$"
                  @update:model-value="validateDiscount"
                />
              </v-col>
              <v-col cols="6">
                <v-btn-toggle
                  v-model="paymentMethod"
                  mandatory
                  color="primary"
                  density="compact"
                  class="w-100"
                >
                  <v-btn value="cash" size="small" class="flex-grow-1">Efectivo</v-btn>
                  <v-btn value="card" size="small" class="flex-grow-1">Tarjeta</v-btn>
                  <v-btn value="credit" size="small" class="flex-grow-1">Crédito</v-btn>
                </v-btn-toggle>
              </v-col>
            </v-row>

            <!-- Campo Auxiliar para Venta a Crédito: Inicial -->
            <div v-if="paymentMethod === 'credit'" class="mb-3 pa-3 border rounded-lg bg-surface">
              <div class="d-flex justify-space-between align-center mb-2">
                <span class="text-caption font-weight-bold">Abono Inicial de Crédito</span>
                <span class="text-caption text-medium-emphasis">Saldo: ${{ remainingBalance.toFixed(2) }}</span>
              </div>
              <v-text-field
                v-model.number="initialPayment"
                placeholder="0.00"
                type="number"
                min="0"
                :max="total"
                step="0.5"
                variant="outlined"
                density="compact"
                hide-details
                prefix="$"
                @update:model-value="validateInitialPayment"
              />
            </div>

            <!-- Resumen Financiero Dual -->
            <div class="d-flex justify-space-between align-center text-caption text-medium-emphasis mb-1">
              <span>Subtotal:</span>
              <span>${{ subtotal.toFixed(2) }}</span>
            </div>
            <div v-if="totalDiscount > 0" class="d-flex justify-space-between align-center text-caption text-success mb-1">
              <span>Descuento aplicado:</span>
              <span>-${{ totalDiscount.toFixed(2) }}</span>
            </div>
            <div class="d-flex justify-space-between align-baseline my-2">
              <span class="text-subtitle-1 font-weight-bold">Total a Cobrar:</span>
              <div class="text-right">
                <div class="text-h6 font-weight-bold text-primary">
                  ${{ total.toFixed(2) }}
                </div>
                <div class="text-caption font-weight-medium text-medium-emphasis">
                  Bs. {{ totalVes.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                </div>
              </div>
            </div>

            <!-- Botón Principal de Procesamiento -->
            <v-btn
              color="primary"
              size="large"
              block
              prepend-icon="mdi-cash-check"
              :loading="isProcessing"
              :disabled="cart.length === 0"
              class="mt-2"
              @click="submitSale"
            >
              Completar Venta
            </v-btn>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue';
import catalogService from '@/api/catalog.api';
import partiesService from '@/api/parties.api';
import settingsService from '@/api/settings.api';
import salesService from '@/api/sale.api';
import { useNotificationStore } from '@/stores/notification';
import type { Product, Category } from '@/types/catalog';
import type { Customer } from '@/types/parties';
import type { PaymentMethod, ProcessSalePayload } from '@/types/sales';

interface CartItem {
  product: Product;
  quantity: number;
  price: number;
}

const notificationStore = useNotificationStore();
const barcodeInputRef = ref<HTMLInputElement | null>(null);

// Catálogos maestros
const products = ref<Product[]>([]);
const categories = ref<Category[]>([]);
const customers = ref<Customer[]>([]);
const exchangeRate = ref(1.0);

// Filtros y estados del catálogo
const isLoadingProducts = ref(false);
const searchQuery = ref('');
const selectedCategory = ref<string | null>(null);
let searchDebounceTimeout: ReturnType<typeof setTimeout> | null = null;

// Estado reactivo de la venta / POS
const cart = ref<CartItem[]>([]);
const selectedCustomerId = ref<string | null>(null);
const paymentMethod = ref<PaymentMethod>('cash');
const totalDiscount = ref(0);
const initialPayment = ref(0);
const isProcessing = ref(false);

// Cálculos financieros derivados
const subtotal = computed(() => {
  return cart.value.reduce((acc, item) => acc + item.price * item.quantity, 0);
});

const total = computed(() => {
  return Math.max(0, subtotal.value - totalDiscount.value);
});

const totalVes = computed(() => {
  return total.value * exchangeRate.value;
});

const remainingBalance = computed(() => {
  return Math.max(0, total.value - initialPayment.value);
});

function validateDiscount() {
  if (totalDiscount.value < 0) totalDiscount.value = 0;
  if (totalDiscount.value > subtotal.value) totalDiscount.value = subtotal.value;
}

function validateInitialPayment() {
  if (initialPayment.value < 0) initialPayment.value = 0;
  if (initialPayment.value > total.value) initialPayment.value = total.value;
}

// Carga inicial de datos maestros
async function loadInitialData() {
  try {
    const [fetchedCategories, fetchedCustomers, fetchedSettings] = await Promise.all([
      catalogService.getAllCategories(),
      partiesService.getAllCustomers(),
      settingsService.getSettings(),
    ]);

    categories.value = fetchedCategories;
    customers.value = fetchedCustomers;

    const rate = Number(fetchedSettings['exchange_rate'] || fetchedSettings['bcv_rate'] || 1.0);
    exchangeRate.value = rate > 0 ? rate : 1.0;
  } catch {
    notificationStore.warning('Advertencia al cargar la configuración general del POS.');
  }
}

async function loadProducts() {
  isLoadingProducts.value = true;
  try {
    const response = await catalogService.getProducts({
      page: 1,
      per_page: 24,
      search: searchQuery.value || undefined,
      category_id: selectedCategory.value || undefined,
      sort_field: 'name',
      sort_direction: 'asc',
    });

    products.value = response.data;
  } catch (error: any) {
    notificationStore.warning(error?.response?.data?.message || 'Error al cargar catálogo.');
  } finally {
    isLoadingProducts.value = false;
  }
}

function filterByCategory(categoryId: string | null) {
  selectedCategory.value = categoryId;
  loadProducts();
}

function onSearchInput() {
  if (searchDebounceTimeout) clearTimeout(searchDebounceTimeout);
  searchDebounceTimeout = setTimeout(() => {
    loadProducts();
  }, 300);
}

function onSearchClear() {
  searchQuery.value = '';
  loadProducts();
}

// Manejo del Lector de Código de Barras por Teclado
async function onBarcodeScanEnter() {
  const code = searchQuery.value.trim();
  if (!code) return;

  const foundLocal = products.value.find(p => p.sku.toLowerCase() === code.toLowerCase());

  if (foundLocal) {
    addToCart(foundLocal);
    searchQuery.value = '';
    return;
  }

  // Si no está en la página actual del grid, verificar en el backend
  try {
    const result = await catalogService.lookupBarcode(code);
    if (result.exists_locally && result.data?.id) {
      const fullProduct = await catalogService.getProductById(result.data.id);
      addToCart(fullProduct);
      searchQuery.value = '';
    } else {
      notificationStore.warning(`Código ${code} no encontrado en inventario.`);
    }
  } catch {
    notificationStore.warning('Error al validar código de barras.');
  }
}

// Operaciones de la Cesta
function addToCart(product: Product) {
  if (Number(product.quantity) <= 0) {
    notificationStore.warning(`El producto '${product.name}' no tiene existencias disponibles.`);
    return;
  }

  const existing = cart.value.find(i => i.product.id === product.id);

  if (existing) {
    if (existing.quantity >= Number(product.quantity)) {
      notificationStore.warning(`Máximo stock disponible alcanzado (${product.quantity} unidades).`);
      return;
    }
    existing.quantity += 1;
  } else {
    cart.value.push({
      product,
      quantity: 1,
      price: Number(product.selling_price),
    });
  }

  validateDiscount();
}

function updateItemQuantity(productId: string, newQuantity: number) {
  const index = cart.value.findIndex(i => i.product.id === productId);
  if (index === -1) return;

  if (newQuantity <= 0) {
    cart.value.splice(index, 1);
  } else {
    const available = Number(cart.value[index].product.quantity);
    if (newQuantity > available) {
      notificationStore.warning(`Solo quedan ${available} unidades en inventario.`);
      return;
    }
    cart.value[index].quantity = newQuantity;
  }

  validateDiscount();
}

function removeFromCart(productId: string) {
  cart.value = cart.value.filter(i => i.product.id !== productId);
  validateDiscount();
}

function clearCart() {
  cart.value = [];
  totalDiscount.value = 0;
  initialPayment.value = 0;
}

// Procesar Venta Definitiva
async function submitSale() {
  if (cart.value.length === 0) {
    notificationStore.warning('Agregue al menos un producto a la orden.');
    return;
  }

  if (paymentMethod.value === 'credit' && !selectedCustomerId.value) {
    notificationStore.error('Las ventas a crédito deben asociarse obligatoriamente a un cliente.');
    return;
  }

  isProcessing.value = true;
  try {
    const payload: ProcessSalePayload = {
      items: cart.value.map(item => ({
        product_id: item.product.id,
        quantity: item.quantity,
        price: item.price,
      })),
      customer_id: selectedCustomerId.value || null,
      payment_method: paymentMethod.value,
      total_discount: totalDiscount.value,
      exchange_rate: exchangeRate.value,
      initial_payment: paymentMethod.value === 'credit' ? initialPayment.value : undefined,
    };

    const sale = await salesService.processSale(payload);

    notificationStore.success(`¡Venta #${sale.invoice_number} procesada con éxito!`);
    clearCart();
    selectedCustomerId.value = null;
    paymentMethod.value = 'cash';

    // Refrescar el stock del catálogo
    await loadProducts();

    nextTick(() => {
      barcodeInputRef.value?.focus();
    });
  } catch (error: any) {
    notificationStore.error(
      error?.response?.data?.message || 'Error al completar la venta.',
    );
  } finally {
    isProcessing.value = false;
  }
}

onMounted(async () => {
  await Promise.all([loadInitialData(), loadProducts()]);
  nextTick(() => {
    barcodeInputRef.value?.focus();
  });
});
</script>

<style scoped>
.pos-terminal-view {
  min-height: calc(100vh - 64px);
}
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.font-mono { font-family: monospace; }
.product-card {
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.cursor-pointer {
  cursor: pointer;
}
</style>